@extends('layouts.master')
@section('title', 'Tambah Parameter Produk')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Tambah Parameter Produk</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('parameter-produk.index') }}">Parameter Produk</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Form Tambah Parameter Produk</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('parameter-produk.store') }}" method="POST">
                        @csrf
                        <div class="row gy-4">
                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="nama_produk" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_produk') is-invalid @enderror" id="nama_produk" name="nama_produk" value="{{ old('nama_produk') }}" placeholder="Masukkan nama produk">
                                    @error('nama_produk')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="bunga_persen" class="form-label">Bunga Persen (%) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('bunga_persen') is-invalid @enderror" id="bunga_persen" name="bunga_persen" value="{{ old('bunga_persen') }}" placeholder="Masukkan persentase bunga (misal: 1.5)">
                                    @error('bunga_persen')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-xxl-6 col-md-6">
                                <div>
                                    <label for="suku_bunga" class="form-label">Jenis Suku Bunga</label>
                                    <input type="text" class="form-control @error('suku_bunga') is-invalid @enderror" id="suku_bunga" name="suku_bunga" value="{{ old('suku_bunga') }}" placeholder="Masukkan jenis suku bunga (misal: Flat / Efektif)">
                                    @error('suku_bunga')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-success">Simpan Data</button>
                            <a href="{{ route('parameter-produk.index') }}" class="btn btn-light">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
