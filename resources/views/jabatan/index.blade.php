@extends('layouts.master')
@section('title', 'Master Jabatan')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Data Jabatan</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Master Data</a></li>
                        <li class="breadcrumb-item active">Jabatan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Daftar Jabatan</h4>
                    <div class="flex-shrink-0">
                        <a href="{{ route('jabatans.create') }}" class="btn btn-success"><i class="ri-add-line align-bottom me-1"></i> Tambah Jabatan</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card mt-3 mb-1 px-3">
                        <table class="table align-middle table-nowrap" id="jabatanTable">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Jabatan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jabatans as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nama_jabatan }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <div class="edit">
                                                <a href="{{ route('jabatans.edit', $item->id) }}" class="btn btn-sm btn-info edit-item-btn"><i class="ri-pencil-fill"></i> Edit</a>
                                            </div>
                                            <div class="remove">
                                                <form action="{{ route('jabatans.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jabatan {{ $item->nama_jabatan }}? Pastikan tidak ada user yang menggunakan role ini.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger remove-item-btn"><i class="ri-delete-bin-fill"></i> Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
