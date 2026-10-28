<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterPencairanMekar;
use App\Constants\RoleConstants;
use App\Exports\DebiturApprovedExport;
use App\Exports\DebiturMitraExport;
use App\Exports\DebiturNominatifExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\AngsuranScheduleService;
use App\Services\DebiturHistoryService;
use Illuminate\Support\Collection;

class DebiturController extends Controller
{
    public function __construct(
        private readonly AngsuranScheduleService $angsuranScheduleService
    ) {
    }

    private function buildUploadFileName(string $nomorKtp, string $collection, string $extension, ?int $index = null): string
    {
        $cleanNomorKtp = preg_replace('/\D+/', '', $nomorKtp) ?: 'FILE';

        $labels = [
            'foto_ktp' => 'KTP',
            'ktp_pasangan' => 'KTP PASANGAN',
            'foto_bpjs' => 'FOTO BPJS',
            'selfie_ktp' => 'FOTO SELFI',
            'kartu_keluarga' => 'KARTU KELUARGA',
            'privy_id_file' => 'PRIVY ID',
            'id_card' => 'ID CARD',
            'slip_gaji' => 'SLIP GAJI',
            'buku_nikah' => 'BUKU NIKAH',
            'dokumen_pendukung' => 'DOKUMEN PENDUKUNG',
            'saldo_jmo' => 'SALDO JMO',
            'mutasi_bank' => 'MUTASI REKENING',
        ];

        $label = $labels[$collection] ?? strtoupper(str_replace('_', ' ', $collection));
        if ($index !== null) {
            $label .= ' ' . ($index + 1);
        }

        return $cleanNomorKtp . '-' . $label . '.' . strtolower($extension);
    }

    public function index()
    {
        return view('apps.ecommerce.debitur');
    }

    public function approvedIndex()
    {
        return view('apps.ecommerce.debitur-approved');
    }

    public function nominativeReport(Request $request)
    {
        $debiturs = $this->getNominativeReportRows($request);

        return view('apps.ecommerce.debitur-laporan-nominatif', compact('debiturs'));
    }

    public function exportNominativeReport(Request $request)
    {
        $rows = $this->getNominativeReportRows($request);

        return Excel::download(
            new DebiturNominatifExport($rows),
            'Laporan_Nominatif_' . date('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    private function getNominativeReportRows(Request $request): Collection
    {
        $user = auth()->user();
        $cabangId = $user->cabang_id;
        $cabang = $cabangId ? \App\Models\Cabang::find($cabangId) : null;
        $isCabangPusat = strtolower($cabang->nama_cabang ?? '') === 'pusat';

        if (!$cabangId) {
            return collect();
        }

        $query = MasterPencairanMekar::with([
            'pembayaranAngsurans',
            'marketing',
            'spv',
            'cabang',
            'kantor',
        ])
            ->whereIn('status_tahap', ['approve', 'approved']);

        if (!$isCabangPusat) {
            $query->where('cabang_id', $cabangId);
        }

        if ($user->isMarketing()) {
            $query->where('marketing_id', $user->id);
        }

        $debiturs = $query->orderByDesc('id')->get();

        return $debiturs->map(function (MasterPencairanMekar $debitur) {
            [$summary, $schedule] = $this->angsuranScheduleService->build($debitur);
            $isLunas = (bool) $debitur->is_lunas
                || (!empty($schedule) && collect($schedule)->every(fn ($item) => !empty($item['is_lunas'])));

            if ($isLunas) {
                return null;
            }

            return ['debitur' => $debitur, 'summary' => $summary];
        })->filter()->values();
    }

    public function exportApproved(Request $request)
    {
        $request->query->set('scope', 'approved');
        $debiturs = $this->getData($request)->getData(true);

        return Excel::download(
            new DebiturApprovedExport($debiturs),
            'Debitur_Approve_' . date('Y-m-d_H-i-s') . '.xlsx'
        );
    }

    public function getApprovedSummary(Request $request)
    {
        $request->query->set('scope', 'approved');
        $approvedRows = collect($this->getData($request)->getData(true));
        $approvedIds = $approvedRows->pluck('id')->all();
        $statusById = $approvedRows->keyBy('id');

        $summary = [
            'jumlah_approve' => $approvedRows->count(),
            'jumlah_aktif' => $approvedRows->where('status_pinjaman', 'aktif')->count(),
            'jumlah_lunas' => $approvedRows->where('status_pinjaman', 'lunas')->count(),
            'jumlah_menunggak' => 0,
            'nominal_angsuran_masuk' => 0,
            'nominal_lunas' => 0,
            'nominal_tunggakan' => 0,
        ];

        if ($approvedIds === []) {
            return response()->json($summary);
        }

        $debiturs = MasterPencairanMekar::with('pembayaranAngsurans')
            ->whereIn('id', $approvedIds)
            ->get();

        foreach ($debiturs as $debitur) {
            $statusPinjaman = $statusById->get($debitur->id)['status_pinjaman'] ?? 'aktif';
            $totalPembayaranDebitur = (float) $debitur->pembayaranAngsurans->sum('nominal_bayar');
            $summary['nominal_angsuran_masuk'] += $totalPembayaranDebitur;

            if ($statusPinjaman === 'lunas') {
                $summary['nominal_lunas'] += $totalPembayaranDebitur;
                continue;
            }

            [$angsuranSummary] = $this->angsuranScheduleService->build($debitur);
            $totalTunggakanDebitur = (float) ($angsuranSummary['total_tunggakan'] ?? 0);

            if ($totalTunggakanDebitur > 0) {
                $summary['jumlah_menunggak']++;
                $summary['nominal_tunggakan'] += $totalTunggakanDebitur;
            }
        }

        $summary['nominal_angsuran_masuk'] = round($summary['nominal_angsuran_masuk'], 2);
        $summary['nominal_lunas'] = round($summary['nominal_lunas'], 2);
        $summary['nominal_tunggakan'] = round($summary['nominal_tunggakan'], 2);

        return response()->json($summary);
    }

    public function getData(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $userCabangId = $user->cabang_id;
        $userKantorId = $user->kantor_id;
        $cabang = $userCabangId ? \App\Models\Cabang::find($userCabangId) : null;
        $kantor = $userKantorId ? \App\Models\Kantor::find($userKantorId) : null;
        $hasCabang = !empty($userCabangId) && strtolower($cabang->nama_cabang ?? '') !== 'pusat';
        $isKantorPusat = !empty($userKantorId) && strtolower($kantor->nama_kantor ?? '') === 'pusat';
        $scope = $request->query('scope', 'default');
        $approvedStatuses = ['approve', 'approved'];
        $draftStatuses = ['draft_step1', 'draft_step2'];

        $query = MasterPencairanMekar::select(
                'master_pencairan_mekar.id',
                'master_pencairan_mekar.nama_peminjam',
                'master_pencairan_mekar.nomor_ktp',
                'master_pencairan_mekar.email',
                'master_pencairan_mekar.nomor_handphone',
                'master_pencairan_mekar.tenor_bulan',
                'master_pencairan_mekar.marketing_id',
                'master_pencairan_mekar.batch',
                'master_pencairan_mekar.kantor_id',
                'users.name as marketing',
                'spv_users.name as spv',
                'cabangs.nama_cabang as nama_cabang',
                'kantors.nama_kantor as unit',
                'master_pencairan_mekar.nominal_pinjaman',
                'master_pencairan_mekar.pengajuan_plafond',
                'master_pencairan_mekar.status_tahap',
                'master_pencairan_mekar.status_verifikasi_data',
                'master_pencairan_mekar.created_at',
                'master_pencairan_mekar.updated_at',
                'master_pencairan_mekar.nomor_debitur'
            )
            ->leftJoin('users', 'master_pencairan_mekar.marketing_id', '=', 'users.id')
            ->leftJoin('users as spv_users', 'master_pencairan_mekar.spv_id', '=', 'spv_users.id')
            ->leftJoin('cabangs', 'master_pencairan_mekar.cabang_id', '=', 'cabangs.id')
            ->leftJoin('kantors', 'master_pencairan_mekar.kantor_id', '=', 'kantors.id');

        if ($scope === 'approved') {
            $query->leftJoinSub(
                \Illuminate\Support\Facades\DB::table('pembayaran_angsurans')
                    ->select('id_debitur')
                    ->selectRaw('COUNT(DISTINCT CASE WHEN status = 1 THEN urutan_bulan END) as paid_installments')
                    ->selectRaw('MIN(CASE WHEN status = 1 THEN urutan_bulan END) as first_paid_month')
                    ->selectRaw('MAX(CASE WHEN status = 1 THEN urutan_bulan END) as last_paid_month')
                    ->groupBy('id_debitur'),
                'payment_summary',
                function ($join) {
                    $join->on('master_pencairan_mekar.id', '=', 'payment_summary.id_debitur');
                }
            )
                ->addSelect(\Illuminate\Support\Facades\DB::raw("CASE WHEN master_pencairan_mekar.is_lunas = 1 OR (master_pencairan_mekar.tenor_bulan > 0 AND COALESCE(payment_summary.paid_installments, 0) >= master_pencairan_mekar.tenor_bulan AND payment_summary.first_paid_month = 1 AND payment_summary.last_paid_month <= master_pencairan_mekar.tenor_bulan) THEN 'lunas' ELSE 'aktif' END as status_pinjaman"));
        }

        // Mitra melihat semua debitur lintas cabang
        $isMitraRole = $user->isMitra();

        if (!$isMitraRole && !$isKantorPusat && $hasCabang) {
            $query->where('master_pencairan_mekar.cabang_id', $userCabangId);
        }

        if (!$isMitraRole && !$isKantorPusat && !empty($userKantorId)) {
            $query->where('master_pencairan_mekar.kantor_id', $userKantorId);
        }

        if ($scope === 'approved') {
            $query->whereIn('master_pencairan_mekar.status_tahap', $approvedStatuses);
        } else {
            $query->where(function ($statusQuery) use ($approvedStatuses) {
                $statusQuery->whereNull('master_pencairan_mekar.status_tahap')
                    ->orWhereNotIn('master_pencairan_mekar.status_tahap', $approvedStatuses);
            });
            $query->where(function ($draftQuery) use ($draftStatuses) {
                $draftQuery->whereNull('master_pencairan_mekar.status_tahap')
                    ->orWhereNotIn('master_pencairan_mekar.status_tahap', $draftStatuses);
            });
        }

        // Filter untuk role marketing: hanya tampilkan debitur yang marketing-nya sesuai dengan nama user
        if ($user->isMarketing()) {
            $query->where('master_pencairan_mekar.marketing_id', $user->id);
        }

        // Filter untuk role spv: hanya tampilkan debitur yang spv-nya sesuai dengan nama user, atau yang belum punya SPV
        if ($user->isSpv()) {
            $query->where(function ($q) use ($user) {
                $q->where('master_pencairan_mekar.spv_id', $user->id)
                  ->orWhereNull('master_pencairan_mekar.spv_id');
            });
        }

        $query->orderBy('master_pencairan_mekar.id', 'desc');

        $debiturs = $query->get();

        return response()->json($debiturs);
    }

    public function tracking()
    {
        return view('apps.ecommerce.debitur-tracking');
    }

    public function getTrackingData(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $userCabangId = $user->cabang_id;
        $cabang = $userCabangId ? \App\Models\Cabang::find($userCabangId) : null;
        $hasCabang = !empty($userCabangId) && strtolower($cabang->nama_cabang ?? '') !== 'pusat';

        $query = MasterPencairanMekar::with(['histories' => function($q) {
            $q->with('user')->orderBy('created_at', 'desc');
        }, 'marketing', 'spv', 'cabang'])
            ->select('id', 'nama_peminjam', 'nomor_ktp', 'created_at', 'status_tahap', 'marketing_id', 'spv_id', 'cabang_id', 'nama_perusahaan as perusahaan');

        // Tracking menampilkan data pengajuan yang masih berjalan (selain approve*/reject*)
        $query->where(function ($q) {
            $q->whereNull('status_tahap')
                ->orWhere(function ($statusQuery) {
                    $statusQuery->where('status_tahap', 'not like', 'approve%')
                        ->where('status_tahap', 'not like', 'reject%');
                });
        });
        $query->where(function ($q) {
            $q->whereNull('status_tahap')
                ->orWhereNotIn('status_tahap', ['draft_step1', 'draft_step2']);
        });

        if ($hasCabang) {
            $query->where('cabang_id', $userCabangId);
        }

        $debiturs = $query->orderByDesc('created_at')->orderByDesc('id')->get();
        return response()->json($debiturs);
    }

    public function details(Request $request)
    {
        $id = $request->query('id');
        $debitur = MasterPencairanMekar::with([
            'pembayaranAngsurans',
            'marketing',
            'kantor',
            'previousLoan.pembayaranAngsurans',
            'renewalLoans.pembayaranAngsurans',
        ])->findOrFail($id);
        $histories = $debitur->histories()->with('user')->orderBy('created_at', 'desc')->get();
        [$summary, $jadwal_angsuran] = $this->angsuranScheduleService->build($debitur);

        $linkedLoans = collect();
        if ($debitur->previousLoan) {
            $linkedLoans->push($this->formatLinkedLoan($debitur->previousLoan));
        }

        foreach ($debitur->renewalLoans->sortBy('id') as $renewalLoan) {
            $linkedLoans->push($this->formatLinkedLoan($renewalLoan));
        }

        return view('apps.ecommerce.debitur-details', compact('debitur', 'jadwal_angsuran', 'summary', 'histories', 'linkedLoans'));
    }

    private function formatLinkedLoan(MasterPencairanMekar $loan): array
    {
        $statusLabel = ucfirst(str_replace('_', ' ', (string) $loan->status_tahap));
        $statusClass = 'bg-secondary-subtle text-secondary';

        if (in_array($loan->status_tahap, ['approve', 'approved'], true)) {
            [$loanSummary, $loanSchedule] = $this->angsuranScheduleService->build($loan);
            $isLunas = (bool) $loan->is_lunas || (!empty($loanSchedule) && collect($loanSchedule)->every(fn ($item) => !empty($item['is_lunas'])));
            if ($isLunas) {
                $statusLabel = 'Lunas';
                $statusClass = 'bg-info-subtle text-info';
            } else {
                $statusLabel = 'Aktif';
                $statusClass = 'bg-success-subtle text-success';
            }
            unset($loanSummary);
        }

        return [
            'id' => $loan->id,
            'nama_peminjam' => $loan->nama_peminjam,
            'nomor_ktp' => $loan->nomor_ktp,
            'status_label' => $statusLabel,
            'status_class' => $statusClass,
            'detail_url' => route('debitur.details', ['id' => $loan->id]),
        ];
    }

    public function printAngsuran($id)
    {
        $debitur = MasterPencairanMekar::with(['pembayaranAngsurans', 'marketing', 'kantor'])->findOrFail($id);

        if (!in_array($debitur->status_tahap, ['approve', 'approved'])) {
            return redirect()->route('debitur.details', ['id' => $id])->with('error', 'Kartu angsuran hanya tersedia untuk debitur yang sudah approve.');
        }

        [$summary, $jadwal_angsuran] = $this->angsuranScheduleService->build($debitur);

        return view('apps.ecommerce.debitur-print-angsuran', compact('debitur', 'summary', 'jadwal_angsuran'));
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'id_debitur' => 'required',
            'urutan_bulan' => 'required|integer',
            'nominal_bayar' => 'required|numeric',
            'status' => 'required|integer',
            'tipe_pembayaran' => 'nullable|integer'
        ]);

        if ($request->tipe_pembayaran == 3) {
            $debitur = MasterPencairanMekar::findOrFail($request->id_debitur);
            
            $nominal_pinjaman = floatval($debitur->nominal_pinjaman ?? 0);
            $tenor_bulan = intval($debitur->tenor_bulan ?? 1);
            if ($tenor_bulan <= 0) $tenor_bulan = 1;
            
            $bungapersen = floatval($debitur->bungapersen ?? 0);
            $pokok = $nominal_pinjaman / $tenor_bulan;
            $jasa = ($nominal_pinjaman * ($bungapersen / 100)) + 20000;
            $total_angsuran = ceil(($pokok + $jasa) / 1000) * 1000;

            for ($i = $request->urutan_bulan; $i <= $tenor_bulan; $i++) {
                \App\Models\PembayaranAngsuran::updateOrCreate(
                    [
                        'id_debitur' => $request->id_debitur,
                        'urutan_bulan' => $i,
                    ],
                    [
                        'nominal_bayar' => $total_angsuran,
                        'status' => 1,
                    ]
                );
            }
        } else {
            \App\Models\PembayaranAngsuran::updateOrCreate(
                [
                    'id_debitur' => $request->id_debitur,
                    'urutan_bulan' => $request->urutan_bulan,
                ],
                [
                    'nominal_bayar' => $request->nominal_bayar,
                    'status' => $request->status,
                ]
            );
        }

        return back()->with('success', 'Pembayaran berhasil disimpan.');
    }

    public function create(Request $request)
    {
        $perusahaans = \App\Models\Perusahaan::orderBy('nama_perusahaan')->get();

        $draftQuery = MasterPencairanMekar::where('marketing_id', auth()->id())
            ->whereIn('status_tahap', ['draft_step1', 'draft_step2']);

        $requestedDraftId = (int) $request->query('draft_id', 0);
        if ($requestedDraftId > 0) {
            $draftDebitur = (clone $draftQuery)->where('id', $requestedDraftId)->first();
        } else {
            $draftDebitur = (clone $draftQuery)->orderByDesc('id')->first();
        }

        $draftMedia = [];
        if ($draftDebitur) {
            $collections = [
                'foto_ktp', 'ktp_pasangan', 'foto_bpjs', 'selfie_ktp', 'kartu_keluarga',
                'privy_id_file', 'id_card', 'slip_gaji', 'buku_nikah', 'dokumen_pendukung'
            ];

            foreach ($collections as $collection) {
                $draftMedia[$collection] = optional($draftDebitur->getFirstMedia($collection))->file_name;
            }

            $draftMedia['mutasi_bank_count'] = $draftDebitur->getMedia('mutasi_bank')->count();
            $draftMedia['dokumen_pendukung_count'] = $draftDebitur->getMedia('dokumen_pendukung')->count();
        }

        return view('apps.ecommerce.debitur-create', compact('perusahaans', 'draftDebitur', 'draftMedia'));
    }

    public function reapply($id)
    {
        $sourceDebitur = MasterPencairanMekar::with('pembayaranAngsurans')->findOrFail($id);

        if (!in_array($sourceDebitur->status_tahap, ['approve', 'approved'], true)) {
            return back()->with('error', 'Pengajuan kembali hanya tersedia untuk pinjaman yang sudah approve.');
        }

        [$summary, $jadwalAngsuran] = $this->angsuranScheduleService->build($sourceDebitur);
        $isLunas = !empty($jadwalAngsuran) && collect($jadwalAngsuran)->every(fn ($item) => !empty($item['is_lunas']));

        if (!$isLunas) {
            return back()->with('error', 'Pengajuan kembali hanya bisa dilakukan jika pinjaman sebelumnya sudah lunas.');
        }

        $cloneData = $sourceDebitur->toArray();

        $skipColumns = [
            'id', 'status_tahap', 'no_kontrak', 'nomor_debitur', 'nomor_spk', 'tanggal_akad_kredit',
            'tanggal_angsuran_pertama_dibayar', 'tanggal_jatuh_tempo', 'status_verifikasi_data',
            'status_penurunan_plafond', 'status_takeout', 'alasan_penurunan', 'keterangan_takeout',
            'alasan_revisi', 'pengajuan_plafond_awal', 'nominal_pinjaman', 'total_pinjaman',
            'total_angsuran', 'previous_loan_id'
        ];

        foreach ($skipColumns as $column) {
            unset($cloneData[$column]);
        }

        // Buat pengajuan baru dari debitur yang sama, tetap menyimpan histori pinjaman lama.
        $cloneData['status_tahap'] = 'draft_step1';
        $cloneData['previous_loan_id'] = $sourceDebitur->id;
        $cloneData['marketing_id'] = auth()->id();
        if (empty($cloneData['nama_marketing'])) {
            $cloneData['nama_marketing'] = auth()->user()->name;
        }

        $newDraft = MasterPencairanMekar::create($cloneData);

        return redirect()
            ->route('debitur.create', ['draft_id' => $newDraft->id])
            ->with('success', 'Draft pengajuan kembali berhasil dibuat. Silakan lengkapi dan submit.');
    }

    private function findOwnedDraftDebitur(int $id, array $allowedStatuses = ['draft_step1', 'draft_step2']): MasterPencairanMekar
    {
        $debitur = MasterPencairanMekar::findOrFail($id);

        if ((int) $debitur->marketing_id !== (int) auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke draft ini.');
        }

        if (!in_array($debitur->status_tahap, $allowedStatuses, true)) {
            abort(422, 'Draft tidak dapat diubah pada tahap ini.');
        }

        return $debitur;
    }

    public function storeStep1(Request $request)
    {
        $draftId = $request->input('debitur_id');
        $previousLoanId = $request->input('previous_loan_id');
        $ignoreId = null;

        if (!empty($draftId)) {
            $draftDebitur = $this->findOwnedDraftDebitur((int) $draftId);
            $ignoreId = $draftDebitur->id;
        } elseif (!empty($previousLoanId)) {
            $ignoreId = (int) $previousLoanId;
        }

        $request->validate([
            'nama_peminjam' => 'required|string|max:255',
            'nomor_ktp' => 'required|digits:16|numeric|unique:master_pencairan_mekar,nomor_ktp,' . $ignoreId,
            'email' => 'required|email|max:255|unique:master_pencairan_mekar,email,' . $ignoreId,
            'nomor_handphone' => 'required|numeric',
            'nama_ibu_kandung' => 'required|string|max:255',
            'status_perkawinan' => 'required|in:Belum Menikah,Menikah,Cerai',
            'nama_pasangan' => 'required_if:status_perkawinan,Menikah|nullable|string|max:255',
            'nik_pasangan' => 'required_if:status_perkawinan,Menikah|nullable|digits:16|numeric|unique:master_pencairan_mekar,nik_pasangan,' . $ignoreId,
            'nomor_hp_pasangan' => 'required_if:status_perkawinan,Menikah|nullable|string|max:50',
            'nomor_npwp' => 'nullable|numeric',
            'pengajuan_plafond' => 'required|numeric|min:0',
            'tenor_bulan' => 'required|in:6,10,12,24',
            'nama_bank' => 'required|string|max:255',
            'nomer_rekening' => 'nullable|numeric',
            'alamat_ktp' => 'required|string|max:500',
            'kode_provinsi' => 'required',
            'kode_kabupaten' => 'required',
            'kode_kecamatan' => 'required',
            'kode_kelurahan' => 'required',
        ]);

        $data = $request->except([
            '_token', '_method', 'debitur_id', 'foto_ktp', 'ktp_pasangan', 'foto_bpjs', 'selfie_ktp',
            'kartu_keluarga', 'privy_id_file', 'id_card', 'slip_gaji', 'buku_nikah', 'dokumen_pendukung', 'saldo_jmo', 'mutasi_bank'
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        if (empty($data['nama_marketing'])) {
            $data['nama_marketing'] = $user->name;
        }
        if (empty($data['marketing_id'])) {
            $data['marketing_id'] = $user->id;
        }
        $data['spv_id'] = $user->spv_id;
        if (!empty($user->cabang_id) && empty($data['cabang_id'])) {
            $data['cabang_id'] = $user->cabang_id;
        }
        if (!empty($user->kantor_id) && empty($data['kantor_id'])) {
            $data['kantor_id'] = $user->kantor_id;
        }
        $data['status_tahap'] = 'draft_step1';

        if (!empty($draftId)) {
            $debitur = $this->findOwnedDraftDebitur((int) $draftId);
            $debitur->update($data);
        } else {
            $debitur = MasterPencairanMekar::create($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Step 1 berhasil disimpan.',
            'debitur_id' => $debitur->id,
            'next_step' => 2,
        ]);
    }

    public function storeStep2(Request $request, $id)
    {
        $debitur = $this->findOwnedDraftDebitur((int) $id, ['draft_step1', 'draft_step2']);

        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'alamat_domisili' => 'required|string|max:500',
            'kode_provinsi_domisili' => 'required',
            'kode_kabupaten_domisili' => 'required',
            'kode_kecamatan_domisili' => 'required',
            'kode_kelurahan_domisili' => 'required',
            'nama_keluarga_tidak_serumah' => 'required|string|max:255',
            'no_handphone_keluarga' => 'required|string|max:50',
        ]);

        $data = $request->except([
            '_token', '_method', 'debitur_id', 'foto_ktp', 'ktp_pasangan', 'foto_bpjs', 'selfie_ktp',
            'kartu_keluarga', 'privy_id_file', 'id_card', 'slip_gaji', 'buku_nikah', 'dokumen_pendukung', 'mutasi_bank'
        ]);
        $data['status_tahap'] = 'draft_step2';

        $debitur->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Step 2 berhasil disimpan.',
            'debitur_id' => $debitur->id,
            'next_step' => 3,
        ]);
    }

    public function storeStep3(Request $request, $id)
    {
        $debitur = $this->findOwnedDraftDebitur((int) $id, ['draft_step2']);

        $fotoKtpRule = $debitur->hasMedia('foto_ktp') ? 'nullable' : 'required';
        $fotoBpjsRule = $debitur->hasMedia('foto_bpjs') ? 'nullable' : 'required';
        $selfieKtpRule = $debitur->hasMedia('selfie_ktp') ? 'nullable' : 'required';
        $ktpPasanganRule = ($debitur->status_perkawinan === 'Menikah' && !$debitur->hasMedia('ktp_pasangan')) ? 'required' : 'nullable';

        $request->validate([
            'pendapatan_per_bulan' => 'required|numeric|min:0',
            'pengeluaran_per_bulan' => 'nullable|numeric|min:0',
            'hutang_per_bulan' => 'nullable|numeric|min:0',
            'foto_ktp' => $fotoKtpRule . '|file|mimes:jpg,jpeg,png,pdf',
            'ktp_pasangan' => $ktpPasanganRule . '|file|mimes:jpg,jpeg,png,pdf',
            'foto_bpjs' => $fotoBpjsRule . '|file|mimes:jpg,jpeg,png,pdf',
            'selfie_ktp' => $selfieKtpRule . '|file|mimes:jpg,jpeg,png',
            'kartu_keluarga' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'privy_id_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'id_card' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'slip_gaji' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'buku_nikah' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'dokumen_pendukung' => 'nullable|array',
            'dokumen_pendukung.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10120',
            'saldo_jmo' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'mutasi_bank' => 'nullable|array',
            'mutasi_bank.*' => 'file|mimes:jpg,jpeg,png,pdf',
        ]);

        $data = $request->except([
            '_token', '_method', 'debitur_id', 'foto_ktp', 'ktp_pasangan', 'foto_bpjs', 'selfie_ktp',
            'kartu_keluarga', 'privy_id_file', 'id_card', 'slip_gaji', 'buku_nikah', 'dokumen_pendukung', 'mutasi_bank'
        ]);
        $data['status_tahap'] = 'pending_spv';

        $debitur->update($data);
        $nomorKtpForFileName = (string) ($debitur->nomor_ktp ?? $request->input('nomor_ktp', ''));

        $mediaCollections = ['foto_ktp', 'ktp_pasangan', 'foto_bpjs', 'selfie_ktp', 'kartu_keluarga', 'privy_id_file', 'id_card', 'slip_gaji', 'buku_nikah', 'saldo_jmo'];
        foreach ($mediaCollections as $collection) {
            if ($request->hasFile($collection) && $request->file($collection)->isValid()) {
                $ext = $request->file($collection)->getClientOriginalExtension();
                $fileName = $this->buildUploadFileName($nomorKtpForFileName, $collection, $ext);
                $debitur->clearMediaCollection($collection);
                $debitur->addMediaFromRequest($collection)
                        ->usingFileName($fileName)
                        ->toMediaCollection($collection);
            }
        }

        if ($request->hasFile('dokumen_pendukung')) {
            $debitur->clearMediaCollection('dokumen_pendukung');
            $files = $request->file('dokumen_pendukung');
            if (is_array($files)) {
                foreach ($files as $index => $file) {
                    if ($file && $file->isValid()) {
                        $ext = $file->getClientOriginalExtension();
                        $fileName = $this->buildUploadFileName($nomorKtpForFileName, 'dokumen_pendukung', $ext, $index);
                        $debitur->addMedia($file)->usingFileName($fileName)->toMediaCollection('dokumen_pendukung');
                    }
                }
            }
        }

        if ($request->hasFile('mutasi_bank')) {
            $debitur->clearMediaCollection('mutasi_bank');
            $files = $request->file('mutasi_bank');
            if (is_array($files)) {
                foreach ($files as $index => $file) {
                    if ($file && $file->isValid()) {
                        $ext = $file->getClientOriginalExtension();
                        $fileName = $this->buildUploadFileName($nomorKtpForFileName, 'mutasi_bank', $ext, $index);
                        $debitur->addMedia($file)->usingFileName($fileName)->toMediaCollection('mutasi_bank');
                    }
                }
            }
        }

        DebiturHistoryService::recordMarketingSubmit($debitur);

        return response()->json([
            'success' => true,
            'message' => 'Step 3 berhasil disimpan. Data debitur dikirim untuk approval SPV.',
            'debitur_id' => $debitur->id,
            'redirect_url' => route('debitur.index'),
        ]);
    }

    public function resetDraft(Request $request)
    {
        $draftId = $request->input('debitur_id');

        $query = MasterPencairanMekar::where('marketing_id', auth()->id())
            ->whereIn('status_tahap', ['draft_step1', 'draft_step2']);

        if (!empty($draftId)) {
            $query->where('id', (int) $draftId);
        }

        $draftDebitur = $query->orderByDesc('id')->first();

        if (!$draftDebitur) {
            return response()->json([
                'success' => false,
                'message' => 'Draft tidak ditemukan atau sudah dihapus.',
            ], 404);
        }

        $draftDebitur->clearMediaCollection('foto_ktp');
        $draftDebitur->clearMediaCollection('ktp_pasangan');
        $draftDebitur->clearMediaCollection('foto_bpjs');
        $draftDebitur->clearMediaCollection('selfie_ktp');
        $draftDebitur->clearMediaCollection('kartu_keluarga');
        $draftDebitur->clearMediaCollection('privy_id_file');
        $draftDebitur->clearMediaCollection('id_card');
        $draftDebitur->clearMediaCollection('slip_gaji');
        $draftDebitur->clearMediaCollection('buku_nikah');
        $draftDebitur->clearMediaCollection('dokumen_pendukung');
        $draftDebitur->clearMediaCollection('saldo_jmo');
        $draftDebitur->clearMediaCollection('mutasi_bank');

        $draftDebitur->delete();

        return response()->json([
            'success' => true,
            'message' => 'Draft berhasil direset. Anda dapat mulai input dari Step 1.',
        ]);
    }

    private function blockMarketingAndSpv()
    {
        $user = auth()->user();
        if ($user) {
            $jabatan = $user->jabatan ? strtolower($user->jabatan->nama_jabatan) : '';
            if (in_array(strtolower($user->level), ['marketing', 'spv']) 
                || strpos($jabatan, 'marketing') !== false 
                || strpos($jabatan, 'spv') !== false
                || $user->hasAnyRoleById([RoleConstants::MARKETING_ID, RoleConstants::SPV_ID])) {
                return true;
            }
        }
        return false;
    }

    private function blockNonMitra()
    {
        $user = auth()->user();
        if ($user) {
            $jabatan = $user->jabatan ? strtolower($user->jabatan->nama_jabatan) : '';
            if (in_array(strtolower($user->level), ['mitra']) 
                || strpos($jabatan, 'mitra') !== false
                || $user->hasAnyRoleById([RoleConstants::MITRA_ID])) {
                return false; // Mitra role is allowed
            }
        }
        return true; // Block non-Mitra
    }

    private function isAdminKredit()
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        $jabatan = $user->jabatan ? strtolower($user->jabatan->nama_jabatan) : '';
        $normalizedLevel = str_replace(' ', '', strtolower($user->level));
        $normalizedJabatan = str_replace(' ', '', strtolower($jabatan));

        return in_array($normalizedLevel, ['admin', 'adminkredit', 'admin_kredit'])
            || strpos($normalizedJabatan, 'adminkredit') !== false
            || $user->hasAnyRoleById([RoleConstants::ADMIN_ID, RoleConstants::ADMINKREDIT_ID]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_peminjam' => 'required|string|max:255',
            'nomor_ktp' => 'required|digits:16|numeric|unique:master_pencairan_mekar,nomor_ktp',
            'email' => 'required|email|max:255|unique:master_pencairan_mekar,email',
            'nomor_handphone' => 'required|numeric',
            'nama_ibu_kandung' => 'required|string|max:255',
            'status_perkawinan' => 'required|in:Belum Menikah,Menikah,Cerai',
            'nama_perusahaan' => 'required|string|max:255',
            'nama_bank' => 'required|string|max:255',
            'foto_ktp' => 'required|file|mimes:jpg,jpeg,png,pdf',
            'ktp_pasangan' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'foto_bpjs' => 'required|file|mimes:jpg,jpeg,png,pdf',
            'selfie_ktp' => 'required|file|mimes:jpg,jpeg,png',
            'kartu_keluarga' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'nama_pasangan' => 'nullable|string|max:255',
            'nik_pasangan' => 'nullable|digits:16|numeric|unique:master_pencairan_mekar,nik_pasangan',
            'nomor_hp_pasangan' => 'nullable|string|max:50',
            'nomor_npwp' => 'nullable|numeric',
            'pengajuan_plafond' => 'required|numeric|min:0',
            'nomer_rekening' => 'nullable|numeric',
            'alamat_domisili' => 'required|string|max:500',
            'nama_keluarga_tidak_serumah' => 'required|string|max:255',
            'no_handphone_keluarga' => 'required|string|max:50',
            'privy_id_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'id_card' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'slip_gaji' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'buku_nikah' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'dokumen_pendukung' => 'nullable|array',
            'dokumen_pendukung.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10120',
            'saldo_jmo' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'mutasi_bank' => 'nullable|array',
            'mutasi_bank.*' => 'file|mimes:jpg,jpeg,png,pdf',
        ], [
            'nomor_ktp.required' => 'Nomor KTP wajib diisi.',
            'nomor_ktp.digits' => 'Nomor KTP harus 16 digit.',
            'nomor_ktp.numeric' => 'Nomor KTP harus berupa angka.',
            'nomor_ktp.unique' => 'Nomor KTP sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'nomor_handphone.required' => 'Nomor handphone wajib diisi.',
            'nomor_handphone.numeric' => 'Nomor handphone harus berupa angka.',
            'nik_pasangan.digits' => 'NIK pasangan harus 16 digit.',
            'nik_pasangan.numeric' => 'NIK pasangan harus berupa angka.',
            'nik_pasangan.unique' => 'NIK pasangan sudah terdaftar.',
            'nomor_npwp.numeric' => 'Nomor NPWP harus berupa angka.',
            'pengajuan_plafond.required' => 'Pengajuan plafond wajib diisi.',
            'pengajuan_plafond.numeric' => 'Pengajuan plafond harus berupa angka.',
            'nomer_rekening.numeric' => 'Nomor rekening harus berupa angka.',
            'nama_ibu_kandung.required' => 'Nama ibu kandung wajib diisi.',
            'status_perkawinan.required' => 'Status perkawinan wajib dipilih.',
            'status_perkawinan.in' => 'Status perkawinan tidak valid.',
            'nama_perusahaan.required' => 'Nama perusahaan wajib dipilih.',
            'nama_bank.required' => 'Nama bank wajib diisi.',
            'foto_ktp.required' => 'Foto KTP debitur wajib diupload.',
            'ktp_pasangan.required_if' => 'KTP pasangan wajib diupload jika status menikah.',
            'foto_bpjs.required' => 'Foto kartu BPJS wajib diupload.',
            'selfie_ktp.required' => 'Selfie dengan KTP wajib diupload.',
            'nama_pasangan.required_if' => 'Nama pasangan wajib diisi jika status menikah.',
            'nik_pasangan.required_if' => 'NIK pasangan wajib diisi jika status menikah.',
            'nomor_hp_pasangan.required_if' => 'No handphone pasangan wajib diisi jika status menikah.',
            'alamat_domisili.required' => 'Alamat domisili wajib diisi.',
            'nama_keluarga_tidak_serumah.required' => 'Nama keluarga wajib diisi.',
            'no_handphone_keluarga.required' => 'No handphone keluarga wajib diisi.',
        ]);

        $data = $request->except(['_token', 'foto_ktp', 'ktp_pasangan', 'foto_bpjs', 'selfie_ktp', 'kartu_keluarga', 'privy_id_file', 'id_card', 'slip_gaji', 'buku_nikah', 'dokumen_pendukung', 'mutasi_bank']);
        $data['status_tahap'] = 'pending_spv';
        
        $user = \Illuminate\Support\Facades\Auth::user();
        if (empty($data['nama_marketing'])) {
            $data['nama_marketing'] = $user->name;
        }
        if (empty($data['marketing_id'])) {
            $data['marketing_id'] = $user->id;
        }
        
        // Auto assign SPV based on the logged-in Marketing's linked SPV
        $data['spv_id'] = $user->spv_id;
        if (!empty($user->cabang_id) && empty($data['cabang_id'])) {
            $data['cabang_id'] = $user->cabang_id;
        }
        if (!empty($user->kantor_id) && empty($data['kantor_id'])) {
            $data['kantor_id'] = $user->kantor_id;
        }

        $debitur = MasterPencairanMekar::create($data);
        $nomorKtpForFileName = (string) ($data['nomor_ktp'] ?? $request->input('nomor_ktp', ''));

        // Upload dokumen ke Spatie Media Library
        $mediaCollections = ['foto_ktp', 'ktp_pasangan', 'foto_bpjs', 'selfie_ktp', 'kartu_keluarga', 'privy_id_file', 'id_card', 'slip_gaji', 'buku_nikah', 'dokumen_pendukung', 'saldo_jmo'];
        foreach ($mediaCollections as $collection) {
            if ($request->hasFile($collection) && $request->file($collection)->isValid()) {
                $ext = $request->file($collection)->getClientOriginalExtension();
            $fileName = $this->buildUploadFileName($nomorKtpForFileName, $collection, $ext);
                $debitur->addMediaFromRequest($collection)
                        ->usingFileName($fileName)
                        ->toMediaCollection($collection);
            }
        }

        if ($request->hasFile('dokumen_pendukung')) {
            $debitur->clearMediaCollection('dokumen_pendukung');
            $files = $request->file('dokumen_pendukung');
            if (is_array($files)) {
                foreach ($files as $index => $file) {
                    if ($file && $file->isValid()) {
                        $ext = $file->getClientOriginalExtension();
                        $fileName = $this->buildUploadFileName($nomorKtpForFileName, 'dokumen_pendukung', $ext, $index);
                        $debitur->addMedia($file)->usingFileName($fileName)->toMediaCollection('dokumen_pendukung');
                    }
                }
            }
        }

        if ($request->hasFile('mutasi_bank')) {
            $files = $request->file('mutasi_bank');
            if (is_array($files)) {
                foreach ($files as $index => $file) {
                    if ($file && $file->isValid()) {
                        $ext = $file->getClientOriginalExtension();
                        $fileName = $this->buildUploadFileName($nomorKtpForFileName, 'mutasi_bank', $ext, $index);
                        $debitur->addMedia($file)->usingFileName($fileName)->toMediaCollection('mutasi_bank');
                    }
                }
            }
        }

        // Catat aktivitas: Marketing submit data baru
        DebiturHistoryService::recordMarketingSubmit($debitur);

        return redirect()->route('debitur.index')->with('success', 'Debitur berhasil disimpan. Menunggu approval SPV.');
    }

    public function edit($id)
    {
        $debitur = MasterPencairanMekar::findOrFail($id);
        // Allow viewing edit form for any status. Validation is done in updateMarketing()
        $perusahaans = \App\Models\Perusahaan::orderBy('nama_perusahaan')->get();
        $histories = $debitur->histories()->with('user')->orderBy('created_at', 'desc')->get();
        return view('apps.ecommerce.debitur-edit-marketing', compact('debitur', 'perusahaans', 'histories'));
    }

    public function updateMarketing(Request $request, $id)
    {
        $debitur = MasterPencairanMekar::findOrFail($id);
        if (!in_array($debitur->status_tahap, ['revisi', 'revisi_spv', 'revisi_analis', 'revisi_risk', 'revisi_mitra'])) {
            return back()->with('error', 'Hanya data dengan status revisi yang dapat diedit.');
        }

        $request->validate([
            'nomor_ktp' => 'required|digits:16|numeric|unique:master_pencairan_mekar,nomor_ktp,' . $debitur->id,
            'email' => 'required|email|max:255|unique:master_pencairan_mekar,email,' . $debitur->id,
            'nomor_handphone' => 'required|numeric',
            'nik_pasangan' => 'nullable|digits:16|numeric|unique:master_pencairan_mekar,nik_pasangan,' . $debitur->id,
            'nomor_npwp' => 'nullable|numeric',
            'pengajuan_plafond' => 'required|numeric|min:0',
            'nomer_rekening' => 'nullable|numeric',
            'foto_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'ktp_pasangan' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'foto_bpjs' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'selfie_ktp' => 'nullable|file|mimes:jpg,jpeg,png',
            'kartu_keluarga' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'privy_id_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'id_card' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'slip_gaji' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'buku_nikah' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'dokumen_pendukung' => 'nullable|array',
            'dokumen_pendukung.*' => 'file|mimes:jpg,jpeg,png,pdf|max:10120',
            'saldo_jmo' => 'nullable|file|mimes:jpg,jpeg,png,pdf',
            'mutasi_bank' => 'nullable|array',
            'mutasi_bank.*' => 'file|mimes:jpg,jpeg,png,pdf',
        ], [
            'nomor_ktp.required' => 'Nomor KTP wajib diisi.',
            'nomor_ktp.digits' => 'Nomor KTP harus 16 digit.',
            'nomor_ktp.numeric' => 'Nomor KTP harus berupa angka.',
            'nomor_ktp.unique' => 'Nomor KTP sudah terdaftar.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'nomor_handphone.required' => 'Nomor handphone wajib diisi.',
            'nomor_handphone.numeric' => 'Nomor handphone harus berupa angka.',
            'nik_pasangan.digits' => 'NIK pasangan harus 16 digit.',
            'nik_pasangan.numeric' => 'NIK pasangan harus berupa angka.',
            'nik_pasangan.unique' => 'NIK pasangan sudah terdaftar.',
            'nomor_npwp.numeric' => 'Nomor NPWP harus berupa angka.',
            'pengajuan_plafond.required' => 'Pengajuan plafond wajib diisi.',
            'pengajuan_plafond.numeric' => 'Pengajuan plafond harus berupa angka.',
            'nomer_rekening.numeric' => 'Nomor rekening harus berupa angka.',
        ]);

        $data = $request->except([
            '_token', '_method',
            'foto_ktp', 'ktp_pasangan', 'foto_bpjs', 'selfie_ktp',
            'kartu_keluarga', 'privy_id_file', 'id_card', 'slip_gaji',
            'buku_nikah', 'dokumen_pendukung', 'saldo_jmo', 'mutasi_bank'
        ]);
        
        // Tentukan status tahap berikutnya berdasarkan tahap revisi sebelumnya
        $previousStatus = $debitur->status_tahap;
        if ($previousStatus === 'revisi_spv') {
            $data['status_tahap'] = 'pending_spv';
            $nextTahap = 'revisi_spv';
        } elseif ($previousStatus === 'revisi_analis' || $previousStatus === 'revisi') {
            $data['status_tahap'] = 'pending_analis';
            $nextTahap = 'revisi_analis';
        } elseif ($previousStatus === 'revisi_risk') {
            $data['status_tahap'] = 'pending_risk';
            $nextTahap = 'revisi_risk';
        } elseif ($previousStatus === 'revisi_mitra') {
            $data['status_tahap'] = 'pending_mitra';
            $nextTahap = 'revisi_mitra';
        } else {
            $data['status_tahap'] = 'pending_spv';
            $nextTahap = 'revisi_spv';
        }
        
        $data['alasan_revisi'] = null; // reset
        
        // Resolve SPV safely: user SPV -> marketing SPV -> existing debitur SPV
        $user = auth()->user();
        $marketingId = $data['marketing_id'] ?? $debitur->marketing_id;
        $marketingUser = $marketingId ? \App\Models\User::find($marketingId) : null;
        $data['spv_id'] = $user->spv_id ?: ($marketingUser->spv_id ?? null) ?: $debitur->spv_id;
        
        $debitur->update($data);
        $nomorKtpForFileName = (string) ($data['nomor_ktp'] ?? $debitur->nomor_ktp ?? $request->input('nomor_ktp', ''));

        // Upload dokumen ke Spatie Media Library
        $mediaCollections = [
            'foto_ktp', 'ktp_pasangan', 'foto_bpjs', 'selfie_ktp',
            'kartu_keluarga', 'privy_id_file', 'id_card', 'slip_gaji',
            'buku_nikah', 'saldo_jmo'
        ];
        foreach ($mediaCollections as $collection) {
            $uploadedFile = $request->file($collection);
            if ($request->hasFile($collection) && $uploadedFile && $uploadedFile->isValid()) {
                $ext = $uploadedFile->getClientOriginalExtension();
                $fileName = $this->buildUploadFileName($nomorKtpForFileName, $collection, $ext);
                $debitur->clearMediaCollection($collection);
                $debitur->addMediaFromRequest($collection)
                        ->usingFileName($fileName)
                        ->toMediaCollection($collection);
            }
        }

        if ($request->hasFile('dokumen_pendukung')) {
            $debitur->clearMediaCollection('dokumen_pendukung');
            $files = $request->file('dokumen_pendukung');
            if (is_array($files)) {
                foreach ($files as $index => $file) {
                    if ($file && $file->isValid()) {
                        $ext = $file->getClientOriginalExtension();
                        $fileName = $this->buildUploadFileName($nomorKtpForFileName, 'dokumen_pendukung', $ext, $index);
                        $debitur->addMedia($file)
                                ->usingFileName($fileName)
                                ->toMediaCollection('dokumen_pendukung');
                    }
                }
            }
        }

        if ($request->hasFile('mutasi_bank')) {
            $debitur->clearMediaCollection('mutasi_bank');
            $files = $request->file('mutasi_bank');
            if (is_array($files)) {
                foreach ($files as $index => $file) {
                    if ($file && $file->isValid()) {
                        $ext = $file->getClientOriginalExtension();
                        $fileName = $this->buildUploadFileName($nomorKtpForFileName, 'mutasi_bank', $ext, $index);
                        $debitur->addMedia($file)
                                ->usingFileName($fileName)
                                ->toMediaCollection('mutasi_bank');
                    }
                }
            }
        }

        // Catat aktivitas: Marketing submit revisi
        DebiturHistoryService::recordMarketingResubmitRevision($debitur, $nextTahap);

        return redirect()->route('debitur.index')->with('success', 'Data revisi berhasil diperbarui dan dikirim kembali.');
    }

    public function editTahap1($id)
    {
        $debitur = MasterPencairanMekar::findOrFail($id);
        
        $hasAccess = false;
        try {
            $hasAccess = auth()->user()->isSpv();
        } catch (\Exception $e) { /* default to false */ }

        if (!$hasAccess) {
            return back()->with('error', 'Akses ditolak. Hanya peran SPV yang memiliki izin untuk mereview Tahap 1.');
        }

        return view('apps.ecommerce.debitur-edit-tahap1', compact('debitur'));
    }

    // Method editTahap2 dan updateTahap2 dinonaktifkan karena admin kredit sudah tidak ada dalam flow
    /*
    public function editTahap2($id)
    {
        if ($this->blockMarketingAndSpv()) return back()->with('error', 'Akses ditolak. Tahap 2 hanya untuk departemen terkait.');
        if (!$this->isAdminKredit()) return back()->with('error', 'Akses ditolak. Tahap 2 hanya untuk Admin Kredit.');
        $debitur = MasterPencairanMekar::findOrFail($id);
        return view('apps.ecommerce.debitur-edit-tahap2', compact('debitur'));
    }

    public function updateTahap2(Request $request, $id)
    {
        if ($this->blockMarketingAndSpv()) return back()->with('error', 'Akses ditolak.');
        if (!$this->isAdminKredit()) return back()->with('error', 'Akses ditolak. Tahap 2 hanya untuk Admin Kredit.');
        $debitur = MasterPencairanMekar::findOrFail($id);
        $data = $request->except([
            '_token', '_method', 'action_type',
            'kode_provinsi_domisili', 'kode_kabupaten_domisili', 'kode_kecamatan_domisili', 'kode_kelurahan_domisili',
            'kode_provinsi_ktp_keluarga', 'kode_kabupaten_ktp_keluarga', 'kode_kecamatan_ktp_keluarga', 'kode_kelurahan_ktp_keluarga',
            'privy_id_file', 'id_card', 'mutasi_bank', 'slip_gaji', 'buku_nikah', 'dokumen_pendukung'
        ]);

        $data['alamat_ktp_keluarga'] = $request->input('alamat_ktp_keluarga');
        
        $action = $request->input('action_type');
        if ($action == 'reject') {
            $data['status_tahap'] = 'reject';
            $msg = 'Pengajuan ditolak secara permanen pada Tahap 2.';
        } elseif ($action == 'revisi') {
            $data['status_tahap'] = 'revisi';
            $data['alasan_revisi'] = $request->input('alasan_revisi');
            $msg = 'Pengajuan dikembalikan ke Tahap 1 untuk direvisi.';
        } else {
            $data['status_tahap'] = 'pending_analis';
            $msg = 'Tahap 2 berhasil disimpan. Menunggu proses Analis.';
        }

        $debitur->update($data);

        $mediaCollections = [
            'foto_ktp', 'ktp_pasangan', 'foto_bpjs', 'selfie_ktp',
            'privy_id_file', 'id_card', 'slip_gaji', 'buku_nikah', 'dokumen_pendukung',
        ];
        foreach ($mediaCollections as $collection) {
            if ($request->hasFile($collection) && $request->file($collection)->isValid()) {
                $debitur->clearMediaCollection($collection);
                $debitur->addMediaFromRequest($collection)
                        ->toMediaCollection($collection);
            }
        }

        if ($request->hasFile('mutasi_bank')) {
            $debitur->clearMediaCollection('mutasi_bank');
            $files = $request->file('mutasi_bank');
            if (is_array($files)) {
                foreach ($files as $file) {
                    if ($file && $file->isValid()) {
                        $debitur->addMedia($file)->toMediaCollection('mutasi_bank');
                    }
                }
            }
        }

        return redirect()->route('debitur.index')->with('success', $msg);
    }
    */

    public function editTahap3Analis($id)
    {
        if ($this->blockMarketingAndSpv()) return back()->with('error', 'Akses ditolak. Tahap Analis hanya untuk departemen terkait.');
        $debitur = MasterPencairanMekar::findOrFail($id);
        $analisa = \App\Models\AnalisaKeuangan::where('debitur_id', $debitur->id)->first();
        
        if (!$analisa || empty($analisa->nomor_mpp)) {
            $nomorMppAuto = 'MPP' . $debitur->id . $debitur->cabang_id . date('Y') . str_pad($debitur->id, 4, '0', STR_PAD_LEFT);
            if (!$analisa) {
                $analisa = new \App\Models\AnalisaKeuangan();
            }
            $analisa->nomor_mpp = $nomorMppAuto;
        }

        $parameterProduks = \App\Models\ParameterProduk::all();
        $cabangs = \App\Models\Cabang::all();
        $users = \App\Models\User::all();
        return view('apps.ecommerce.debitur-edit-tahap3-analis', compact('debitur', 'analisa', 'parameterProduks', 'cabangs', 'users'));
    }

    public function updateTahap3Analis(Request $request, $id)
    {
        if ($this->blockMarketingAndSpv()) return back()->with('error', 'Akses ditolak.');
        $debitur = MasterPencairanMekar::findOrFail($id);
        
        $action = $request->input('action_type');
        if ($action == 'reject') {
            $debitur->update(['status_tahap' => 'reject_analis']);
            $msg = 'Pengajuan ditolak secara permanen pada Tahap Analis.';
            
            // Catat aktivitas: Analis reject
            DebiturHistoryService::recordAnalisReject($debitur, $request->input('alasan_revisi') ?? 'Ditolak oleh Analis');
        } elseif ($action == 'revisi') {
            $debitur->update([
                'status_tahap' => 'revisi_analis',
                'alasan_revisi' => $request->input('alasan_revisi')
            ]);
            $msg = 'Pengajuan dikembalikan ke Marketing untuk direvisi.';
            
            // Catat aktivitas: Analis revisi dengan catatan
            DebiturHistoryService::recordAnalisRevisi($debitur, $request->input('alasan_revisi'));
        } else {
            $request->validate([
                'jenis_produk' => 'required|string',
                'jaminan' => 'required|string',
                'kode_jaminan' => 'required|string',
                'saldo_bpjs' => 'required|numeric',
                'tanggal_mpp' => 'required|date',
                'jenis_fasilitas' => 'required|string',
            ]);

            $tenorBulan = (int) $request->input('tenor_bulan', $debitur->tenor_bulan);
            if ($tenorBulan <= 0) {
                $tenorBulan = (int) ($debitur->tenor_bulan ?: 1);
            }
            $bungaPersen = (float) $request->input('bunga_persen', $debitur->bunga_persen);
            $pengajuanPlafond = $request->input('usulan_plafond') ?: $debitur->pengajuan_plafond;
            $spvId = $debitur->spv_id;
            
            // Update Master
            $debitur->update([
                'status_tahap' => 'pending_risk',
                'pengajuan_plafond' => $pengajuanPlafond,
                'nominal_pinjaman' => $pengajuanPlafond,
                'tenor_bulan' => $tenorBulan,
                'bunga_persen' => $bungaPersen,
                'suku_bunga' => $request->input('suku_bunga', $debitur->suku_bunga),
                'spv_id' => $spvId,
            ]);

            // Hitung Ulang Total Pinjaman
            $totalAngsuran = ($pengajuanPlafond * ($bungaPersen / 100)) + ($pengajuanPlafond / $tenorBulan);
            $totalAngsuran = round($totalAngsuran);

            // Simpan Data
            $debitur->update([
                'total_pinjaman' => $totalAngsuran * $tenorBulan
            ]);

            // Get SPV name for analisa
            $spvMarketing = $debitur->spv ? $debitur->spv->name : null;

            // Update Analisa
            \App\Models\AnalisaKeuangan::updateOrCreate(
                ['debitur_id' => $debitur->id],
                [
                    'nomor_mpp' => $request->input('nomor_mpp'),
                    'tanggal_mpp' => $request->input('tanggal_mpp'),
                    'jenis_fasilitas' => $request->input('jenis_fasilitas'),
                    'cabang_id' => $debitur->cabang_id,
                    'marketing_id' => $debitur->marketing_id,
                    'spv_marketing' => $spvMarketing,
                    'spv_id' => $spvId,
                    'pengajuan_plafond' => $debitur->pengajuan_plafond,
                    'usulan_plafond' => $request->input('usulan_plafond'),
                    'bunga_persen' => $request->input('bunga_persen'),
                    'suku_bunga' => $request->input('suku_bunga'),
                    'jaminan' => $request->input('jaminan'),
                    'kode_jaminan' => $request->input('kode_jaminan'),
                    'saldo_bpjs' => $request->input('saldo_bpjs'),
                    'jenis_produk' => $request->input('jenis_produk'),
                    'total_penghasilan_bersih' => $request->input('total_penghasilan_bersih'),
                    'detail_hutang' => $request->input('detail_hutang'),
                    'total_angsuran_hutang' => $request->input('total_angsuran_hutang'),
                    'total_angsuran_pengajuan' => $request->input('total_angsuran_pengajuan'),
                    'nilai_iir' => $request->input('nilai_iir'),
                    'nilai_ltv' => $request->input('nilai_ltv'),
                    'alasan_disetujui' => $request->input('alasan_disetujui'),
                ]
            );

            $msg = 'Tahap 3 (Analis) berhasil disimpan. Menunggu proses Risk.';
            
            // Catat aktivitas: Analis approve
            DebiturHistoryService::recordAnalisApprove($debitur);
        }

        return redirect()->route('debitur.index')->with('success', $msg);
    }

    public function editTahap3Risk($id)
    {
        if ($this->blockMarketingAndSpv()) return back()->with('error', 'Akses ditolak. Tahap Risk hanya untuk departemen terkait.');
        $debitur = MasterPencairanMekar::findOrFail($id);
        return view('apps.ecommerce.debitur-edit-tahap3-risk', compact('debitur'));
    }

    public function updateTahap3Risk(Request $request, $id)
    {
        if ($this->blockMarketingAndSpv()) return back()->with('error', 'Akses ditolak.');
        $debitur = MasterPencairanMekar::findOrFail($id);
        $request->validate([
            'nomor_spk' => 'nullable|unique:master_pencairan_mekar,nomor_spk,' . $id,
        ], [
            'nomor_spk.unique' => 'Nomor SPK sudah digunakan oleh debitur lain. Silakan edit atau klik refresh.'
        ]);

        $data = $request->except(['_token', '_method', 'action_type']);
        
        $action = $request->input('action_type');
        if ($action == 'reject') {
            $data['status_tahap'] = 'reject_risk';
            $msg = 'Pengajuan ditolak secara permanen pada Tahap Risk.';
            
            // Catat aktivitas: Risk reject
            DebiturHistoryService::recordRiskReject($debitur, $request->input('alasan_revisi') ?? 'Ditolak oleh Risk');
        } elseif ($action == 'revisi') {
            $data['status_tahap'] = 'revisi_risk';
            $data['alasan_revisi'] = $request->input('alasan_revisi');
            $msg = 'Pengajuan dikembalikan ke Marketing untuk direvisi.';
            
            // Catat aktivitas: Risk revisi
            DebiturHistoryService::recordRiskRevisi($debitur, $request->input('alasan_revisi'));
        } else {
            $data['status_tahap'] = 'pending_mitra';
            
            // Set default values as requested
            $data['metode_angsuran'] = 'monthly';
            $data['status_pinjaman'] = 'SPECIAL';

            // Generate SPK number only if it's empty
            if (empty($data['nomor_spk']) && !empty($data['tanggal_akad_kredit'])) {
                $dateAkad = \Carbon\Carbon::parse($data['tanggal_akad_kredit']);
                $debiturIdPadded = str_pad($debitur->id, 4, '0', STR_PAD_LEFT);
                $cabangId = $debitur->cabang_id;
                
                $blnRomawi = $this->intToRoman((int)$dateAkad->format('m'));
                $thn = $dateAkad->format('Y');
                
                $data['nomor_spk'] = "{$debiturIdPadded}/PK-NSB/0{$cabangId}/{$blnRomawi}/{$thn}";
            }

            $msg = 'Tahap 3 (Risk) berhasil disimpan. Menunggu approval Mitra.';
            
            // Catat aktivitas: Risk approve
            DebiturHistoryService::recordRiskApprove($debitur);
        }

        $debitur->update($data);
        return redirect()->route('debitur.index')->with('success', $msg);
    }

    private function intToRoman($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }


    public function editTahapMitra($id)
    {
        if ($this->blockNonMitra()) return back()->with('error', 'Akses ditolak. Tahap Mitra hanya untuk role Mitra.');
        $debitur = MasterPencairanMekar::findOrFail($id);
        return view('apps.ecommerce.debitur-edit-tahap-mitra', compact('debitur'));
    }

    public function updateTahapMitra(Request $request, $id)
    {
        if ($this->blockNonMitra()) return back()->with('error', 'Akses ditolak.');
        $debitur = MasterPencairanMekar::findOrFail($id);

        $statusVerifikasi = $request->input('status_verifikasi_data');
        $catatan = $request->input('catatan_mitra', '');
        $msg = '';

        // Backup pengajuan plafond awal jika belum ada
        if (empty($debitur->pengajuan_plafond_awal)) {
            $debitur->pengajuan_plafond_awal = $debitur->pengajuan_plafond ?? 0;
        }

        switch ($statusVerifikasi) {
            case 'Debt Service Ratio (DSR)':
                $nominalDsr = $request->input('nominal_pinjaman_dsr');
                if (empty($nominalDsr) || floatval($nominalDsr) <= 0) {
                    return back()->withErrors(['nominal_pinjaman_dsr' => 'Nominal pinjaman DSR harus diisi'])->withInput();
                }
                $dsrLangsungApprove = $request->input('dsr_langsung_approve') == '1';
                $debitur->status_verifikasi_data = 'Debt Service Ratio (DSR)';
                $debitur->alasan_penurunan = $catatan;
                $debitur->nominal_pinjaman = floatval($nominalDsr);
                if ($dsrLangsungApprove) {
                    $debitur->status_tahap = 'approve';
                    $debitur->status_penurunan_plafond = 'approved';
                    $debitur->save();
                    $msg = 'Pengajuan DSR langsung disetujui.';
                    DebiturHistoryService::recordMitraApprove($debitur);
                } else {
                    $debitur->status_tahap = 'pending_debitur_dsr';
                    $debitur->status_penurunan_plafond = 'pending_debitur';
                    $debitur->save();
                    $msg = 'Verifikasi DSR disimpan. Menunggu konfirmasi debitur untuk penurunan plafond.';
                    DebiturHistoryService::recordMitraReject($debitur, 'DSR - Menunggu konfirmasi penurunan plafond');
                }
                break;

            case 'Fintech Data Center (FDC)':
                $debitur->status_verifikasi_data = 'Fintech Data Center (FDC)';
                $debitur->status_tahap = 'reject_fdc';
                $debitur->save();
                $msg = 'Pengajuan ditolak oleh Mitra (Fintech Data Center).';
                DebiturHistoryService::recordMitraReject($debitur, 'FDC - Reject: ' . $catatan);
                break;

            case 'Take Out Data':
                $debitur->status_verifikasi_data = 'Take Out Data';
                $debitur->status_tahap = 'pending_debitur_takeout';
                $debitur->status_takeout = 'pending';
                $debitur->keterangan_takeout = $catatan;
                $debitur->save();
                $msg = 'Status Take Out disimpan. Menunggu respons debitur.';
                DebiturHistoryService::recordMitraReject($debitur, 'Take Out - Menunggu konfirmasi pengajuan ulang');
                break;

            case 'Revisi Data':
                $debitur->status_verifikasi_data = 'Revisi Data';
                $debitur->status_tahap = 'revisi_mitra';
                $debitur->alasan_revisi = $catatan;
                $debitur->save();
                $msg = 'Pengajuan direvisi oleh Mitra, dikembalikan ke Marketing.';
                DebiturHistoryService::recordMitraRevisi($debitur, 'Revisi Mitra: ' . $catatan);
                break;

            case 'Approve':
                $debitur->status_verifikasi_data = 'Approve';
                $debitur->status_tahap = 'approve';
                $debitur->save();
                $msg = 'Pengajuan debitur telah di-approve oleh Mitra.';
                DebiturHistoryService::recordMitraApprove($debitur);
                break;

            default:
                return back()->with('error', 'Status verifikasi tidak valid.');
        }

        return redirect()->route('debitur.index')->with('success', $msg);
    }

    /**
     * Konfirmasi debitur untuk DSR (Debt Service Ratio) - penurunan plafond
     */
    public function confirmDsrPenurunanPlafond(Request $request, $id)
    {
        $debitur = MasterPencairanMekar::findOrFail($id);
        $isApprove = $request->input('confirm_dsr') === '1';

        if ($isApprove) {
            $debitur->status_tahap = 'approve';
            $debitur->status_penurunan_plafond = 'approve';
            $debitur->save();
            DebiturHistoryService::recordMitraApprove($debitur);
            $msg = 'Debitur menyetujui penurunan plafond. Pengajuan di-approve.';
        } else {
            $debitur->status_tahap = 'reject_dsr';
            $debitur->status_penurunan_plafond = 'reject';
            $debitur->save();
            DebiturHistoryService::recordMitraReject($debitur, 'DSR penurunan plafond ditolak oleh debitur');
            $msg = 'Debitur menolak penurunan plafond. Pengajuan ditolak.';
        }

        return response()->json(['success' => true, 'message' => $msg]);
    }

    /**
     * Konfirmasi debitur untuk Take Out
     */
    public function confirmTakeOut(Request $request, $id)
    {
        $debitur = MasterPencairanMekar::findOrFail($id);
        $isApprove = $request->input('confirm_takeout') === '1';

        if ($isApprove) {
            $debitur->status_tahap = 'pending_risk';
            $debitur->status_takeout = 'approve';
            $debitur->save();
            $msg = 'Pinjaman diajukan kembali. Status menjadi Pending Risk.';
        } else {
            $debitur->status_tahap = 'reject_takeout';
            $debitur->status_takeout = 'reject';
            $debitur->save();
            DebiturHistoryService::recordMitraReject($debitur, 'Take Out ditolak');
            $msg = 'Pinjaman Take Out ditolak.';
        }

        return response()->json(['success' => true, 'message' => $msg]);
    }

    public function downloadSemuaFile($id)
    {
        $debitur = MasterPencairanMekar::findOrFail($id);
        $medias = $debitur->getMedia('*');
        
        if ($medias->count() === 0) {
            return back()->with('error', 'Tidak ada dokumen yang tersedia untuk didownload.');
        }

        $safeName = preg_replace('/[^A-Za-z0-9_-]/', '', str_replace(' ', '_', $debitur->nama_peminjam ?? 'Debitur'));
        $zipFileName = 'Dokumen_' . $safeName . '.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            foreach ($medias as $media) {
                // Determine folder structure inside zip if you want, here we put it flat
                $zip->addFile($media->getPath(), $media->file_name);
            }
            $zip->close();
        } else {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    public function action(Request $request, $id)
    {
        $debitur = MasterPencairanMekar::findOrFail($id);
        $action = $request->input('action_type'); // approve, revisi, reject
        $current_status = $debitur->status_tahap;

        if ($current_status == 'pending_spv') {
            $hasAccess = false;
            try {
                $hasAccess = auth()->user()->isSpv();
            } catch (\Exception $e) {
                // Ignore spatie exception if permission is missing
            }
            if (!$hasAccess) {
                return back()->with('error', 'Anda tidak memiliki hak akses untuk memproses pengajuan tahap 1. Hanya SPV yang diizinkan.');
            }
        }

        if ($action == 'approve') {
            if ($current_status == 'pending_spv') {
                $debitur->status_tahap = 'approved_spv';
                
                // Catat aktivitas: SPV approve dan lanjut ke Analis
                DebiturHistoryService::recordSpvApprove($debitur);
                
                // Update status untuk next tahap
                $debitur->status_tahap = 'pending_analis';
                $debitur->save();
            } elseif ($current_status == 'pending_mitra') {
                $debitur->status_tahap = 'approved';
                DebiturHistoryService::recordMitraApprove($debitur);
                $debitur->save();
            }
            $msg = 'Pengajuan berhasil disetujui.';
        } elseif ($action == 'revisi') {
            if ($current_status == 'pending_spv') {
                $debitur->status_tahap = 'revisi_spv';
                DebiturHistoryService::recordSpvRevisi($debitur, $request->input('alasan_revisi'));
            } elseif ($current_status == 'pending_analis') {
                $debitur->status_tahap = 'revisi_analis';
                DebiturHistoryService::recordAnalisRevisi($debitur, $request->input('alasan_revisi'));
            } elseif ($current_status == 'pending_risk') {
                $debitur->status_tahap = 'revisi_risk';
                \App\Models\DebiturHistory::create([
                    'debitur_id' => $debitur->id,
                    'user_id' => auth()->id(),
                    'status_tahap' => 'revisi_risk',
                    'catatan' => $request->input('alasan_revisi'),
                ]);
            } elseif ($current_status == 'pending_mitra') {
                $debitur->status_tahap = 'revisi_mitra';
                DebiturHistoryService::recordMitraRevisi($debitur, $request->input('alasan_revisi'));
            } else {
                $debitur->status_tahap = 'revisi';
                \App\Models\DebiturHistory::create([
                    'debitur_id' => $debitur->id,
                    'user_id' => auth()->id(),
                    'status_tahap' => $debitur->status_tahap,
                    'catatan' => $request->input('alasan_revisi'),
                ]);
            }
            $debitur->alasan_revisi = $request->input('alasan_revisi');
            $debitur->save();
            $msg = 'Pengajuan dikembalikan untuk direvisi.';
        } elseif ($action == 'reject') {
            if ($current_status == 'pending_spv') {
                $debitur->status_tahap = 'reject_spv';
                DebiturHistoryService::recordSpvReject($debitur, 'Ditolak oleh SPV');
            } elseif ($current_status == 'pending_analis') {
                $debitur->status_tahap = 'reject_analis';
                DebiturHistoryService::recordAnalisReject($debitur, 'Ditolak oleh Analis');
            } elseif ($current_status == 'pending_risk') {
                $debitur->status_tahap = 'reject_risk';
                \App\Models\DebiturHistory::create([
                    'debitur_id' => $debitur->id,
                    'user_id' => auth()->id(),
                    'status_tahap' => 'reject_risk',
                    'catatan' => 'Ditolak oleh Risk',
                ]);
            } elseif ($current_status == 'pending_mitra') {
                $debitur->status_tahap = 'reject_mitra';
                DebiturHistoryService::recordMitraReject($debitur, 'Ditolak oleh Mitra');
            } else {
                $debitur->status_tahap = 'reject';
                \App\Models\DebiturHistory::create([
                    'debitur_id' => $debitur->id,
                    'user_id' => auth()->id(),
                    'status_tahap' => $debitur->status_tahap,
                    'catatan' => 'Ditolak',
                ]);
            }
            $debitur->save();
            $msg = 'Pengajuan ditolak.';
        } else {
            return back()->with('error', 'Aksi tidak valid.');
        }

        return redirect()->route('debitur.index')->with('success', $msg);
    }

    public function exportMitra()
    {
        // Check if user has permission to download excel
        if (!auth()->user()->can('download excel mitra')) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengunduh data ini.');
        }

        return Excel::download(
            new DebiturMitraExport(),
            'Debitur_Pending_Mitra_' . date('Y-m-d_H-i-s') . '.xlsx'
        );
    }
}
