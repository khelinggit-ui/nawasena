<?php

namespace App\Console\Commands;

use App\Models\MasterPencairanMekar;
use App\Services\AngsuranScheduleService;
use App\Services\PhoneNumberService;
use Illuminate\Console\Command;

class AuditReminderData extends Command
{
    protected $signature = 'reminders:audit
                            {--json : Tampilkan hasil dalam format JSON}
                            {--limit=10 : Jumlah contoh data bermasalah yang ditampilkan}';

    protected $description = 'Audit kesiapan data debitur sebelum pengingat WhatsApp diaktifkan';

    public function handle(
        AngsuranScheduleService $scheduleService,
        PhoneNumberService $phoneService
    ): int {
        $debiturs = MasterPencairanMekar::query()
            ->with('pembayaranAngsurans')
            ->whereIn('status_tahap', ['approve', 'approved'])
            ->orderBy('id')
            ->get();

        $stats = [
            'approved_total' => $debiturs->count(),
            'missing_tanggal_angsuran_pertama_dibayar' => 0,
            'missing_tanggal_jatuh_tempo' => 0,
            'missing_both_due_dates' => 0,
            'missing_tanggal_akad_kredit' => 0,
            'missing_phone' => 0,
            'invalid_phone' => 0,
            'lunas' => 0,
            'aktif' => 0,
            'reliability' => [],
            'due_date_basis' => [],
            'duplicate_phone_groups' => 0,
            'duplicate_phone_extra_rows' => 0,
            'ready_to_send' => 0,
            'needs_review' => 0,
        ];

        $phoneCounts = [];
        $problemRows = [];

        foreach ($debiturs as $debitur) {
            [$summary, $schedule] = $scheduleService->build($debitur);

            $isLunas = (bool) $debitur->is_lunas
                || (! empty($schedule) && collect($schedule)->every(fn ($item) => ! empty($item['is_lunas'])));

            $isLunas ? $stats['lunas']++ : $stats['aktif']++;

            if (empty($debitur->tanggal_angsuran_pertama_dibayar)) {
                $stats['missing_tanggal_angsuran_pertama_dibayar']++;
            }

            if (empty($debitur->tanggal_jatuh_tempo)) {
                $stats['missing_tanggal_jatuh_tempo']++;
            }

            if (empty($debitur->tanggal_angsuran_pertama_dibayar) && empty($debitur->tanggal_jatuh_tempo)) {
                $stats['missing_both_due_dates']++;
            }

            if (empty($debitur->tanggal_akad_kredit)) {
                $stats['missing_tanggal_akad_kredit']++;
            }

            $normalizedPhone = $phoneService->normalize($debitur->nomor_handphone);

            if (empty($debitur->nomor_handphone)) {
                $stats['missing_phone']++;
            } elseif ($normalizedPhone === null) {
                $stats['invalid_phone']++;
            } else {
                $phoneCounts[$normalizedPhone] = ($phoneCounts[$normalizedPhone] ?? 0) + 1;
            }

            $basis = (string) ($summary['due_date_basis'] ?? 'unknown');
            $reliability = (string) ($summary['due_date_reliability'] ?? 'unknown');

            $stats['due_date_basis'][$basis] = ($stats['due_date_basis'][$basis] ?? 0) + 1;
            $stats['reliability'][$reliability] = ($stats['reliability'][$reliability] ?? 0) + 1;

            $hasUnpaid = collect($schedule)->contains(fn ($item) => empty($item['is_lunas']));
            $isReady = ! $isLunas
                && $hasUnpaid
                && $normalizedPhone !== null
                && $reliability === 'high';

            if ($isReady) {
                $stats['ready_to_send']++;
            }

            if (! $isReady) {
                $stats['needs_review']++;

                if (count($problemRows) < (int) $this->option('limit')) {
                    $problemRows[] = [
                        'id' => $debitur->id,
                        'nomor_debitur' => $debitur->nomor_debitur,
                        'nama' => $debitur->nama_peminjam,
                        'lunas' => $isLunas ? 'ya' : 'tidak',
                        'phone' => $normalizedPhone ?? '-',
                        'basis' => $basis,
                        'reliability' => $reliability,
                        'next_due' => $summary['next_jatuh_tempo'] ?? '-',
                        'missing_dates' => empty($debitur->tanggal_angsuran_pertama_dibayar) && empty($debitur->tanggal_jatuh_tempo) ? 'ya' : 'tidak',
                    ];
                }
            }
        }

        foreach ($phoneCounts as $count) {
            if ($count > 1) {
                $stats['duplicate_phone_groups']++;
                $stats['duplicate_phone_extra_rows'] += $count - 1;
            }
        }

        if ($this->option('json')) {
            $this->line(json_encode([
                'stats' => $stats,
                'problem_samples' => $problemRows,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }

        $this->info('Audit data pengingat WhatsApp');
        $this->newLine();

        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Debitur approved', $stats['approved_total']],
                ['Aktif (belum lunas)', $stats['aktif']],
                ['Lunas', $stats['lunas']],
                ['Siap dikirim (tanggal high + HP valid)', $stats['ready_to_send']],
                ['Perlu review', $stats['needs_review']],
                ['Tanpa tanggal angsuran pertama', $stats['missing_tanggal_angsuran_pertama_dibayar']],
                ['Tanpa tanggal jatuh tempo', $stats['missing_tanggal_jatuh_tempo']],
                ['Tanpa kedua tanggal', $stats['missing_both_due_dates']],
                ['Tanpa tanggal akad', $stats['missing_tanggal_akad_kredit']],
                ['Tanpa nomor HP', $stats['missing_phone']],
                ['Nomor HP tidak valid', $stats['invalid_phone']],
                ['Grup nomor duplikat', $stats['duplicate_phone_groups']],
                ['Baris berlebih dari duplikat', $stats['duplicate_phone_extra_rows']],
            ]
        );

        $this->newLine();
        $this->info('Sumber tanggal jatuh tempo:');
        $this->table(
            ['Basis', 'Jumlah'],
            collect($stats['due_date_basis'])->map(fn ($count, $basis) => [$basis, $count])->values()->all()
        );

        $this->newLine();
        $this->info('Tingkat keandalan tanggal:');
        $this->table(
            ['Reliability', 'Jumlah'],
            collect($stats['reliability'])->map(fn ($count, $level) => [$level, $count])->values()->all()
        );

        if ($problemRows !== []) {
            $this->newLine();
            $this->warn('Contoh data yang perlu review:');
            $this->table(
                ['ID', 'Nomor Debitur', 'Nama', 'Lunas', 'HP', 'Basis', 'Reliability', 'Next Due', 'Tanggal Kosong'],
                collect($problemRows)->map(fn ($row) => [
                    $row['id'],
                    $row['nomor_debitur'],
                    $row['nama'],
                    $row['lunas'],
                    $row['phone'],
                    $row['basis'],
                    $row['reliability'],
                    $row['next_due'],
                    $row['missing_dates'],
                ])->all()
            );
        }

        return self::SUCCESS;
    }
}
