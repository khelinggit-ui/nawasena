<?php

namespace App\Http\Controllers;

use App\Models\WhatsappInstance;
use App\Services\EvolutionApiService;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Throwable;

class WhatsAppController extends Controller
{
    protected EvolutionApiService $evolutionService;

    public function __construct(EvolutionApiService $evolutionService)
    {
        $this->evolutionService = $evolutionService;
    }

    public function index()
    {
        $instances = WhatsappInstance::latest()->get();

        $this->syncInstancesStatus($instances);

        return view('whatsapp.index', compact('instances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'instance_name' => ['required', 'alpha_dash', 'unique:whatsapp_instances,instance_name'],
            'phone_number' => ['nullable', 'string', 'max:30'],
        ]);

        $name = $request->instance_name;

        try {
            $response = $this->evolutionService->createInstance($name);
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Tidak dapat terhubung ke server Evolution API: '.$e->getMessage());
        }

        if ($response->successful() || $response->status() === 403) {
            WhatsappInstance::firstOrCreate(
                ['instance_name' => $name],
                ['phone_number' => $request->phone_number]
            );

            return redirect()
                ->route('whatsapp.show', $name)
                ->with('success', 'Instance WhatsApp berhasil dibuat. Silakan scan QR code.');
        }

        $message = $this->evolutionFailureMessage($response);

        return back()->withInput()->with('error', 'Gagal membuat instance pada server Evolution API. '.$message);
    }

    public function show(string $instanceName)
    {
        $instance = WhatsappInstance::where('instance_name', $instanceName)->firstOrFail();

        $qrCode = null;
        $error = null;
        $stateSynced = false;

        try {
            $stateResponse = $this->evolutionService->getConnectionState($instanceName);
            $state = data_get($stateResponse->json(), 'instance.state') ?? $stateResponse->json('state');

            if ($stateResponse->successful() && $state !== null) {
                $instance->update(['status' => $this->mapState($state)]);
                $stateSynced = true;
            }
        } catch (Throwable) {
            // Abaikan, QR code tetap dicoba diambil di bawah.
        }

        if ($instance->status !== 'connected' || ! $stateSynced) {
            try {
                $response = $this->evolutionService->connectInstance($instanceName);

                if ($response->successful()) {
                    $qrCode = $response->json('base64') ?? $response->json('qrcode.base64');

                    if ($qrCode && ! str_starts_with($qrCode, 'data:')) {
                        $qrCode = 'data:image/png;base64,'.$qrCode;
                    }
                } elseif (! $error) {
                    $error = $response->json('message') ?? 'QR code tidak tersedia dari server Evolution API.';
                }
            } catch (Throwable $e) {
                $error = 'Tidak dapat terhubung ke server Evolution API: '.$e->getMessage();
            }
        }

        return view('whatsapp.connect', compact('instance', 'qrCode', 'error'));
    }

    public function disconnect(string $instanceName)
    {
        $instance = WhatsappInstance::where('instance_name', $instanceName)->firstOrFail();

        try {
            $response = $this->evolutionService->logoutInstance($instanceName);
        } catch (Throwable $e) {
            return back()->with('error', 'Tidak dapat terhubung ke server Evolution API: '.$e->getMessage());
        }

        if ($response->successful()) {
            $instance->update(['status' => 'disconnected']);

            return back()->with('success', 'Instance '.$instanceName.' berhasil diputuskan dari WhatsApp.');
        }

        $message = $this->evolutionFailureMessage($response);

        return back()->with('error', 'Gagal memutuskan koneksi: '.$message);
    }

    public function destroy(string $instanceName)
    {
        $instance = WhatsappInstance::where('instance_name', $instanceName)->firstOrFail();

        try {
            $response = $this->evolutionService->deleteInstance($instanceName);
        } catch (Throwable $e) {
            return back()->with('error', 'Tidak dapat terhubung ke server Evolution API: '.$e->getMessage());
        }

        if ($response->successful() || $response->status() === 404) {
            $instance->delete();

            return redirect()
                ->route('whatsapp.index')
                ->with('success', 'Instance '.$instanceName.' berhasil dihapus.');
        }

        $message = $this->evolutionFailureMessage($response);

        return back()->with('error', 'Gagal menghapus instance: '.$message);
    }

    public function settings(string $instanceName)
    {
        $instance = WhatsappInstance::where('instance_name', $instanceName)->firstOrFail();
        $webhook = null;
        $error = null;

        try {
            $response = $this->evolutionService->findWebhook($instanceName);

            if ($response->successful()) {
                $webhook = $response->json();
            } else {
                $error = $response->json('message') ?? 'Pengaturan webhook tidak dapat diambil dari server Evolution API.';
            }
        } catch (Throwable $e) {
            $error = 'Tidak dapat terhubung ke server Evolution API: '.$e->getMessage();
        }

        if (! is_array($webhook)) {
            $webhook = null;
        }

        $events = $this->webhookEvents();

        return view('whatsapp.settings', compact('instance', 'webhook', 'error', 'events'));
    }

    public function updateSettings(Request $request, string $instanceName)
    {
        $instance = WhatsappInstance::where('instance_name', $instanceName)->firstOrFail();

        $validated = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'url' => ['required', 'url', 'max:2048'],
            'base64' => ['nullable', 'boolean'],
            'events' => ['nullable', 'array'],
            'events.*' => ['string', 'in:'.implode(',', array_keys($this->webhookEvents()))],
        ]);

        $webhook = [
            'enabled' => (bool) ($validated['enabled'] ?? false),
            'url' => $validated['url'],
            'byEvents' => false,
            'base64' => (bool) ($validated['base64'] ?? false),
            'events' => array_values($validated['events'] ?? []),
        ];

        try {
            $response = $this->evolutionService->setWebhook($instanceName, $webhook);
        } catch (Throwable $e) {
            return back()->withInput()->with('error', 'Tidak dapat terhubung ke server Evolution API: '.$e->getMessage());
        }

        if ($response->successful()) {
            return redirect()
                ->route('whatsapp.settings', $instance->instance_name)
                ->with('success', 'Pengaturan webhook berhasil disimpan.');
        }

        $message = $this->evolutionFailureMessage($response);

        return back()->withInput()->with('error', 'Gagal menyimpan webhook: '.$message);
    }

    private function evolutionFailureMessage(Response $response): string
    {
        if ($response->status() === 401) {
            return 'Autentikasi ke server Evolution API gagal (401 Unauthorized). '
                .'Periksa EVOLUTION_API_GLOBAL_KEY di .env production, lalu jalankan "php artisan optimize:clear". '
                .'Jalankan "php artisan whatsapp:check-evolution" untuk diagnosa.';
        }

        return (string) ($response->json('message')
            ?? $response->json('response.message')
            ?? $response->body());
    }

    private function syncInstancesStatus($instances): void
    {
        try {
            $response = $this->evolutionService->fetchInstances();
        } catch (Throwable) {
            return;
        }

        if (! $response->successful()) {
            return;
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            return;
        }

        if (isset($payload['name']) || isset($payload['instanceName'])) {
            $payload = [$payload];
        }

        $remoteInstances = collect($payload)
            ->filter(fn ($item) => is_array($item))
            ->keyBy(fn ($item) => $item['name'] ?? $item['instanceName'] ?? data_get($item, 'instance.instanceName'));

        foreach ($instances as $instance) {
            $remote = $remoteInstances->get($instance->instance_name);

            if (! $remote) {
                continue;
            }

            $state = $remote['connectionStatus'] ?? $remote['state'] ?? data_get($remote, 'instance.state');
            $data = [];

            if ($state !== null) {
                $data['status'] = $this->mapState($state);
            }

            if (! $instance->phone_number && ! empty($remote['number'])) {
                $data['phone_number'] = $remote['number'];
            }

            if ($data) {
                $instance->update($data);
            }
        }
    }

    private function mapState(?string $state): string
    {
        return match ($state) {
            'open', 'connected' => 'connected',
            'connecting' => 'connecting',
            default => 'disconnected',
        };
    }

    private function webhookEvents(): array
    {
        return [
            'CONNECTION_UPDATE' => 'Status koneksi',
            'MESSAGES_UPSERT' => 'Pesan masuk dan keluar',
            'MESSAGES_UPDATE' => 'Update status pesan',
            'SEND_MESSAGE' => 'Pesan terkirim',
            'QRCODE_UPDATED' => 'QR code diperbarui',
            'CONTACTS_UPSERT' => 'Kontak baru',
            'CONTACTS_UPDATE' => 'Update kontak',
            'GROUPS_UPSERT' => 'Grup baru',
            'GROUP_UPDATE' => 'Update grup',
            'GROUP_PARTICIPANTS_UPDATE' => 'Update peserta grup',
            'PRESENCE_UPDATE' => 'Update presence',
            'CHATS_UPSERT' => 'Chat baru',
            'CHATS_UPDATE' => 'Update chat',
        ];
    }
}
