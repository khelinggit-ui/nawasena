<?php

namespace Tests\Unit;

use App\Models\MasterPencairanMekar;
use App\Models\PembayaranAngsuran;
use App\Services\AngsuranScheduleService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AngsuranScheduleServiceTest extends TestCase
{
    private AngsuranScheduleService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AngsuranScheduleService();
    }

    public function test_it_builds_schedule_from_first_payment_date(): void
    {
        $debitur = $this->makeDebitur([
            'nominal_pinjaman' => 12_000_000,
            'tenor_bulan' => 12,
            'bungapersen' => 0,
            'tanggal_angsuran_pertama_dibayar' => '2026-01-15',
        ]);

        [$summary, $schedule] = $this->service->build($debitur, '2026-01-10');

        $this->assertCount(12, $schedule);
        $this->assertSame('2026-01-15', $summary['next_jatuh_tempo_date']);
        $this->assertSame(1, $summary['next_installment_number']);
        $this->assertSame('first_payment_date', $summary['due_date_basis']);
        $this->assertSame('high', $summary['due_date_reliability']);
        $this->assertSame(0.0, $summary['total_tunggakan']);
        $this->assertSame(12 * 1_020_000.0, $summary['total_sisa_pinjaman']);
        $this->assertSame(0, $summary['jumlah_angsuran_menunggak']);
    }

    public function test_it_flags_overdue_installments_and_penalty(): void
    {
        $debitur = $this->makeDebitur([
            'nominal_pinjaman' => 12_000_000,
            'tenor_bulan' => 12,
            'bungapersen' => 0,
            'tanggal_angsuran_pertama_dibayar' => '2026-01-15',
        ]);

        [$summary, $schedule] = $this->service->build($debitur, '2026-02-20');

        $this->assertSame('2026-01-15', $summary['next_jatuh_tempo_date']);
        $this->assertSame(1, $summary['next_installment_number']);
        $this->assertSame(2, $summary['jumlah_angsuran_menunggak']);
        $this->assertSame(2 * 1_020_000.0, $summary['total_tunggakan']);

        // Angsuran 1 telat 36 hari (2 bulan), angsuran 2 telat 5 hari (1 bulan).
        $this->assertSame(60_000 * 2 + 60_000.0, $summary['total_denda']);

        $this->assertNotNull($summary['oldest_unpaid']);
        $this->assertSame(1, $summary['oldest_unpaid']['installment_number']);
        $this->assertSame(36, $summary['oldest_unpaid']['days_late']);

        $this->assertTrue($schedule[0]['is_overdue']);
        $this->assertFalse($schedule[2]['is_overdue']);
    }

    public function test_it_skips_installments_that_are_already_paid(): void
    {
        $debitur = $this->makeDebitur(
            [
                'nominal_pinjaman' => 12_000_000,
                'tenor_bulan' => 12,
                'bungapersen' => 0,
                'tanggal_angsuran_pertama_dibayar' => '2026-01-15',
            ],
            [
                $this->makePayment(1, 1_020_000, 1, '2026-01-15 09:00:00'),
                $this->makePayment(2, 1_020_000, 1, '2026-02-15 09:00:00'),
            ]
        );

        [$summary] = $this->service->build($debitur, '2026-03-10');

        $this->assertSame(3, $summary['next_installment_number']);
        $this->assertSame('2026-03-15', $summary['next_jatuh_tempo_date']);
        $this->assertSame(0, $summary['jumlah_angsuran_menunggak']);
        $this->assertSame(0.0, $summary['total_tunggakan']);
        $this->assertSame(10 * 1_020_000.0, $summary['total_sisa_pinjaman']);
    }

    public function test_partial_payment_is_still_counted_as_arrears(): void
    {
        $debitur = $this->makeDebitur(
            [
                'nominal_pinjaman' => 12_000_000,
                'tenor_bulan' => 12,
                'bungapersen' => 0,
                'tanggal_angsuran_pertama_dibayar' => '2026-01-15',
            ],
            [
                $this->makePayment(1, 500_000, 2, '2026-01-20 09:00:00'),
            ]
        );

        [$summary, $schedule] = $this->service->build($debitur, '2026-01-20');

        $this->assertTrue($schedule[0]['is_overdue']);
        $this->assertTrue($schedule[0]['is_sebagian']);
        $this->assertSame(1_020_000.0 - 500_000.0, $summary['total_tunggakan']);
        $this->assertSame(1, $summary['jumlah_angsuran_menunggak']);
    }

    public function test_fallback_based_on_earliest_payment_is_stable(): void
    {
        $debitur = $this->makeDebitur(
            [
                'nominal_pinjaman' => 12_000_000,
                'tenor_bulan' => 12,
                'bungapersen' => 0,
            ],
            [
                $this->makePayment(1, 1_020_000, 1, '2026-01-05 09:00:00'),
                $this->makePayment(2, 0, 0),
            ]
        );

        [$summaryA, $scheduleA] = $this->service->build($debitur, '2026-02-01');
        [$summaryB, $scheduleB] = $this->service->build($debitur, '2026-03-01');

        $this->assertSame('earliest_payment_date', $summaryA['due_date_basis']);
        $this->assertSame('medium', $summaryA['due_date_reliability']);
        $this->assertSame('2026-01-05', $scheduleA[0]['jatuh_tempo_date']);

        $this->assertSame(
            array_column($scheduleA, 'jatuh_tempo_date'),
            array_column($scheduleB, 'jatuh_tempo_date')
        );

        $this->assertSame('2026-02-05', $summaryA['next_jatuh_tempo_date']);
    }

    public function test_fallback_based_on_created_at(): void
    {
        $debitur = $this->makeDebitur([
            'nominal_pinjaman' => 12_000_000,
            'tenor_bulan' => 12,
            'bungapersen' => 0,
        ]);
        $debitur->created_at = \Carbon\Carbon::parse('2026-01-20 10:00:00', 'UTC');

        [$summary, $schedule] = $this->service->build($debitur, '2026-01-25');

        $this->assertSame('created_at_plus_one_month', $summary['due_date_basis']);
        $this->assertSame('medium', $summary['due_date_reliability']);
        $this->assertSame('2026-02-20', $schedule[0]['jatuh_tempo_date']);
    }

    public function test_it_uses_jakarta_timezone_at_day_boundary(): void
    {
        $debitur = $this->makeDebitur([
            'nominal_pinjaman' => 12_000_000,
            'tenor_bulan' => 12,
            'bungapersen' => 0,
            'tanggal_angsuran_pertama_dibayar' => '2026-01-15',
        ]);

        $today = CarbonImmutable::parse('2026-01-15 00:30:00', 'Asia/Jakarta');

        [$summary, $schedule] = $this->service->build($debitur, $today);

        $this->assertSame('Asia/Jakarta', $summary['timezone']);
        $this->assertSame('2026-01-15', $summary['next_jatuh_tempo_date']);
        $this->assertSame(0, $schedule[0]['hari_telat']);
        $this->assertSame(0, $summary['jumlah_angsuran_menunggak']);
    }

    private function makeDebitur(array $attributes, array $payments = []): MasterPencairanMekar
    {
        $debitur = new MasterPencairanMekar();
        $debitur->forceFill($attributes);
        $debitur->setRelation('pembayaranAngsurans', new Collection($payments));

        return $debitur;
    }

    private function makePayment(int $urutan, float $nominal, int $status, ?string $createdAt = null): PembayaranAngsuran
    {
        $payment = new PembayaranAngsuran();
        $payment->forceFill([
            'urutan_bulan' => $urutan,
            'nominal_bayar' => $nominal,
            'status' => $status,
        ]);

        if ($createdAt !== null) {
            $payment->created_at = \Carbon\Carbon::parse($createdAt, 'UTC');
        }

        return $payment;
    }
}
