<?php

namespace App\Http\Controllers;

use App\Models\WhatsappInstance;
use App\Models\WhatsappOptOut;
use App\Models\WhatsappReminderLog;
use App\Services\PhoneNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WebhookController extends Controller
{
    /**
     * Kata kunci balasan yang dianggap minta berhenti.
     */
    private const OPT_OUT_KEYWORDS = ['stop', 'berhenti', 'unsubscribe', 'jangan kirim', 'jangan hubungi'];

    public function handle(Request $request, PhoneNumberService $phoneService)
    {
        $event = strtoupper(str_replace('.', '_', (string) $request->input('event')));
        $instanceName = $request->input('instance')
            ?? $request->input('instanceName')
            ?? $request->input('data.instance');

        if (is_array($instanceName)) {
            $instanceName = $instanceName['instanceName'] ?? $instanceName['name'] ?? null;
        }

        if ($event === 'CONNECTION_UPDATE' && $instanceName) {
            $state = $request->input('data.state');

            $statusMap = [
                'open' => 'connected',
                'connecting' => 'connecting',
                'close' => 'disconnected',
            ];

            WhatsappInstance::where('instance_name', $instanceName)->update([
                'status' => $statusMap[$state] ?? 'disconnected',
            ]);
        }

        if ($event === 'MESSAGES_UPDATE') {
            $this->syncMessageStatuses($request->input('data', []));
        }

        if (in_array($event, ['MESSAGES_UPSERT', 'SEND_MESSAGE'], true)) {
            $this->handleIncomingMessages($request->input('data', []), $phoneService);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Sinkronkan status pesan keluar (pending/sent/delivered/read) ke log pengingat.
     */
    private function syncMessageStatuses(mixed $data): void
    {
        foreach ($this->normalizeList($data) as $update) {
            if (! is_array($update)) {
                continue;
            }

            $messageId = $update['key']['id'] ?? $update['keyId'] ?? $update['id'] ?? null;
            $status = $update['status'] ?? $update['update']['status'] ?? $update['messageStatus'] ?? null;

            if (! $messageId || $status === null) {
                continue;
            }

            $mapped = $this->mapStatus($status);

            if ($mapped === null) {
                continue;
            }

            $log = WhatsappReminderLog::query()->where('provider_message_id', $messageId)->first();

            if ($log === null) {
                continue;
            }

            $log->status = $mapped;

            if ($mapped !== 'pending' && $log->sent_at === null) {
                $log->sent_at = Carbon::now();
            }

            $log->save();
        }
    }

    /**
     * Tangani pesan masuk: deteksi kata kunci opt-out.
     */
    private function handleIncomingMessages(mixed $data, PhoneNumberService $phoneService): void
    {
        foreach ($this->normalizeList($data) as $message) {
            if (! is_array($message)) {
                continue;
            }

            $key = $message['key'] ?? [];

            if (! is_array($key) || ! empty($key['fromMe'])) {
                continue;
            }

            $remoteJid = (string) ($key['remoteJid'] ?? '');

            if ($remoteJid === '' || str_contains($remoteJid, '@g.us')) {
                continue;
            }

            $text = $this->extractText($message);

            if ($text === null) {
                continue;
            }

            $normalized = strtolower(trim($text));

            foreach (self::OPT_OUT_KEYWORDS as $keyword) {
                if ($normalized === $keyword || str_starts_with($normalized, $keyword)) {
                    $phone = $phoneService->normalize(explode('@', $remoteJid)[0]);

                    if ($phone !== null) {
                        WhatsappOptOut::query()->updateOrCreate(
                            ['phone_number' => $phone],
                            [
                                'reason' => 'Balasan otomatis: '.$text,
                                'source' => 'auto',
                            ]
                        );
                    }

                    break;
                }
            }
        }
    }

    private function extractText(array $message): ?string
    {
        $payload = $message['message'] ?? null;

        if (! is_array($payload)) {
            return null;
        }

        $text = $payload['conversation']
            ?? $payload['extendedTextMessage']['text']
            ?? $payload['buttonsResponseMessage']['selectedDisplayText']
            ?? $payload['listResponseMessage']['title']
            ?? null;

        if (! is_string($text) || trim($text) === '') {
            return null;
        }

        return $text;
    }

    private function mapStatus(mixed $status): ?string
    {
        if (is_numeric($status)) {
            return match ((int) $status) {
                1 => 'pending',
                2 => 'sent',
                3 => 'delivered',
                4, 5 => 'read',
                default => null,
            };
        }

        if (! is_string($status)) {
            return null;
        }

        return match (strtoupper($status)) {
            'PENDING', 'PENDING_ACK' => 'pending',
            'SERVER_ACK', 'SENT' => 'sent',
            'DELIVERY_ACK', 'DELIVERED' => 'delivered',
            'READ', 'PLAYED' => 'read',
            default => null,
        };
    }

    /**
     * @return array<int, mixed>
     */
    private function normalizeList(mixed $data): array
    {
        if (! is_array($data) || $data === []) {
            return [];
        }

        if (array_keys($data) !== range(0, count($data) - 1)) {
            return [$data];
        }

        return $data;
    }
}
