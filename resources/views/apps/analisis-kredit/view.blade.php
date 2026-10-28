@extends('layouts.master')
@section('title', 'Detail Analisis Kredit | NSB')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Detail Analisis Kredit</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('analisis-kredit.index') }}">Analisis Kredit</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h5 class="mb-1">{{ $analisa->debitur->nama_peminjam ?? '-' }}</h5>
                        <p class="text-muted mb-0">Debitur ID: {{ $analisa->debitur_id }}</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th class="fw-medium">Nomor MPP</th>
                                    <td>{{ $analisa->nomor_mpp ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="fw-medium">Tanggal MPP</th>
                                    <td>{{ $analisa->tanggal_mpp ? $analisa->tanggal_mpp->format('d M Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="fw-medium">Jenis Fasilitas</th>
                                    <td>{{ $analisa->jenis_fasilitas ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="fw-medium">Jenis Produk</th>
                                    <td>{{ $analisa->jenis_produk ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="fw-medium">Jaminan</th>
                                    <td>{{ $analisa->jaminan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="fw-medium">Kode Jaminan</th>
                                    <td>{{ $analisa->kode_jaminan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="fw-medium">Saldo BPJS</th>
                                    <td>Rp. {{ number_format($analisa->saldo_bpjs ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th class="fw-medium">Pengajuan Plafond</th>
                                    <td>Rp. {{ number_format($analisa->pengajuan_plafond ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th class="fw-medium">Usulan Plafond</th>
                                    <td>Rp. {{ number_format($analisa->usulan_plafond ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th class="fw-medium">Bunga Persen</th>
                                    <td>{{ $analisa->bunga_persen ? $analisa->bunga_persen . '%' : '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="fw-medium">Suku Bunga</th>
                                    <td>{{ $analisa->suku_bunga ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="fw-medium">Nilai IIR</th>
                                    <td>{{ $analisa->nilai_iir ? $analisa->nilai_iir . '%' : '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="fw-medium">Nilai LTV</th>
                                    <td>{{ $analisa->nilai_ltv ? $analisa->nilai_ltv . '%' : '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="fw-medium">Marketing</th>
                                    <td>{{ $analisa->marketing->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="fw-medium">SPV</th>
                                    <td>{{ $analisa->spv->name ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-8">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail Keuangan</h5>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <div class="mb-2 text-muted">Total Penghasilan Bersih</div>
                            <div class="fw-semibold">{{ number_format($analisa->total_penghasilan_bersih ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2 text-muted">Total Angsuran Hutang</div>
                            <div class="fw-semibold">{{ number_format($analisa->total_angsuran_hutang ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2 text-muted">Total Angsuran Pengajuan</div>
                            <div class="fw-semibold">{{ number_format($analisa->total_angsuran_pengajuan ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-2 text-muted">Alasan Disetujui</div>
                            <div class="p-3 bg-light rounded">{{ $analisa->alasan_disetujui ?? '-' }}</div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-2 text-muted">Detail Hutang</div>
                            @if(is_array($analisa->detail_hutang) && count($analisa->detail_hutang))
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Deskripsi</th>
                                                <th>Nominal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($analisa->detail_hutang as $index => $hutang)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $hutang['description'] ?? $hutang['nama'] ?? '-' }}</td>
                                                    <td>{{ isset($hutang['amount']) ? number_format($hutang['amount'], 0, ',', '.') : ($hutang['nominal'] ?? '-') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-3 bg-light rounded">{{ $analisa->detail_hutang ? json_encode($analisa->detail_hutang, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '-' }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Status Persetujuan</h5>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <div class="mb-2 text-muted">Marketing Status</div>
                            <div class="fw-semibold">{{ $analisa->marketing_status ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2 text-muted">Marketing Approved By</div>
                            <div class="fw-semibold">{{ $analisa->marketing_approved_by ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2 text-muted">Analis Status</div>
                            <div class="fw-semibold">{{ $analisa->analis_status ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2 text-muted">Analis Approved By</div>
                            <div class="fw-semibold">{{ $analisa->analis_approved_by ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2 text-muted">Kadiv Bisnis Status</div>
                            <div class="fw-semibold">{{ $analisa->kadivbisnis_status ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2 text-muted">Kadiv Bisnis Approved By</div>
                            <div class="fw-semibold">{{ $analisa->kadivbisnis_approved_by ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2 text-muted">Pengelola Status</div>
                            <div class="fw-semibold">{{ $analisa->pengelola_status ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2 text-muted">Pengelola Approved By</div>
                            <div class="fw-semibold">{{ $analisa->pengelola_approved_by ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3 text-end">
                <a href="{{ route('analisis-kredit.index') }}" class="btn btn-light">Kembali ke Daftar</a>
            </div>
        </div>
    </div>
</div>
@endsection
