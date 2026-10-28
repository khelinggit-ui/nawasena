@extends('layouts.master')
@section('title', 'Perusahaan')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Data Perusahaan</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Perusahaan</a></li>
                        <li class="breadcrumb-item active">List</li>
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
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Daftar Perusahaan Mitra</h4>
                    <div class="flex-shrink-0">
                        <a href="{{ route('perusahaan.create') }}" class="btn btn-success"><i class="ri-add-line align-bottom me-1"></i> Tambah Perusahaan</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card mt-3 mb-1 px-3">
                        <table class="table align-middle table-nowrap" id="perusahaanTable">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Perusahaan</th>
                                    <th>No PKS</th>
                                    <th>Tanggal PKS</th>
                                    <th>Jatuh Tempo PKS</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($perusahaans as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->nama_perusahaan }}</td>
                                    <td>{{ $item->no_PKS ?? '-' }}</td>
                                    <td>{{ $item->tanggal_PKS ? \Carbon\Carbon::parse($item->tanggal_PKS)->format('d M Y') : '-' }}</td>
                                    <td>{{ $item->tanggal_berakhir_PKS ? \Carbon\Carbon::parse($item->tanggal_berakhir_PKS)->format('d M Y') : '-' }}</td>
                                    <td>
                                        @if($item->is_active)
                                            <span class="badge bg-success-subtle text-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <div class="edit">
                                                <a href="{{ route('perusahaan.edit', $item->id) }}" class="btn btn-sm btn-info edit-item-btn"><i class="ri-pencil-fill"></i> Edit</a>
                                            </div>
                                            <div class="remove">
                                                <form action="{{ route('perusahaan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus perusahaan {{ $item->nama_perusahaan }}?');">
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
