<?php

namespace App\Services;

use App\Models\MasterPencairanMekar;
use App\Models\WhatsappBotSetting;
use App\Models\WhatsappOptOut;
use App\Models\WhatsappReminderLog;
use App\Models\WhatsappTemplate;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class WhatsappReminderService
{
    public function __construct(
        private readonly AngsuranScheduleService $scheduleService,
        private readonly PhoneNumberService $phoneService
    ) {
    }

    /**
     * Hitung calon penerima pengingat untuk tanggal tertentu.
     *
     * @param  array{debitur?: int|string|null, limit?: int|string|null, branch_ids?: array|null}  $options
     * @return array{date: string, candidates: Collection<int, array<string, mixed>>, stats: array<string, mixed>, settings: array<string, mixed>}
     */
    public function candidatesForDate(CarbonInterface|string|null $date = null, array $options = []): array
    {
        $settings = $this->settings();
        $timezone = (string) $settings['timezone'];
        $resolvedDate = $this->resolveDate($date, $timezone);
        $rules = $this->normalizeRules((array) ($settings['rules'] ?? []));

        $stats = [
            'approved_total' => 0,
            'candidates_total' => 0,
            'skipped' => [
                'lunas' => 0,
                'no_unpaid_installment' => 0,
                'no_phone' => 0,
                'no_rule' => 0,
                'already_sent' => 0,
                'opt_out' => 0,
                'inactive_day' => 0,
            ],
            'today_logged' => 0,
            'daily_cap' => (int) ($settings['max_per_day'] ?? 50),
        ];

        if (! $this->isActiveDay($resolvedDate, $settings)) {
            $stats['skipped']['inactive_day'] = 1;

            return [
                'date' => $resolvedDate->toDateString(),
                'candidates' => collect(),
                'stats' => $stats,
                'settings' => $settings,
            ];
        }

        $branchIds = $options['branch_ids'] ?? $settings['branch_ids'] ?? null;

        $query = MasterPencairanMekar::query()
            ->with(['pembayaranAngsurans', 'cabang'])
            ->whereIn('status_tahap', ['approve', 'approved']);

        if (! empty($options['debitur'])) {
            $query->whereKey($options['debitur']);
        }

        if (! empty($branchIds)) {
            $query->whereIn('cabang_id', (array) $branchIds);
        }

        $debiturs = $query->orderBy('id')->get();
        $stats['approved_total'] = $debiturs->count();

        $sentMilestones = $this->loadSentMilestones($debiturs->pluck('id')->all());
        $stats['today_logged'] = $this->todayLoggedCount($resolvedDate);
        $remainingQuota = max($stats['daily_cap'] - $stats['today_logged'], 0);

        $requestedLimit = $options['limit'] ?? null;

        if ($requestedLimit !== null && (int) $requestedLimit > 0) {
            $remainingQuota = min($remainingQuota, (int) $requestedLimit);
        }

        $candidates = collect();

        foreach ($debiturs as $debitur) {
            [$summary, $schedule] = $this->scheduleService->build($debitur, $resolvedDate);
            $isLunas = (bool) $debitur->is_lunas
                || (! empty($schedule) && collect($schedule)->every(fn ($item) => ! empty($item['is_lunas'])));

            if ($isLunas) {
                $stats['skipped']['lunas']++;
                continue;
            }

            $installment = collect($schedule)->first(fn ($item) => empty($item['is_lunas']));

            if ($installment === null) {
                $stats['skipped']['no_unpaid_installment']++;
                continue;
            }

            $phone = $this->phoneService->normalize($debitur->nomor_handphone);

            if ($phone === null) {
                $stats['skipped']['no_phone']++;
                continue;
            }

            $dueDate = CarbonImmutable::parse($installment['jatuh_tempo_date'], $timezone)->startOfDay();
            $daysLate = (int) round($dueDate->diffInDays($resolvedDate));

            [$reminderType, $reason] = $this->resolveReminderType(
                $daysLate,
                $rules,
                (int) $debitur->id,
                (int) $installment['urutan'],
                $sentMilestones
            );

            if ($reminderType === null) {
                $stats['skipped'][$reason] = ($stats['skipped'][$reason] ?? 0) + 1;
                continue;
            }

            if ($settings['opt_out_enabled'] && $this->isOptedOut($phone)) {
                $stats['skipped']['opt_out']++;
                continue;
            }

            $candidates->push([
                'debitur_id' => (int) $debitur->id,
                'nomor_debitur' => $debitur->nomor_debitur,
                'nama' => $debitur->nama_peminjam,
                'phone_number' => $phone,
                'cabang_id' => $debitur->cabang_id,
                'nama_cabang' => $debitur->cabang->nama_cabang ?? null,
                'nomor_rekening' => $debitur->nomer_rekening,
                'nama_pemilik_rekening' => $debitur->nama_pemilik_rekening,
                'installment_number' => (int) $installment['urutan'],
                'reminder_type' => $reminderType,
                'reminder_date' => $resolvedDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'days_late' => $daysLate,
                'amount_due' => (float) $installment['sisa_angsuran'],
                'installment_amount' => (float) $installment['total_angsuran'],
                'total_tunggakan' => (float) ($summary['total_tunggakan'] ?? 0),
                'total_denda' => (float) ($summary['total_denda'] ?? 0),
                'next_jatuh_tempo' => $summary['next_jatuh_tempo'] ?? null,
            ]);
        }

        $grouped = $candidates
            ->groupBy('phone_number')
            ->map(function (Collection $group) {
                $primary = $group->sortBy([
                    ['due_date', 'asc'],
                    ['days_late', 'desc'],
                ])->first();

                $primary['loan_count'] = $group->count();
                $primary['total_tunggakan_group'] = (float) $group->sum('total_tunggakan');
                $primary['total_denda_group'] = (float) $group->sum('total_denda');
                $primary['amount_due_group'] = (float) $group->sum('amount_due');

                return $primary;
            })
            ->values();

        // Prioritas: yang akan jatuh tempo (paling dekat) dulu, lalu tunggakan terlama.
        $grouped = $grouped
            ->sort(function (array $a, array $b) {
                $aUpcoming = ((int) $a['days_late']) < 0;
                $bUpcoming = ((int) $b['days_late']) < 0;

                if ($aUpcoming !== $bUpcoming) {
                    return $aUpcoming ? -1 : 1;
                }

                if ($aUpcoming) {
                    return strcmp((string) $a['due_date'], (string) $b['due_date']);
                }

                return ((int) $b['days_late']) <=> ((int) $a['days_late']);
            })
            ->values();

        if ($remainingQuota < $grouped->count()) {
            $grouped = $grouped->take($remainingQuota);
        }

        $grouped = $grouped->map(function (array $candidate) use ($settings, $rules) {
            $candidate['message'] = $this->buildMessage($candidate, $settings, $rules);

            return $candidate;
        })->values();

        $stats['candidates_total'] = $grouped->count();

        return [
            'date' => $resolvedDate->toDateString(),
            'candidates' => $grouped,
            'stats' => $stats,
            'settings' => $settings,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function settings(): array
    {
        $config = (array) config('whatsapp.reminders', []);
        $timezone = (string) config('whatsapp.timezone', AngsuranScheduleService::DEFAULT_TIMEZONE);

        $defaults = [
            'instance_name' => null,
            'is_active' => (bool) ($config['enabled'] ?? false),
            'timezone' => $timezone,
            'send_window' => $config['send_window'] ?? ['start' => '09:00', 'end' => '16:00'],
            'max_per_day' => (int) ($config['max_per_day'] ?? 50),
            'delay_seconds' => $config['delay_seconds'] ?? ['min' => 20, 'max' => 30],
            'active_days' => null,
            'rules' => $config['rules'] ?? [],
            'branch_ids' => null,
            'opt_out_enabled' => false,
            'contact_cs' => null,
            'bank_name' => null,
            'bank_account_number' => null,
            'bank_account_name' => null,
        ];

        if (! Schema::hasTable('whatsapp_bot_settings')) {
            return $defaults;
        }

        $setting = WhatsappBotSetting::current();

        if ($setting === null) {
            return $defaults;
        }

        return [
            'instance_name' => $setting->instance_name,
            'is_active' => (bool) $setting->is_active,
            'timezone' => $setting->timezone ?: $timezone,
            'send_window' => [
                'start' => substr((string) $setting->send_window_start, 0, 5),
                'end' => substr((string) $setting->send_window_end, 0, 5),
            ],
            'max_per_day' => (int) ($setting->max_per_day ?: $defaults['max_per_day']),
            'delay_seconds' => [
                'min' => (int) ($setting->delay_min_seconds ?: 20),
                'max' => (int) ($setting->delay_max_seconds ?: 30),
            ],
            'active_days' => $setting->active_days,
            'rules' => $setting->reminder_rules ?: $defaults['rules'],
            'branch_ids' => $setting->branch_ids,
            'opt_out_enabled' => (bool) $setting->opt_out_enabled,
            'contact_cs' => $setting->contact_cs,
            'bank_name' => $setting->bank_name,
            'bank_account_number' => $setting->bank_account_number,
            'bank_account_name' => $setting->bank_account_name,
        ];
    }

    /**
     * @return array<string, array{offset_days: int, label: string}>
     */
    public function normalizeRules(array $rules): array
    {
        $normalized = [];

        foreach ($rules as $key => $rule) {
            if (is_array($rule) && isset($rule['offset_days'])) {
                $normalized[(string) $key] = [
                    'offset_days' => (int) $rule['offset_days'],
                    'label' => (string) ($rule['label'] ?? $key),
                ];
            } elseif (is_array($rule) && isset($rule['key'], $rule['offset_days'])) {
                $normalized[(string) $rule['key']] = [
                    'offset_days' => (int) $rule['offset_days'],
                    'label' => (string) ($rule['label'] ?? $rule['key']),
                ];
            } elseif (is_numeric($rule)) {
                $normalized[(string) $key] = [
                    'offset_days' => (int) $rule,
                    'label' => (string) $key,
                ];
            }
        }

        if ($normalized === []) {
            $configRules = (array) config('whatsapp.reminders.rules', []);

            foreach ($configRules as $key => $rule) {
                $normalized[(string) $key] = [
                    'offset_days' => (int) ($rule['offset_days'] ?? 0),
                    'label' => (string) ($rule['label'] ?? $key),
                ];
            }
        }

        return $normalized;
    }

    /**
     * @param  array<string, array<int, array<string, bool>>>  $sent
     * @return array{0: string|null, 1: string}
     */
    private function resolveReminderType(
        int $daysLate,
        array $rules,
        int $debiturId,
        int $installmentNumber,
        array $sent
    ): array {
        if ($rules === []) {
            return [null, 'no_rule'];
        }

        if ($daysLate < 0) {
            foreach ($rules as $key => $rule) {
                if ((int) $rule['offset_days'] !== $daysLate) {
                    continue;
                }

                if (isset($sent[$debiturId][$installmentNumber][$key])) {
                    return [null, 'already_sent'];
                }

                return [$key, 'ok'];
            }

            return [null, 'no_rule'];
        }

        $eligible = [];

        foreach ($rules as $key => $rule) {
            $offset = (int) $rule['offset_days'];

            if ($offset >= 0 && $offset <= $daysLate) {
                $eligible[$key] = $offset;
            }
        }

        if ($eligible === []) {
            return [null, 'no_rule'];
        }

        arsort($eligible);
        $key = (string) array_key_first($eligible);

        if (isset($sent[$debiturId][$installmentNumber][$key])) {
            return [null, 'already_sent'];
        }

        return [$key, 'ok'];
    }

    /**
     * @param  array<string, mixed>  $candidate
     * @param  array<string, mixed>  $settings
     * @param  array<string, array{offset_days: int, label: string}>  $rules
     */
    private function buildMessage(array $candidate, array $settings, array $rules): string
    {
        $templateKey = ((int) $candidate['days_late']) >= 0 ? 'overdue_reminder' : 'due_reminder';
        $defaults = $this->defaultTemplates();
        $template = null;

        if (Schema::hasTable('whatsapp_templates')) {
            $template = WhatsappTemplate::bodyFor($templateKey);
        }

        $template = $template ?: ($defaults[$templateKey] ?? '');

        $data = [
            'nama' => (string) ($candidate['nama'] ?? ''),
            'nomor_debitur' => (string) ($candidate['nomor_debitur'] ?? ''),
            'angsuran_ke' => (string) ($candidate['installment_number'] ?? ''),
            'nominal_angsuran' => $this->rupiah((float) ($candidate['installment_amount'] ?? 0)),
            'tanggal_jatuh_tempo' => CarbonImmutable::parse((string) $candidate['due_date'], (string) $settings['timezone'])->format('d/m/Y'),
            'hari_terlambat' => (string) max((int) ($candidate['days_late'] ?? 0), 0),
            'total_tunggakan' => $this->rupiah((float) ($candidate['total_tunggakan_group'] ?? $candidate['total_tunggakan'] ?? 0)),
            'sisa_angsuran' => $this->rupiah((float) ($candidate['amount_due_group'] ?? $candidate['amount_due'] ?? 0)),
            'denda' => $this->rupiah((float) ($candidate['total_denda_group'] ?? $candidate['total_denda'] ?? 0)),
            'nama_cabang' => (string) ($candidate['nama_cabang'] ?? ''),
            'kontak_cs' => (string) ($settings['contact_cs'] ?? ''),
            'nama_bank' => (string) ($settings['bank_name'] ?? ''),
            'nomor_rekening' => (string) ($settings['bank_account_number'] ?? $candidate['nomor_rekening'] ?? ''),
            'nama_pemilik_rekening' => (string) ($settings['bank_account_name'] ?? $candidate['nama_pemilik_rekening'] ?? ''),
            'reminder_type' => (string) ($candidate['reminder_type'] ?? ''),
        ];

        return (string) preg_replace_callback(
            '/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/',
            fn (array $matches) => (string) ($data[$matches[1]] ?? ''),
            $template
        );
    }

    /**
     * @return array<string, string>
     */
    private function defaultTemplates(): array
    {
        return [
            'due_reminder' => "Halo {{nama}},\n\nPengingat: angsuran ke-{{angsuran_ke}} sebesar {{nominal_angsuran}} akan jatuh tempo pada {{tanggal_jatuh_tempo}}.\n\nTotal tunggakan saat ini: {{total_tunggakan}}.\n\nTerima kasih.",
            'overdue_reminder' => "Halo {{nama}},\n\nAngsuran ke-{{angsuran_ke}} sebesar {{nominal_angsuran}} telah terlambat {{hari_terlambat}} hari (jatuh tempo {{tanggal_jatuh_tempo}}).\n\nTotal tunggakan: {{total_tunggakan}}.\nMohon segera lakukan pembayaran.\n\nTerima kasih.",
        ];
    }

    private function isActiveDay(CarbonImmutable $date, array $settings): bool
    {
        $activeDays = $settings['active_days'] ?? null;

        if (empty($activeDays) || ! is_array($activeDays)) {
            return true;
        }

        return in_array($date->dayOfWeekIso, array_map('intval', $activeDays), true);
    }

    private function isOptedOut(string $phoneNumber): bool
    {
        if (! Schema::hasTable('whatsapp_opt_outs')) {
            return false;
        }

        return WhatsappOptOut::isOptedOut($phoneNumber);
    }

    /**
     * @param  array<int, int|string>  $debiturIds
     * @return array<int, array<int, array<string, bool>>>
     */
    private function loadSentMilestones(array $debiturIds): array
    {
        if ($debiturIds === [] || ! Schema::hasTable('whatsapp_reminder_logs')) {
            return [];
        }

        $logs = WhatsappReminderLog::query()
            ->whereIn('debitur_id', $debiturIds)
            ->whereIn('status', ['pending', 'sent', 'delivered', 'read'])
            ->get(['debitur_id', 'installment_number', 'reminder_type']);

        $cache = [];

        foreach ($logs as $log) {
            $cache[(int) $log->debitur_id][(int) $log->installment_number][(string) $log->reminder_type] = true;
        }

        return $cache;
    }

    private function todayLoggedCount(CarbonImmutable $date): int
    {
        if (! Schema::hasTable('whatsapp_reminder_logs')) {
            return 0;
        }

        return WhatsappReminderLog::query()
            ->whereDate('reminder_date', $date->toDateString())
            ->whereIn('status', ['pending', 'sent', 'delivered', 'read'])
            ->distinct('phone_number')
            ->count('phone_number');
    }

    private function resolveDate(CarbonInterface|string|null $date, string $timezone): CarbonImmutable
    {
        if ($date === null) {
            return CarbonImmutable::now($timezone)->startOfDay();
        }

        if ($date instanceof CarbonInterface) {
            return CarbonImmutable::parse(
                $date->format('Y-m-d H:i:s.u'),
                $date->getTimezone()->getName()
            )->setTimezone($timezone)->startOfDay();
        }

        return CarbonImmutable::parse($date, $timezone)->startOfDay();
    }

    private function rupiah(float $value): string
    {
        return 'Rp '.number_format($value, 0, ',', '.');
    }
}
