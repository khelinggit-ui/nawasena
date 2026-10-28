<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\MasterPencairanMekar;
use App\Models\Cabang;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $approvedStatuses = ['approve', 'approved'];
        $userCabangId = Auth::user()->cabang_id;
        $cabang = $userCabangId ? Cabang::find($userCabangId) : null;
        $userCabang = $cabang ? $cabang->nama_cabang : 'Unknown';
        
        $hasCabang = !empty($userCabangId) && strtolower($userCabang) !== 'pusat';

        $filterCabang = $hasCabang ? $userCabangId : $request->input('cabang_id');
        $filterTahun = $request->input('tahun', now()->year);

        // 1. Total Plafond
        $queryPlafond = MasterPencairanMekar::query()
            ->whereIn('status_tahap', $approvedStatuses);
        if ($filterCabang) {
            $queryPlafond->where('cabang_id', $filterCabang);
        }
        if ($filterTahun) {
            $queryPlafond->whereYear('tanggal_akad_kredit', $filterTahun);
        }
        $totalPlafond = $queryPlafond->sum('nominal_pinjaman');

        // 1.1 Total All Plafond (Semua Cabang, Semua Tahun)
        $totalAllPlafond = MasterPencairanMekar::query()
            ->whereIn('status_tahap', $approvedStatuses)
            ->sum('nominal_pinjaman');

        // 2. Total Pembayaran
        try {
            $queryPembayaran = DB::table('pembayaran_angsurans')
                ->join('master_pencairan_mekar', 'pembayaran_angsurans.id_debitur', '=', 'master_pencairan_mekar.id')
                ->whereIn('master_pencairan_mekar.status_tahap', $approvedStatuses);
            if ($filterCabang) {
                $queryPembayaran->where('master_pencairan_mekar.cabang_id', $filterCabang);
            }
            if ($filterTahun) {
                $queryPembayaran->whereYear('pembayaran_angsurans.created_at', $filterTahun);
            }
            $totalPembayaran = $queryPembayaran->sum('pembayaran_angsurans.nominal_bayar');
        } catch (\Exception $e) {
            $totalPembayaran = 0;
        }

        // 2.1 Total All Pembayaran (Semua Cabang, Semua Tahun)
        try {
            $totalAllPembayaran = DB::table('pembayaran_angsurans')
                ->join('master_pencairan_mekar', 'pembayaran_angsurans.id_debitur', '=', 'master_pencairan_mekar.id')
                ->whereIn('master_pencairan_mekar.status_tahap', $approvedStatuses)
                ->sum('pembayaran_angsurans.nominal_bayar');
        } catch (\Exception $e) {
            $totalAllPembayaran = 0;
        }

        // 3. Total Debitur
        $queryDebitur = MasterPencairanMekar::query()
            ->whereIn('status_tahap', $approvedStatuses);
        if ($filterCabang) {
            $queryDebitur->where('cabang_id', $filterCabang);
        }
        if ($filterTahun) {
            $queryDebitur->whereYear('tanggal_akad_kredit', $filterTahun);
        }
        $totalDebitur = $queryDebitur->count();

        // 3.1 Total All Debitur (Semua Cabang, Semua Tahun)
        $totalAllDebitur = MasterPencairanMekar::query()
            ->whereIn('status_tahap', $approvedStatuses)
            ->count();

        // 4. Outstanding
        $outstanding = $totalPlafond - $totalPembayaran;
        
        // 4.1 Total All Outstanding
        $totalAllOutstanding = $totalAllPlafond - $totalAllPembayaran;

        // 5. Pencapaian Marketing
        $pencapaianMarketing = MasterPencairanMekar::query()
            ->selectRaw('master_pencairan_mekar.marketing_id, users.name as marketing_name, master_pencairan_mekar.cabang_id, cabangs.nama_cabang as cabang_name, COUNT(master_pencairan_mekar.id) as jumlah_debitur, SUM(master_pencairan_mekar.nominal_pinjaman) as total_plafond')
            ->leftJoin('users', 'master_pencairan_mekar.marketing_id', '=', 'users.id')
            ->leftJoin('cabangs', 'master_pencairan_mekar.cabang_id', '=', 'cabangs.id')
            ->whereIn('master_pencairan_mekar.status_tahap', $approvedStatuses)
            ->whereNotNull('master_pencairan_mekar.tanggal_akad_kredit');

        if ($hasCabang) {
            $pencapaianMarketing->where('master_pencairan_mekar.cabang_id', $userCabangId);
        }
        if ($filterTahun) {
            $pencapaianMarketing->whereYear('master_pencairan_mekar.tanggal_akad_kredit', $filterTahun);
        }

        $pencapaianMarketing = $pencapaianMarketing->groupBy('master_pencairan_mekar.marketing_id', 'users.name', 'master_pencairan_mekar.cabang_id', 'cabangs.nama_cabang')
            ->orderByDesc('total_plafond')
            ->get();

        $pembayaranMarketing = [];
        try {
            $pembayaranQuery = DB::table('pembayaran')
                ->join('master_pencairan_mekar', 'pembayaran.no_kontrak', '=', 'master_pencairan_mekar.no_kontrak')
                ->selectRaw('master_pencairan_mekar.marketing_id, master_pencairan_mekar.cabang_id, SUM(pembayaran.nominal) as total_pembayaran');
            
            if ($hasCabang) {
                $pembayaranQuery->where('master_pencairan_mekar.cabang_id', $userCabangId);
            }
            if ($filterTahun) {
                $pembayaranQuery->whereYear('pembayaran.created_at', $filterTahun);
            }
            
            $pembayaranData = $pembayaranQuery->groupBy('master_pencairan_mekar.marketing_id', 'master_pencairan_mekar.cabang_id')->get();
            foreach ($pembayaranData as $row) {
                $pembayaranMarketing[$row->marketing_id . '_' . $row->cabang_id] = $row->total_pembayaran;
            }
        } catch (\Exception $e) {
            // ignore
        }

        foreach ($pencapaianMarketing as $pm) {
            $key = $pm->marketing_id . '_' . $pm->cabang_id;
            $pm->total_pembayaran = $pembayaranMarketing[$key] ?? 0;
            $pm->rasio_bayar = $pm->total_plafond > 0 ? ($pm->total_pembayaran / $pm->total_plafond) * 100 : 0;
        }

        // 5. List cabang untuk filter (hanya untuk user tanpa cabang)
        $listCabang = $hasCabang ? [] : Cabang::orderBy('nama_cabang')->get();

        // 6. List tahun tersedia dari data
        $listTahun = MasterPencairanMekar::query()
            ->selectRaw('YEAR(tanggal_akad_kredit) as tahun')
            ->whereIn('status_tahap', $approvedStatuses)
            ->whereNotNull('tanggal_akad_kredit')
            ->groupBy('tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('dashboard.home', compact(
            'totalPlafond',
            'totalAllPlafond',
            'totalPembayaran',
            'totalAllPembayaran',
            'totalDebitur',
            'totalAllDebitur',
            'outstanding',
            'totalAllOutstanding',
            'hasCabang',
            'userCabangId',
            'userCabang',
            'listCabang',
            'listTahun',
            'filterTahun',
            'filterCabang',
            'pencapaianMarketing'
        ));
    }

    /**
     * API endpoint: data grafik pencapaian per bulan
     * berdasarkan tanggal_pencairan dan cabang
     */
    public function grafikPencapaian(Request $request)
    {
        $approvedStatuses = ['approve', 'approved'];
        $userCabangId = Auth::user()->cabang_id;
        $cabang = $userCabangId ? Cabang::find($userCabangId) : null;
        $hasCabang = !empty($userCabangId) && strtolower($cabang->nama_cabang ?? '') !== 'pusat';

        // User tanpa cabang boleh filter lewat query param ?cabang_id=xxx
        $filterCabang = $hasCabang ? $userCabangId : $request->input('cabang_id');

        $tahunBerjalan = now()->year;
        // Gunakan tahun dari request jika ada, fallback ke tahun berjalan
        $tahunDipilih = $request->input('tahun', $tahunBerjalan);

        $query = MasterPencairanMekar::query()
            ->selectRaw("MONTH(tanggal_akad_kredit) as bulan_ke, SUM(nominal_pinjaman) as total_plafond, COUNT(*) as total_debitur")
            ->whereIn('status_tahap', $approvedStatuses)
            ->whereNotNull('tanggal_akad_kredit')
            ->whereYear('tanggal_akad_kredit', $tahunDipilih)
            ->groupBy('bulan_ke')
            ->orderBy('bulan_ke');

        if (!empty($filterCabang)) {
            $query->where('cabang_id', $filterCabang);
        }

        $data = $query->get();

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return response()->json([
            'labels'       => $data->map(fn($d) => $namaBulan[(int)$d->bulan_ke] ?? $d->bulan_ke),
            'totalPlafond' => $data->pluck('total_plafond'),
            'totalDebitur' => $data->pluck('total_debitur'),
            'tahun'        => $tahunDipilih,
        ]);
    }

    /**
     * API endpoint: data grafik pencapaian per cabang
     */
    public function grafikPencapaianCabang(Request $request)
    {
        $approvedStatuses = ['approve', 'approved'];
        $userCabangId = Auth::user()->cabang_id;
        $cabang = $userCabangId ? Cabang::find($userCabangId) : null;
        $hasCabang = !empty($userCabangId) && strtolower($cabang->nama_cabang ?? '') !== 'pusat';

        $tahunBerjalan = now()->year;
        $tahunDipilih = $request->input('tahun', $tahunBerjalan);

        $query = MasterPencairanMekar::query()
            ->selectRaw("cabangs.nama_cabang as cabang_name, SUM(master_pencairan_mekar.nominal_pinjaman) as total_plafond")
            ->leftJoin('cabangs', 'master_pencairan_mekar.cabang_id', '=', 'cabangs.id')
            ->whereIn('master_pencairan_mekar.status_tahap', $approvedStatuses)
            ->whereNotNull('master_pencairan_mekar.tanggal_akad_kredit')
            ->whereYear('master_pencairan_mekar.tanggal_akad_kredit', $tahunDipilih)
            ->groupBy('cabangs.nama_cabang')
            ->orderBy('total_plafond', 'desc');

        if ($hasCabang) {
            $query->where('master_pencairan_mekar.cabang_id', $userCabangId);
        }

        $data = $query->get();

        return response()->json([
            'labels' => $data->pluck('cabang_name'),
            'series' => $data->pluck('total_plafond'),
        ]);
    }

    /**
     * Tampilan Halaman Summary Marketing Data
     */
    public function summary(Request $request)
    {
        $userCabang = Auth::user()->cabang;
        $hasCabang = !empty($userCabang) && strtolower($userCabang->nama_cabang ?? '') !== 'pusat';
        $userCabangId = Auth::user()->cabang_id;
        $approvedStatuses = ['approve', 'approved'];

        $query = MasterPencairanMekar::query()
            ->selectRaw('cabangs.nama_cabang as cabang_name, users.name as marketing_name, master_pencairan_mekar.nominal_pinjaman, COUNT(master_pencairan_mekar.id) as jumlah_debitur, SUM(master_pencairan_mekar.nominal_pinjaman) as jumlah_pencapaian')
            ->leftJoin('users', 'master_pencairan_mekar.marketing_id', '=', 'users.id')
            ->leftJoin('cabangs', 'master_pencairan_mekar.cabang_id', '=', 'cabangs.id')
            ->whereNotNull('master_pencairan_mekar.tanggal_akad_kredit')
            ->whereIn('master_pencairan_mekar.status_tahap', $approvedStatuses);

        if ($hasCabang) {
            $query->where('master_pencairan_mekar.cabang_id', $userCabangId);
        }

        // Group the data by cabang_name first, then by marketing_name and nominal_pinjaman
        $data = $query->groupBy('cabangs.nama_cabang', 'users.name', 'master_pencairan_mekar.nominal_pinjaman')
            ->orderBy('cabangs.nama_cabang')
            ->orderBy('users.name', 'asc')
            ->orderBy('master_pencairan_mekar.nominal_pinjaman', 'asc')
            ->get();

        // Laravel collection groupBy to easily iterate in the view
        $summaryData = $data->groupBy('cabang_name');

        return view('dashboard.summary', compact('summaryData'));
    }

    /**
     * Tampilan Halaman Summary SPV Data
     */
    public function summarySpv(Request $request)
    {
        $userCabang = Auth::user()->cabang;
        $hasCabang = !empty($userCabang) && strtolower($userCabang->nama_cabang ?? '') !== 'pusat';
        $userCabangId = Auth::user()->cabang_id;
        $approvedStatuses = ['approve', 'approved'];

        $query = MasterPencairanMekar::query()
            ->selectRaw('cabangs.nama_cabang as cabang_name, spv_users.name as spv, users.name as marketing_name, COUNT(master_pencairan_mekar.id) as jumlah_debitur, SUM(master_pencairan_mekar.nominal_pinjaman) as jumlah_pencapaian')
            ->leftJoin('users', 'master_pencairan_mekar.marketing_id', '=', 'users.id')
            ->leftJoin('users as spv_users', 'master_pencairan_mekar.spv_id', '=', 'spv_users.id')
            ->leftJoin('cabangs', 'master_pencairan_mekar.cabang_id', '=', 'cabangs.id')
            ->whereNotNull('master_pencairan_mekar.tanggal_akad_kredit')
            ->whereIn('master_pencairan_mekar.status_tahap', $approvedStatuses);

        if ($hasCabang) {
            $query->where('master_pencairan_mekar.cabang_id', $userCabangId);
        }

        $data = $query->groupBy('cabangs.nama_cabang', 'spv_users.name', 'users.name')
            ->orderBy('cabangs.nama_cabang')
            ->orderBy('spv_users.name', 'asc')
            ->orderBy('users.name', 'asc')
            ->get();

        $summaryData = $data->groupBy('cabang_name');

        return view('dashboard.summary-spv', compact('summaryData'));
    }
}
