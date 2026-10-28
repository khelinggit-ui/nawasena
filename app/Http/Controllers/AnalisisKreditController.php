<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnalisaKeuangan;

class AnalisisKreditController extends Controller
{
    public function index()
    {
        return view('apps.analisis-kredit.index');
    }

    public function getData(Request $request)
    {
        $query = AnalisaKeuangan::with('debitur')->orderBy('id', 'desc');

        // Optional filtering based on branch or specific rules if needed
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && !empty($user->cabang_id)) {
            $cabang = \App\Models\Cabang::find($user->cabang_id);
            if ($cabang && strtolower($cabang->nama_cabang) !== 'pusat') {
                $query->whereHas('debitur', function($q) use ($user) {
                    $q->where('cabang_id', $user->cabang_id);
                });
            }
        }

        $data = $query->get()->map(function ($item) {
            $debiturStatus = $item->debitur->status_tahap ?? null;
            $isDebiturApproved = is_string($debiturStatus) && str_starts_with(strtolower($debiturStatus), 'approve');

            return [
                'id' => $item->id,
                'debitur_id' => $item->debitur_id,
                'nama_debitur' => $item->debitur ? $item->debitur->nama_peminjam : '-',
                'debitur_status' => $debiturStatus,
                'is_debitur_approved' => $isDebiturApproved,
                'jenis_fasilitas' => $item->jenis_fasilitas,
                'nomor_mpp' => $item->nomor_mpp,
                'tanggal_mpp' => $item->tanggal_mpp ? $item->tanggal_mpp->format('Y-m-d') : '-',
                'marketing_status' => $item->marketing_status,
                'spv_status' => $item->spv_status,
                'analis_status' => $item->analis_status,
                'kadivbisnis_status' => $item->kadivbisnis_status,
                'pengelola_status' => $item->pengelola_status,
            ];
        });

        return response()->json($data);
    }

    public function edit($id)
    {
        if ($this->getUserRoleType() !== 'analis') {
            return back()->with('error', 'Akses ditolak. Hanya Analis yang dapat mengedit data Analisis Kredit.');
        }

        $analisa = AnalisaKeuangan::findOrFail($id);
        $debitur = $analisa->debitur;
        $parameterProduks = \App\Models\ParameterProduk::all();
        
        return view('apps.analisis-kredit.edit', compact('analisa', 'debitur', 'parameterProduks'));
    }

    public function update(Request $request, $id)
    {
        if ($this->getUserRoleType() !== 'analis') {
            return back()->with('error', 'Akses ditolak.');
        }

        $analisa = AnalisaKeuangan::findOrFail($id);
        $analisa->update([
            'jenis_fasilitas' => $request->input('jenis_fasilitas'),
            'jaminan' => $request->input('jaminan'),
            'kode_jaminan' => $request->input('kode_jaminan'),
            'saldo_bpjs' => $request->input('saldo_bpjs'),
            'jenis_produk' => $request->input('jenis_produk'),
            'total_penghasilan_bersih' => $request->input('total_penghasilan_bersih'),
            'detail_hutang' => $request->input('detail_hutang'),
            'total_angsuran_hutang' => $request->input('total_angsuran_hutang'),
            'total_angsuran_pengajuan' => $request->input('total_angsuran_pengajuan'),
            'alasan_disetujui' => $request->input('alasan_disetujui'),
        ]);

        return redirect()->route('analisis-kredit.index')->with('success', 'Data Analisis Kredit berhasil diperbarui.');
    }

    public function persetujuan($id)
    {
        $analisa = AnalisaKeuangan::with('debitur')->findOrFail($id);
        $debiturStatus = $analisa->debitur->status_tahap ?? null;
        $isDebiturApproved = is_string($debiturStatus) && str_starts_with(strtolower($debiturStatus), 'approve');
        if (!$isDebiturApproved) {
            return back()->with('error', 'Persetujuan hanya dapat dilakukan jika status debitur sudah approve.');
        }

        if (!auth()->user()->can('page debitur approve')) {
            return back()->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk membuka halaman persetujuan.');
        }

        $roleType = $this->getUserRoleType();
        return view('apps.analisis-kredit.persetujuan', compact('analisa', 'roleType'));
    }

    public function view($id)
    {
        $analisa = AnalisaKeuangan::with('debitur', 'marketing', 'spv')->findOrFail($id);

        if (!auth()->user()->can('page analis detail')) {
            return back()->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk melihat detail analis.');
        }

        return view('apps.analisis-kredit.view', compact('analisa'));
    }

    public function approvePersetujuan(Request $request, $id)
    {
        $analisa = AnalisaKeuangan::with('debitur')->findOrFail($id);
        $debiturStatus = $analisa->debitur->status_tahap ?? null;
        $isDebiturApproved = is_string($debiturStatus) && str_starts_with(strtolower($debiturStatus), 'approve');
        if (!$isDebiturApproved) {
            return back()->with('error', 'Persetujuan hanya dapat dilakukan jika status debitur sudah approve.');
        }

        if (!auth()->user()->can('page debitur approve')) {
            return back()->with('error', 'Akses ditolak. Anda tidak memiliki izin untuk melakukan persetujuan.');
        }

        $roleType = $this->getUserRoleType();

        if (!$roleType || !in_array($roleType, ['marketing', 'spv', 'analis', 'kadivbisnis', 'pengelola'])) {
            return back()->with('error', 'Akses ditolak. Peran Anda tidak memiliki hak untuk menyetujui dokumen ini.');
        }

        // Validate current role fields
        $request->validate([
            "{$roleType}_note" => 'nullable|string',
            "{$roleType}_status" => 'required|in:Setuju,Tidak Setuju',
        ]);

        // Update database
        $analisa->update([
            "{$roleType}_note" => $request->input("{$roleType}_note"),
            "{$roleType}_status" => $request->input("{$roleType}_status"),
            "{$roleType}_approved_by" => auth()->user()->name,
        ]);

        return redirect()->route('analisis-kredit.index')->with('success', 'Persetujuan berhasil disimpan.');
    }

    public function print($id)
    {
        $analisa = AnalisaKeuangan::with(['debitur.marketing', 'debitur.spv', 'debitur.cabang', 'spv'])->findOrFail($id);
        
        // Ensure all roles have approved
        $isAllApproved = $analisa->marketing_status === 'Setuju' &&
                         $analisa->spv_status === 'Setuju' &&
                         $analisa->analis_status === 'Setuju' &&
                         $analisa->kadivbisnis_status === 'Setuju' &&
                         $analisa->pengelola_status === 'Setuju';

        if (!$isAllApproved) {
            return back()->with('error', 'Dokumen belum disetujui sepenuhnya oleh semua pihak terkait.');
        }

        return view('apps.analisis-kredit.print', compact('analisa'));
    }

    private function getUserRoleType()
    {
        return $this->getUserRoleTypePublic();
    }

    public function getUserRoleTypePublic()
    {
        $user = auth()->user();
        if (!$user) return null;

        $level = str_replace(' ', '', strtolower($user->level));
        $jabatan = $user->jabatan ? str_replace(' ', '', strtolower($user->jabatan->nama_jabatan)) : '';

        if (in_array($level, ['marketing', 'lendingofficer']) || strpos($jabatan, 'marketing') !== false || strpos($jabatan, 'lending') !== false || (method_exists($user, 'isMarketing') && $user->isMarketing())) {
            return 'marketing';
        }
        if ($level === 'spv' || strpos($jabatan, 'spv') !== false || (method_exists($user, 'isSpv') && $user->isSpv())) {
            return 'spv';
        }
        if ($level === 'analis' || strpos($jabatan, 'analis') !== false || (method_exists($user, 'isAnalis') && $user->isAnalis())) {
            return 'analis';
        }
        if (strpos($level, 'kadiv') !== false || strpos($jabatan, 'kadiv') !== false) {
            return 'kadivbisnis';
        }
        if (strpos($level, 'pengelola') !== false || strpos($jabatan, 'pengelola') !== false) {
            return 'pengelola';
        }
        
        return null;
    }
}
