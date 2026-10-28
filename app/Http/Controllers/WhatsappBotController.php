<?php

namespace App\Http\Controllers;

use App\Models\WhatsappBotSetting;
use App\Models\WhatsappInstance;
use App\Models\WhatsappOptOut;
use App\Models\WhatsappReminderLog;
use App\Models\WhatsappTemplate;
use App\Services\EvolutionApiService;
use App\Services\PhoneNumberService;
use App\Services\WhatsappReminderService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;

class WhatsappBotController extends Controller
{
    public function __construct(
        private readonly WhatsappReminderService $reminderService,
        private readonly EvolutionApiService $evolutionService,
        private readonly PhoneNumberService $phoneNumberService
    ) {
    }

    public function index(Request $request)
    {
        $setting = WhatsappBotSetting::current();
        $instances = WhatsappInstance::query()->orderBy('instance_name')->get();
        $rules = $this->ruleCatalog();

        $setting ??= new WhatsappBotSetting([
            'timezone' => config('whatsapp.timezone', 'Asia/Jakarta'),
            'send_window_start' => '09:00',
            'send_window_end' => '16:00',
            'max_per_day' => (int) config('whatsapp.reminders.max_per_day', 50),
            'delay_min_seconds' => 20,
            'delay_max_seconds' => 30,
            'active_days' => [1, 2, 3, 4, 5, 6],
            'reminder_rules' => $rules,
            'branch_ids' => [],
        ]);

        $selectedRules = array_keys((array) ($setting->reminder_rules ?: $rules));

        $preview = null;

        if ($request->boolean('preview')) {
            $preview = $this->reminderService->candidatesForDate(
                $request->input('date'),
                ['limit' => $request->input('limit') ?: null]
            );
        }

        return view('whatsapp.bot.index', compact('setting', 'instances', 'rules', 'selectedRules', 'preview'));
    }

    public function dashboard()
    {
        $timezone = (string) config('whatsapp.timezone', 'Asia/Jakarta');
        $today = Carbon::now($timezone)->toDateString();

        $setting = WhatsappBotSetting::current();
        $instance = null;

        if ($setting !== null && $setting->instance_name) {
            $instance = WhatsappInstance::query()
                ->where('instance_name', $setting->instance_name)
                ->first();
        }

        $sentStatuses = ['sent', 'delivered', 'read'];

        $todayCounts = $this->statusCounts(WhatsappReminderLog::query()->whereDate('reminder_date', $today));
        $overallCounts = $this->statusCounts(WhatsappReminderLog::query());

        $todaySent = (int) array_sum(array_intersect_key($todayCounts, array_flip($sentStatuses)));
        $overallSent = (int) array_sum(array_intersect_key($overallCounts, array_flip($sentStatuses)));

        $todayByType = WhatsappReminderLog::query()
            ->whereDate('reminder_date', $today)
            ->selectRaw('reminder_type, COUNT(*) as total')
            ->groupBy('reminder_type')
            ->orderByDesc('total')
            ->pluck('total', 'reminder_type');

        $trend = collect(range(6, 0))->map(function (int $daysAgo) use ($timezone, $sentStatuses) {
            $date = Carbon::now($timezone)->subDays($daysAgo)->toDateString();
            $counts = $this->statusCounts(WhatsappReminderLog::query()->whereDate('reminder_date', $date));
            $sent = (int) array_sum(array_intersect_key($counts, array_flip($sentStatuses)));

            return [
                'date' => $date,
                'label' => Carbon::parse($date)->format('d/m'),
                'sent' => $sent,
                'failed' => $counts['failed'],
                'skipped' => $counts['skipped'],
                'pending' => $counts['pending'],
                'total' => array_sum($counts),
            ];
        });

        $optOutTotal = WhatsappOptOut::query()->count();
        $optOutToday = WhatsappOptOut::query()->whereDate('created_at', $today)->count();

        $uniquePhonesToday = WhatsappReminderLog::query()
            ->whereDate('reminder_date', $today)
            ->distinct('phone_number')
            ->count('phone_number');

        $recentLogs = WhatsappReminderLog::query()
            ->with('debitur')
            ->latest('id')
            ->limit(10)
            ->get();

        return view('whatsapp.bot.dashboard', compact(
            'setting',
            'instance',
            'today',
            'todayCounts',
            'overallCounts',
            'todaySent',
            'overallSent',
            'todayByType',
            'trend',
            'optOutTotal',
            'optOutToday',
            'uniquePhonesToday',
            'recentLogs'
        ));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'instance_name' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'timezone' => ['required', 'string', 'max:64'],
            'send_window_start' => ['required', 'date_format:H:i'],
            'send_window_end' => ['required', 'date_format:H:i', 'after:send_window_start'],
            'max_per_day' => ['required', 'integer', 'min:1', 'max:200'],
            'delay_min_seconds' => ['required', 'integer', 'min:5', 'max:600'],
            'delay_max_seconds' => ['required', 'integer', 'min:5', 'max:600', 'gte:delay_min_seconds'],
            'active_days' => ['nullable', 'array'],
            'active_days.*' => ['integer', 'between:1,7'],
            'rules' => ['nullable', 'array'],
            'rules.*' => ['string'],
            'branch_ids' => ['nullable', 'array'],
            'branch_ids.*' => ['integer'],
            'opt_out_enabled' => ['nullable', 'boolean'],
            'contact_cs' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_name' => ['nullable', 'string', 'max:150'],
        ]);

        $catalog = $this->ruleCatalog();
        $selectedRuleKeys = array_values(array_intersect(array_keys($catalog), (array) ($validated['rules'] ?? [])));
        $rules = array_intersect_key($catalog, array_flip($selectedRuleKeys));

        $setting = WhatsappBotSetting::current() ?? new WhatsappBotSetting();

        $setting->fill([
            'instance_name' => $validated['instance_name'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'timezone' => $validated['timezone'],
            'send_window_start' => $validated['send_window_start'],
            'send_window_end' => $validated['send_window_end'],
            'max_per_day' => $validated['max_per_day'],
            'delay_min_seconds' => $validated['delay_min_seconds'],
            'delay_max_seconds' => $validated['delay_max_seconds'],
            'active_days' => array_map('intval', $validated['active_days'] ?? []),
            'reminder_rules' => $rules,
            'branch_ids' => array_map('intval', $validated['branch_ids'] ?? []),
            'opt_out_enabled' => (bool) ($validated['opt_out_enabled'] ?? false),
            'contact_cs' => $validated['contact_cs'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'bank_account_name' => $validated['bank_account_name'] ?? null,
            'updated_by' => auth()->id(),
        ]);

        $setting->save();

        return redirect()
            ->route('whatsapp.bot.index')
            ->with('success', 'Pengaturan bot pengingat berhasil disimpan.');
    }

    public function templates()
    {
        $this->ensureDefaultTemplates();

        $templates = WhatsappTemplate::query()->orderBy('key')->get();

        return view('whatsapp.bot.templates', compact('templates'));
    }

    public function updateTemplates(Request $request)
    {
        $validated = $request->validate([
            'templates' => ['required', 'array'],
            'templates.*.name' => ['required', 'string', 'max:150'],
            'templates.*.body' => ['required', 'string'],
            'templates.*.is_active' => ['nullable', 'boolean'],
        ]);

        foreach ($validated['templates'] as $key => $template) {
            WhatsappTemplate::query()->updateOrCreate(
                ['key' => $key],
                [
                    'name' => $template['name'],
                    'body' => $template['body'],
                    'is_active' => (bool) ($template['is_active'] ?? false),
                ]
            );
        }

        return redirect()
            ->route('whatsapp.bot.templates')
            ->with('success', 'Template pesan berhasil disimpan.');
    }

    public function logs(Request $request)
    {
        $query = WhatsappReminderLog::query()->with('debitur')->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('phone')) {
            $phone = $this->phoneNumberService->normalize($request->input('phone')) ?? $request->input('phone');
            $query->where('phone_number', 'like', '%'.$phone.'%');
        }

        if ($request->filled('date')) {
            $query->whereDate('reminder_date', $request->input('date'));
        }

        $logs = $query->paginate(25)->withQueryString();
        $statuses = ['pending', 'sent', 'failed', 'skipped', 'delivered', 'read'];

        return view('whatsapp.bot.logs', compact('logs', 'statuses'));
    }

    public function optOuts()
    {
        $optOuts = WhatsappOptOut::query()->latest('id')->paginate(25);

        return view('whatsapp.bot.opt-outs', compact('optOuts'));
    }

    public function storeOptOut(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => ['required', 'string', 'max:30'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $phone = $this->phoneNumberService->normalize($validated['phone_number']);

        if ($phone === null) {
            return back()->withInput()->with('error', 'Nomor HP tidak valid. Gunakan format 08xx atau 62xx.');
        }

        WhatsappOptOut::query()->updateOrCreate(
            ['phone_number' => $phone],
            [
                'reason' => $validated['reason'] ?? null,
                'source' => 'manual',
                'created_by' => auth()->id(),
            ]
        );

        return redirect()
            ->route('whatsapp.bot.opt-outs')
            ->with('success', 'Nomor '.$phone.' berhasil ditambahkan ke blacklist.');
    }

    public function destroyOptOut(int $id)
    {
        $optOut = WhatsappOptOut::query()->findOrFail($id);
        $optOut->delete();

        return redirect()
            ->route('whatsapp.bot.opt-outs')
            ->with('success', 'Nomor berhasil dihapus dari blacklist.');
    }

    public function testSend(Request $request)
    {
        $validated = $request->validate([
            'instance_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $phone = $this->phoneNumberService->normalize($validated['phone_number']);

        if ($phone === null) {
            return back()->withInput()->with('error', 'Nomor HP tidak valid. Gunakan format 08xx atau 62xx.');
        }

        $instance = WhatsappInstance::query()
            ->where('instance_name', $validated['instance_name'])
            ->where('status', 'connected')
            ->first();

        if ($instance === null) {
            return back()->withInput()->with('error', 'Instance belum terhubung. Pastikan status connected sebelum test kirim.');
        }

        try {
            $response = $this->evolutionService->sendText(
                $instance->instance_name,
                $phone,
                $validated['message']
            );
        } catch (Throwable $exception) {
            return back()->withInput()->with('error', 'Gagal menghubungi Evolution API: '.$exception->getMessage());
        }

        if (! $response->successful()) {
            if ($response->status() === 401) {
                return back()->withInput()->with('error', 'Autentikasi ke server Evolution API gagal (401 Unauthorized). '
                    .'Periksa EVOLUTION_API_GLOBAL_KEY di .env production, lalu jalankan "php artisan optimize:clear".');
            }

            $message = $response->json('message') ?? $response->body();

            if (is_array($message)) {
                $message = json_encode($message);
            }

            return back()->withInput()->with('error', 'Gagal mengirim pesan uji: '.$message);
        }

        return redirect()
            ->route('whatsapp.bot.index')
            ->with('success', 'Pesan uji berhasil dikirim ke '.$phone.'.');
    }

    /**
     * @return array<string, int>
     */
    private function statusCounts(\Illuminate\Database\Eloquent\Builder $query): array
    {
        $rows = $query->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $counts = [
            'pending' => 0,
            'sent' => 0,
            'failed' => 0,
            'skipped' => 0,
            'delivered' => 0,
            'read' => 0,
        ];

        foreach ($rows as $status => $total) {
            $counts[(string) $status] = (int) $total;
        }

        return $counts;
    }

    /**
     * @return array<string, array{offset_days: int, label: string}>
     */
    private function ruleCatalog(): array
    {
        $catalog = [];

        foreach ((array) config('whatsapp.reminders.rules', []) as $key => $rule) {
            $catalog[(string) $key] = [
                'offset_days' => (int) ($rule['offset_days'] ?? 0),
                'label' => (string) ($rule['label'] ?? $key),
            ];
        }

        return $catalog;
    }

    private function ensureDefaultTemplates(): void
    {
        WhatsappTemplate::query()->firstOrCreate(
            ['key' => 'due_reminder'],
            [
                'name' => 'Pengingat jatuh tempo',
                'body' => "Halo {{nama}},\n\nPengingat: angsuran ke-{{angsuran_ke}} sebesar {{nominal_angsuran}} akan jatuh tempo pada {{tanggal_jatuh_tempo}}.\n\nTotal tunggakan saat ini: {{total_tunggakan}}.\n\nTerima kasih.",
                'is_active' => true,
            ]
        );

        WhatsappTemplate::query()->firstOrCreate(
            ['key' => 'overdue_reminder'],
            [
                'name' => 'Pengingat tunggakan',
                'body' => "Halo {{nama}},\n\nAngsuran ke-{{angsuran_ke}} sebesar {{nominal_angsuran}} telah terlambat {{hari_terlambat}} hari (jatuh tempo {{tanggal_jatuh_tempo}}).\n\nTotal tunggakan: {{total_tunggakan}}.\nMohon segera lakukan pembayaran.\n\nTerima kasih.",
                'is_active' => true,
            ]
        );
    }
}
