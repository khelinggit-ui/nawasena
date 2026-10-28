@extends('layouts.master')
@section('title', 'Tambah Jabatan')
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Tambah Jabatan</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('jabatans.index') }}">Jabatan</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('jabatans.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header border-bottom-dashed">
                        <h5 class="card-title mb-0">Informasi Jabatan</h5>
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
                            <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_jabatan" class="form-control" value="{{ old('nama_jabatan') }}" required placeholder="Masukkan nama jabatan">
                        </div>
                    </div>
                </div>

                <div class="text-end mb-4">
                    <a href="{{ route('jabatans.index') }}" class="btn btn-light me-2">Batal</a>
                    <button type="submit" class="btn btn-success">Simpan Data</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
