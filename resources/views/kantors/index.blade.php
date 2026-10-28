@extends('layouts.master')
@section('title', 'Data Kantor')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box mt-3">
                <h4 class="mb-sm-0">Data Kantor</h4>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-header">
                    @can('create kantor')
                        <a href="{{ route('kantors.create') }}" class="btn btn-primary">Tambah Kantor</a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Kantor</th>
                                    <th>Unit</th>
                                    <th>Alamat</th>
                                    <th>Status</th>
                                    <th width="180">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kantors as $item)
                                    <tr>
                                        <td>{{ $item->nama_kantor }}</td>
                                        <td>{{ $item->unit ?: '-' }}</td>
                                        <td>{{ $item->alamat ?: '-' }}</td>
                                        <td>
                                            <span class="badge {{ $item->status === 'Aktif' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td>
                                            @can('edit kantor')
                                                <a href="{{ route('kantors.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                            @endcan

                                            @can('delete kantor')
                                                <form action="{{ route('kantors.destroy', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data kantor ini?')">Delete</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada data kantor.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
