@extends('layouts.master')
@section('title', 'Edit Analisis Kredit | NSB')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Edit Data Analisis Kredit</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('analisis-kredit.index') }}">Analisis Kredit</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Form Edit Data Analis</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('analisis-kredit.update', $analisa->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row gy-4">
                            <!-- Basic fields from Analis Keuangan -->
                            <div class="col-md-6">
                                <label class="form-label">Jenis Fasilitas</label>
                                <input type="text" name="jenis_fasilitas" class="form-control" value="{{ old('jenis_fasilitas', $analisa->jenis_fasilitas) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jaminan</label>
                                <input type="text" name="jaminan" class="form-control" value="{{ old('jaminan', $analisa->jaminan) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Jaminan</label>
                                <input type="text" name="kode_jaminan" class="form-control" value="{{ old('kode_jaminan', $analisa->kode_jaminan) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Saldo BPJS</label>
                                <input type="number" name="saldo_bpjs" class="form-control" value="{{ old('saldo_bpjs', $analisa->saldo_bpjs) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Produk</label>
                                <input type="text" name="jenis_produk" class="form-control" value="{{ old('jenis_produk', $analisa->jenis_produk) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total Penghasilan Bersih</label>
                                <input type="number" name="total_penghasilan_bersih" class="form-control" value="{{ old('total_penghasilan_bersih', $analisa->total_penghasilan_bersih) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total Angsuran Hutang</label>
                                <input type="number" name="total_angsuran_hutang" class="form-control" value="{{ old('total_angsuran_hutang', $analisa->total_angsuran_hutang) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Total Angsuran Pengajuan</label>
                                <input type="number" name="total_angsuran_pengajuan" class="form-control" value="{{ old('total_angsuran_pengajuan', $analisa->total_angsuran_pengajuan) }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Alasan Disetujui</label>
                                <textarea name="alasan_disetujui" class="form-control" rows="3">{{ old('alasan_disetujui', $analisa->alasan_disetujui) }}</textarea>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">Update Data</button>
                            <a href="{{ route('analisis-kredit.index') }}" class="btn btn-light">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
