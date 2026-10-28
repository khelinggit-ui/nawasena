@extends('layouts.master')
@section('title', 'Tambah Perusahaan')
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Tambah Perusahaan</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('perusahaan.index') }}">Perusahaan</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('perusahaan.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <h5 class="card-title mb-0">Informasi Dasar Perusahaan</h5>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_perusahaan" class="form-control" value="{{ old('nama_perusahaan') }}" required placeholder="Masukkan nama perusahaan">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat Perusahaan</label>
                            <textarea name="alamat_perusahaan" class="form-control" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat_perusahaan') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Telepon Perusahaan</label>
                            <input type="text" name="telepon_perusahaan" class="form-control" value="{{ old('telepon_perusahaan') }}" placeholder="Contoh: 021-12345678">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <h5 class="card-title mb-0">Informasi Kerjasama (PKS) & Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-12">
                                <div>
                                    <label class="form-label">Nomor PKS</label>
                                    <input type="text" name="no_PKS" class="form-control" value="{{ old('no_PKS') }}" placeholder="Contoh: 001/PKS-XX/2026">
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div>
                                    <label class="form-label">Tanggal PKS</label>
                                    <input type="date" name="tanggal_PKS" class="form-control" value="{{ old('tanggal_PKS') }}">
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <div>
                                    <label class="form-label">Tanggal Berakhir PKS</label>
                                    <input type="date" name="tanggal_berakhir_PKS" class="form-control" value="{{ old('tanggal_berakhir_PKS') }}">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div>
                                    <label class="form-label">Payment Method</label>
                                    <input type="text" name="payment" class="form-control" value="{{ old('payment') }}" placeholder="Contoh: Transfer Bank, VA">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div>
                                    <label class="form-label">Payroll Date</label>
                                    <input type="text" name="payroll_date" class="form-control" value="{{ old('payroll_date') }}" placeholder="Contoh: 2026-04-09">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end mb-4">
                    <a href="{{ route('perusahaan.index') }}" class="btn btn-light me-2">Batal</a>
                    <button type="submit" class="btn btn-success">Simpan Data</button>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <h5 class="card-title mb-0">Pengaturan Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                            <input type="checkbox" class="form-check-input" id="isActiveSwitch" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActiveSwitch">Status Aktif</label>
                        </div>
                        <p class="text-muted mb-0">
                            Hanya perusahaan dengan status <b>Aktif</b> yang akan muncul pada menu pembuatan debitur/transaksi (jika dikonfigurasi).
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
