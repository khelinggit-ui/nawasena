<?php

namespace App\Console\Commands;

use App\Jobs\SendWhatsappReminderJob;
use App\Models\WhatsappInstance;
use App\Models\WhatsappReminderLog;
use App\Services\WhatsappReminderService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendWhatsappReminders extends Command
{
    protected $signature = 'reminders:send
                            {--dry-run : Tampilkan calon penerima tanpa mengirim}
                            {--send : Kirim pesan melalui queue}
                            {--date= : Tanggal acuan pengingat (Y-m-d)}
                            {--debitur= : Batasi ke satu ID debitur}
                            {--limit= : Batasi jumlah pesan}
                            {--instance= : Nama instance WhatsApp pengirim}';

    protected $description = 'Kirim pengingat WhatsApp ke debitur yang akan jatuh tempo / sudah tertunggak';

    public function handle(WhatsappReminderService $reminderService): int
    {
        $dryRun = (bool) $this->option('dry-run') || ! (bool) $this->option('send');

        $result = $reminderService->candidatesForDate($this->option('date'), [
            'debitur' => $this->option('debitur'),
            'limit' => $this->option('limit'),
        ]);

        $this->renderSummary($result);

        if ($dryRun) {
            $this->newLine();
            $this->info('DRY RUN: tidak ada pesan yang dikirim.');

            return self::SUCCESS;
        }

        $settings = $result['settings'];
        $instanceName = (string) ($this->option('instance') ?: ($settings['instance_name'] ?? ''));

        if (! ($settings['is_active'] ?? false)) {
            $this->error('Pengaturan bot belum aktif. Aktifkan terlebih dahulu di menu WhatsApp API.');

            return self::FAILURE;
        }

        if ($instanceName === '') {
            $this->error('Instance WhatsApp pengirim belum dipilih.');

            return self::FAILURE;
        }

        $instanceConnected = WhatsappInstance::query()
            ->where('instance_name', $instanceName)
            ->where('status', 'connected')
            ->exists();

        if (! $instanceConnected) {
            $this->error("Instance {$instanceName} tidak dalam status connected.");

            return self::FAILURE;
        }

        $candidates = $result['candidates'];

        if ($candidates->isEmpty()) {
            $this->warn('Tidak ada calon penerima untuk tanggal ini.');

            return self::SUCCESS;
        }

        $delay = $settings['delay_seconds'] ?? ['min' => 20, 'max' => 30];
        $delayMin = max((int) ($delay['min'] ?? 20), 1);
        $delayMax = max((int) ($delay['max'] ?? 30), $delayMin);

        $offsetSeconds = 0;
        $queued = 0;
        $skipped = 0;

        foreach ($candidates as $candidate) {
            $log = WhatsappReminderLog::firstOrCreate(
                [
                    'debitur_id' => $candidate['debitur_id'],
                    'installment_number' => $candidate['installment_number'],
                    'reminder_type' => $candidate['reminder_type'],
                ],
                [
                    'reminder_date' => $candidate['reminder_date'],
                    'phone_number' => $candidate['phone_number'],
                    'message' => $candidate['message'],
                    'status' => 'pending',
                    'meta' => [
                        'days_late' => $candidate['days_late'],
                        'due_date' => $candidate['due_date'],
                        'loan_count' => $candidate['loan_count'] ?? 1,
                    ],
                ]
            );

            if (! $log->wasRecentlyCreated) {
                $skipped++;
                continue;
            }

            $offsetSeconds += random_int($delayMin, $delayMax);

            SendWhatsappReminderJob::dispatch($log->id, $instanceName)
                ->delay(now()->addSeconds($offsetSeconds));

            $queued++;
        }

        $this->newLine();
        $this->info("Berhasil masuk queue: {$queued} pesan. Dilewati (sudah ada log): {$skipped}.");
        $this->comment('Pastikan queue worker berjalan: php artisan queue:work --queue=default');

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $result
     */
    private function renderSummary(array $result): void
    {
        $stats = $result['stats'];
        $candidates = $result['candidates'];

        $this->info('Rencana pengingat WhatsApp — '.$result['date']);
        $this->newLine();

        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Debitur approved discan', $stats['approved_total']],
                ['Calon penerima (setelah dedup nomor)', $stats['candidates_total']],
                ['Sudah tercatat hari ini', $stats['today_logged']],
                ['Batas harian', $stats['daily_cap']],
                ['Dilewati: sudah lunas', $stats['skipped']['lunas']],
                ['Dilewati: tanpa angsuran belum lunas', $stats['skipped']['no_unpaid_installment']],
                ['Dilewati: nomor tidak valid', $stats['skipped']['no_phone']],
                ['Dilewati: tidak ada aturan hari ini', $stats['skipped']['no_rule']],
                ['Dilewati: sudah pernah dikirim', $stats['skipped']['already_sent']],
                ['Dilewati: opt-out', $stats['skipped']['opt_out']],
            ]
        );

        if ($candidates->isEmpty()) {
            return;
        }

        $this->newLine();
        $this->table(
            ['Nomor Debitur', 'Nama', 'HP', 'Tipe', 'Jatuh Tempo', 'Telat', 'Sisa', 'Total Tunggakan'],
            $candidates->map(fn (array $candidate) => [
                $candidate['nomor_debitur'],
                Str::limit((string) $candidate['nama'], 24),
                $candidate['phone_number'],
                $candidate['reminder_type'],
                $candidate['due_date'],
                $candidate['days_late'].' hari',
                'Rp '.number_format((float) $candidate['amount_due'], 0, ',', '.'),
                'Rp '.number_format((float) ($candidate['total_tunggakan_group'] ?? $candidate['total_tunggakan']), 0, ',', '.'),
            ])->all()
        );

        $this->newLine();
        $this->comment('Contoh pesan pertama:');
        $this->line($candidates->first()['message']);
    }
}
