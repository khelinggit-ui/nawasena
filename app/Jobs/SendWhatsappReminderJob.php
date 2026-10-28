<?php

namespace App\Jobs;

use App\Models\MasterPencairanMekar;
use App\Models\WhatsappOptOut;
use App\Models\WhatsappReminderLog;
use App\Services\AngsuranScheduleService;
use App\Services\EvolutionApiService;
use App\Services\PhoneNumberService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Throwable;

class SendWhatsappReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $backoff = 60;

    public function __construct(
        public readonly int $logId,
        public readonly string $instanceName
    ) {
    }

    public function handle(
        EvolutionApiService $evolutionService,
        AngsuranScheduleService $scheduleService,
        PhoneNumberService $phoneService
    ): void {
        $log = WhatsappReminderLog::find($this->logId);

        if ($log === null) {
            return;
        }

        if (in_array($log->status, ['sent', 'delivered', 'read'], true)) {
            return;
        }

        if (WhatsappOptOut::isOptedOut($log->phone_number)) {
            $log->update([
                'status' => 'skipped',
                'error_message' => 'Nomor ada di daftar opt-out.',
            ]);

            return;
        }

        $debitur = MasterPencairanMekar::with('pembayaranAngsurans')->find($log->debitur_id);

        if ($debitur === null) {
            $log->update([
                'status' => 'failed',
                'error_message' => 'Data debitur tidak ditemukan.',
            ]);

            return;
        }

        [, $schedule] = $scheduleService->build($debitur);

        $installment = collect($schedule)->firstWhere('urutan', (int) $log->installment_number);

        if ($installment === null || ! empty($installment['is_lunas'])) {
            $log->update([
                'status' => 'skipped',
                'error_message' => 'Angsuran sudah dibayar sebelum pesan dikirim.',
            ]);

            return;
        }

        $phone = $phoneService->normalize($log->phone_number);

        if ($phone === null) {
            $log->update([
                'status' => 'failed',
                'error_message' => 'Nomor HP tidak valid.',
            ]);

            return;
        }

        try {
            $response = $evolutionService->sendText($this->instanceName, $phone, (string) $log->message);

            if ($response->successful()) {
                $log->update([
                    'status' => 'sent',
                    'provider_message_id' => $response->json('key.id') ?? $response->json('message.key.id'),
                    'error_message' => null,
                    'sent_at' => now(),
                ]);

                return;
            }

            $errorBody = $response->json('message') ?? $response->body();

            if (is_array($errorBody)) {
                $errorBody = json_encode($errorBody);
            }

            $log->update([
                'status' => 'failed',
                'error_message' => substr((string) $errorBody, 0, 1000),
            ]);
        } catch (Throwable $exception) {
            $log->update([
                'status' => 'failed',
                'error_message' => substr($exception->getMessage(), 0, 1000),
            ]);

            throw $exception;
        }
    }

    /**
     * @return Collection<int, string>
     */
    public function tags(): Collection
    {
        return collect(['whatsapp-reminder', 'log:'.$this->logId]);
    }
}
