<?php

namespace App\Services;

use App\Models\MasterPencairanMekar;
use App\Models\DebiturHistory;
use Illuminate\Support\Facades\Auth;

class DebiturHistoryService
{
    /**
     * Catat aktivitas debitur
     * 
     * @param MasterPencairanMekar $debitur
     * @param string $status_tahap - status yang baru (pending_spv, approved_spv, revisi_spv, reject_spv, etc)
     * @param string|null $catatan - catatan/alasan
     * @param string|null $revisi_dari_tahap - tahap mana yang memberikan revisi
     * @param int|null $revised_by_user_id - user yang memberikan revisi (berbeda dari yang login jika ada)
     * @return DebiturHistory
     */
    public static function recordActivity(
        MasterPencairanMekar $debitur,
        string $status_tahap,
        ?string $catatan = null,
        ?string $revisi_dari_tahap = null,
        ?int $revised_by_user_id = null
    ): DebiturHistory
    {
        $user = Auth::user();
        
        $data = [
            'debitur_id' => $debitur->id,
            'user_id' => $user?->id,
            'status_tahap' => $status_tahap,
            'revisi_dari_tahap' => $revisi_dari_tahap,
            'revised_by_user_id' => $revised_by_user_id,
            'catatan' => $catatan,
        ];

        // Simpan catatan revisi ke kolom yang sesuai dengan role
        if (strpos($status_tahap, 'revisi') !== false) {
            if ($user) {
                if ($user->isSpv() || $user->roles->contains('name', 'SPV')) {
                    $data['catatan_spv'] = $catatan;
                } elseif ($user->isAnalis() || $user->roles->contains('name', 'Analis')) {
                    $data['catatan_analis'] = $catatan;
                } elseif ($user->isRisk() || $user->roles->contains('name', 'Risk')) {
                    $data['catatan_risk'] = $catatan;
                } elseif ($user->isMitra() || $user->roles->contains('name', 'Mitra')) {
                    $data['catatan_mitra'] = $catatan;
                }
            }
        }

        return DebiturHistory::create($data);
    }

    /**
     * Catat ketika marketing submit (Tahap 1)
     */
    public static function recordMarketingSubmit(MasterPencairanMekar $debitur): DebiturHistory
    {
        return self::recordActivity(
            $debitur,
            'pending_spv',
            'Data input baru dari Marketing'
        );
    }

    /**
     * Catat ketika SPV approve
     */
    public static function recordSpvApprove(MasterPencairanMekar $debitur): DebiturHistory
    {
        return self::recordActivity(
            $debitur,
            'approved_spv',
            'Disetujui oleh SPV'
        );
    }

    /**
     * Catat ketika SPV revisi
     */
    public static function recordSpvRevisi(MasterPencairanMekar $debitur, string $alasan): DebiturHistory
    {
        return self::recordActivity(
            $debitur,
            'revisi_spv',
            $alasan,
            'spv'
        );
    }

    /**
     * Catat ketika SPV reject
     */
    public static function recordSpvReject(MasterPencairanMekar $debitur, string $alasan): DebiturHistory
    {
        return self::recordActivity(
            $debitur,
            'reject_spv',
            $alasan
        );
    }

    /**
     * Catat ketika Analis approve
     */
    public static function recordAnalisApprove(MasterPencairanMekar $debitur): DebiturHistory
    {
        return self::recordActivity(
            $debitur,
            'approved_analis',
            'Disetujui oleh Analis'
        );
    }

    /**
     * Catat ketika Analis revisi
     */
    public static function recordAnalisRevisi(MasterPencairanMekar $debitur, string $alasan): DebiturHistory
    {
        return self::recordActivity(
            $debitur,
            'revisi_analis',
            $alasan,
            'analis'
        );
    }

    /**
     * Catat ketika Analis reject
     */
    public static function recordAnalisReject(MasterPencairanMekar $debitur, string $alasan): DebiturHistory
    {
        return self::recordActivity(
            $debitur,
            'reject_analis',
            $alasan
        );
    }

    /**
     * Catat ketika Risk approve
     */
    public static function recordRiskApprove(MasterPencairanMekar $debitur): DebiturHistory
    {
        return self::recordActivity(
            $debitur,
            'approved_risk',
            'Disetujui oleh Risk'
        );
    }

    /**
     * Catat ketika Risk revisi
     */
    public static function recordRiskRevisi(MasterPencairanMekar $debitur, string $alasan): DebiturHistory
    {
        return self::recordActivity(
            $debitur,
            'revisi_risk',
            $alasan,
            'risk'
        );
    }

    /**
     * Catat ketika Risk reject
     */
    public static function recordRiskReject(MasterPencairanMekar $debitur, string $alasan): DebiturHistory
    {
        return self::recordActivity(
            $debitur,
            'reject_risk',
            $alasan
        );
    }

    /**
     * Catat ketika Mitra approve
     */
    public static function recordMitraApprove(MasterPencairanMekar $debitur): DebiturHistory
    {
        return self::recordActivity(
            $debitur,
            'approved_mitra',
            'Disetujui oleh Mitra'
        );
    }

    /**
     * Catat ketika Mitra revisi
     */
    public static function recordMitraRevisi(MasterPencairanMekar $debitur, string $alasan): DebiturHistory
    {
        return self::recordActivity(
            $debitur,
            'revisi_mitra',
            $alasan,
            'mitra'
        );
    }

    /**
     * Catat ketika Mitra reject
     */
    public static function recordMitraReject(MasterPencairanMekar $debitur, string $alasan): DebiturHistory
    {
        return self::recordActivity(
            $debitur,
            'reject_mitra',
            $alasan
        );
    }

    /**
     * Catat ketika marketing edit revisi dan submit kembali
     */
    public static function recordMarketingResubmitRevision(
        MasterPencairanMekar $debitur,
        string $revised_from_tahap
    ): DebiturHistory
    {
        // Map tahap revisi ke tahap berikutnya untuk disubmit kembali
        $nextTahap = match($revised_from_tahap) {
            'revisi_spv' => 'pending_spv',
            'revisi_analis' => 'pending_analis',
            'revisi_risk' => 'pending_risk',
            'revisi_mitra' => 'pending_mitra',
            default => 'pending_spv'
        };

        return self::recordActivity(
            $debitur,
            $nextTahap,
            'Revisi dari ' . ucfirst(str_replace('revisi_', '', $revised_from_tahap)) . ' telah diperbaiki dan disubmit kembali oleh Marketing'
        );
    }
}
