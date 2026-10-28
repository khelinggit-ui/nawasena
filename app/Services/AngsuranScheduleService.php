<?php

namespace App\Services;

use App\Models\MasterPencairanMekar;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Throwable;

class AngsuranScheduleService
{
    public const DEFAULT_TIMEZONE = 'Asia/Jakarta';

    public const DENDA_PERSEN_PER_BULAN = 0.005;

    public const BIAYA_ADMIN_BULANAN = 20000;

    /**
     * Hitung ringkasan dan jadwal angsuran debitur.
     *
     * @return array{0: array<string, mixed>, 1: array<int, array<string, mixed>>}
     */
    public function build(MasterPencairanMekar $debitur, CarbonInterface|string|null $today = null): array
    {
        $timezone = $this->timezone();
        $todayDate = $this->resolveToday($today, $timezone);

        $debitur->loadMissing('pembayaranAngsurans');
        $payments = collect($debitur->pembayaranAngsurans ?? []);
        $paymentsByOrder = $payments->keyBy(fn ($payment) => (int) $payment->urutan_bulan);

        $nominalPinjaman = (float) ($debitur->nominal_pinjaman ?? 0);
        $tenorBulan = max((int) ($debitur->tenor_bulan ?? 1), 1);
        $bungaPersen = (float) ($debitur->bungapersen ?? 0);

        $pokok = $nominalPinjaman / $tenorBulan;
        $jasaBulanan = ($nominalPinjaman * ($bungaPersen / 100)) + self::BIAYA_ADMIN_BULANAN;
        $totalAngsuran = ceil(($pokok + $jasaBulanan) / 1000) * 1000;

        $totalPaid = (float) $payments->sum('nominal_bayar');
        $totalPokokDibayarkan = (float) $payments->sum(
            fn ($payment) => max((float) $payment->nominal_bayar - $jasaBulanan, 0)
        );

        $remainingBalance = max($nominalPinjaman - $totalPokokDibayarkan, 0);
        $totalJasaTerhitung = $jasaBulanan * $tenorBulan;
        $totalUtangBerjalan = $nominalPinjaman + $totalJasaTerhitung;

        [$firstDueDate, $dueDateBasis, $dueDateReliability] = $this->resolveFirstDueDate(
            $debitur,
            $payments,
            $tenorBulan,
            $todayDate,
            $timezone
        );

        $schedule = [];
        $runningOutstanding = $nominalPinjaman;
        $bulanMenunggak = 0;
        $totalHariTelat = 0;
        $totalDenda = 0.0;
        $totalTunggakan = 0.0;
        $totalSisaPinjaman = 0.0;
        $jumlahAngsuranMenunggak = 0;
        $nextDueDate = null;
        $nextInstallmentNumber = null;
        $oldestUnpaid = null;

        $dendaPerBulan = $nominalPinjaman * self::DENDA_PERSEN_PER_BULAN;

        for ($i = 1; $i <= $tenorBulan; $i++) {
            $dueDate = $firstDueDate->addMonthsNoOverflow($i - 1)->startOfDay();

            $payment = $paymentsByOrder->get($i);
            $status = $payment ? (int) $payment->status : 0;
            $isLunas = $status === 1;
            $isSebagian = $status === 2;
            $nominalBayar = $payment ? (float) $payment->nominal_bayar : 0.0;

            $tanggalAcuanDenda = null;
            $hariTelat = 0;
            $dendaAngsuran = 0.0;

            if ($isLunas && $payment && $payment->created_at) {
                $tanggalAcuanDenda = $this->parseDate($payment->created_at, $timezone)?->startOfDay();
            } elseif (! $isLunas) {
                $tanggalAcuanDenda = $todayDate;
            }

            if ($tanggalAcuanDenda && $tanggalAcuanDenda->greaterThan($dueDate)) {
                $hariTelat = (int) round($dueDate->diffInDays($tanggalAcuanDenda, true));
                $bulanTelat = max(1, (int) ceil($hariTelat / 30));

                $dendaAngsuran = $dendaPerBulan * $bulanTelat;
                $totalDenda += $dendaAngsuran;
                $totalHariTelat += $hariTelat;
            }

            if ($tanggalAcuanDenda && $dueDate->lessThan($todayDate) && ! $isLunas) {
                $bulanMenunggak += max(1, (int) ceil($hariTelat / 30));
            }

            $jasaDibayarkan = 0.0;
            $pokokDibayarkan = 0.0;

            if ($nominalBayar > 0) {
                if ($nominalBayar >= $jasaBulanan) {
                    $jasaDibayarkan = $jasaBulanan;
                    $pokokDibayarkan = $nominalBayar - $jasaBulanan;
                } else {
                    $jasaDibayarkan = $nominalBayar;
                }
            }

            $runningOutstanding = max($runningOutstanding - $pokokDibayarkan, 0);

            $sisaAngsuran = $isLunas ? 0.0 : max($totalAngsuran - $nominalBayar, 0);
            $isOverdue = ! $isLunas && $dueDate->lessThan($todayDate);

            if (! $isLunas) {
                $totalSisaPinjaman += $sisaAngsuran;

                if ($isOverdue) {
                    $totalTunggakan += $sisaAngsuran;
                    $jumlahAngsuranMenunggak++;

                    if ($oldestUnpaid === null) {
                        $oldestUnpaid = [
                            'installment_number' => $i,
                            'due_date' => $dueDate->toDateString(),
                            'days_late' => $hariTelat,
                            'remaining_amount' => $sisaAngsuran,
                        ];
                    }
                }

                if ($nextDueDate === null) {
                    $nextDueDate = $dueDate;
                    $nextInstallmentNumber = $i;
                }
            }

            $tanggalBayar = '';
            if ($payment && $payment->created_at) {
                $tanggalBayar = $this->parseDate($payment->created_at, $timezone)?->format('d M Y, H:i') ?? '';
            }

            $schedule[] = [
                'urutan' => $i,
                'tgl_tagihan' => $dueDate->format('j M Y'),
                'tgl_jatuh_tempo' => $dueDate->format('d M Y'),
                'jatuh_tempo_date' => $dueDate->toDateString(),
                'hari_telat' => $hariTelat,
                'denda' => round($dendaAngsuran, 2),
                'pokok' => $pokok,
                'jasa' => $jasaBulanan,
                'total_angsuran' => $totalAngsuran,
                'pokok_dibayarkan' => $pokokDibayarkan,
                'jasa_dibayarkan' => $jasaDibayarkan,
                'running_outstanding' => $runningOutstanding,
                'nominal_bayar' => $nominalBayar,
                'sisa_angsuran' => $sisaAngsuran,
                'tgl_bayar' => $tanggalBayar,
                'status' => $status,
                'is_lunas' => $isLunas,
                'is_sebagian' => $isSebagian,
                'is_overdue' => $isOverdue,
                'has_payment' => $payment !== null,
            ];
        }

        if ($nextDueDate === null) {
            $nextDueDate = $firstDueDate->addMonthsNoOverflow($tenorBulan - 1)->startOfDay();
        }

        $summary = [
            'nominal_pinjaman' => $nominalPinjaman,
            'total_paid' => $totalPaid,
            'remaining_balance' => $remainingBalance,
            'total_jasa_terhitung' => $totalJasaTerhitung,
            'total_utang_berjalan' => $totalUtangBerjalan,
            'next_jatuh_tempo' => $nextDueDate->format('d M Y'),
            'next_jatuh_tempo_date' => $nextDueDate->toDateString(),
            'next_installment_number' => $nextInstallmentNumber,
            'tenor_bulan' => $tenorBulan,
            'bulan_menunggak' => $bulanMenunggak,
            'total_hari_telat' => $totalHariTelat,
            'total_denda' => round($totalDenda, 2),
            'total_tunggakan' => round($totalTunggakan, 2),
            'total_sisa_pinjaman' => round($totalSisaPinjaman, 2),
            'jumlah_angsuran_menunggak' => $jumlahAngsuranMenunggak,
            'oldest_unpaid' => $oldestUnpaid,
            'due_date_basis' => $dueDateBasis,
            'due_date_reliability' => $dueDateReliability,
            'timezone' => $timezone,
            'today' => $todayDate->toDateString(),
        ];

        return [$summary, $schedule];
    }

    /**
     * Ambil angsuran belum lunas paling awal.
     *
     * @return array<string, mixed>|null
     */
    public function nextUnpaidInstallment(MasterPencairanMekar $debitur, CarbonInterface|string|null $today = null): ?array
    {
        [, $schedule] = $this->build($debitur, $today);

        foreach ($schedule as $installment) {
            if (! $installment['is_lunas']) {
                return $installment;
            }
        }

        return null;
    }

    public function timezone(): string
    {
        return (string) config('whatsapp.timezone', self::DEFAULT_TIMEZONE);
    }

    /**
     * @return array{0: CarbonImmutable, 1: string, 2: string}
     */
    private function resolveFirstDueDate(
        MasterPencairanMekar $debitur,
        Collection $payments,
        int $tenorBulan,
        CarbonImmutable $today,
        string $timezone
    ): array {
        $firstPaid = $this->parseDate($debitur->tanggal_angsuran_pertama_dibayar, $timezone);
        if ($firstPaid) {
            return [$firstPaid, 'first_payment_date', 'high'];
        }

        $lastDue = $this->parseDate($debitur->tanggal_jatuh_tempo, $timezone);
        if ($lastDue) {
            return [$lastDue->subMonthsNoOverflow($tenorBulan - 1), 'last_due_date', 'high'];
        }

        $akad = $this->parseDate($debitur->tanggal_akad_kredit, $timezone);
        if ($akad) {
            return [$akad->addMonthsNoOverflow(1), 'akad_plus_one_month', 'high'];
        }

        $earliestPayment = $payments
            ->map(fn ($payment) => $this->parseDate($payment->created_at, $timezone))
            ->filter()
            ->sortBy(fn (CarbonImmutable $date) => $date->getTimestamp())
            ->first();

        if ($earliestPayment) {
            return [$earliestPayment, 'earliest_payment_date', 'medium'];
        }

        $createdAt = $this->parseDate($debitur->created_at, $timezone);
        if ($createdAt) {
            return [$createdAt->addMonthsNoOverflow(1), 'created_at_plus_one_month', 'medium'];
        }

        return [$today->addMonthsNoOverflow(1), 'generated_fallback', 'low'];
    }

    private function resolveToday(CarbonInterface|string|null $today, string $timezone): CarbonImmutable
    {
        if ($today === null) {
            return CarbonImmutable::now($timezone)->startOfDay();
        }

        if ($today instanceof CarbonInterface) {
            return CarbonImmutable::parse(
                $today->format('Y-m-d H:i:s.u'),
                $today->getTimezone()->getName()
            )->setTimezone($timezone)->startOfDay();
        }

        return CarbonImmutable::parse($today, $timezone)->startOfDay();
    }

    private function parseDate(CarbonInterface|string|null $value, string $timezone): ?CarbonImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if ($value instanceof CarbonInterface) {
                return CarbonImmutable::parse(
                    $value->format('Y-m-d H:i:s.u'),
                    $value->getTimezone()->getName()
                )->setTimezone($timezone)->startOfDay();
            }

            return CarbonImmutable::parse($value, $timezone)->startOfDay();
        } catch (Throwable) {
            return null;
        }
    }
}
