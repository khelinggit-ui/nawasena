@extends('layouts.master')
@section('title', 'Data Cabang')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
             <div class="page-title-box mt-3">
                 <h4 class="mb-sm-0">Data Cabang</h4>
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
                    @can('create cabang')
                    <a href="{{ route('cabangs.create') }}" class="btn btn-primary">Create Cabang</a>
                    @endcan
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Cabang</th>
                                <th>SPV Cabang</th>
                                <th>Target Cabang</th>
                                <th>Pencapaian Bulan Lalu</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cabangs as $item)
                            <tr>
                                <td>{{ $item->nama_cabang }}</td>
                                <td>{{ $item->spv_cabang }}</td>
                                <td>Rp {{ number_format($item->target_cabang, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->pencapaian_bulan_lalu, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $item->status == 'Aktif' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td>
                                    @can('edit cabang')
                                    <a href="{{ route('cabangs.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    @endcan
                                    @can('delete cabang')
                                    <form action="{{ route('cabangs.destroy', $item->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this cabang?')">Delete</button>
                                    </form>
                                    @endcan
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
@endsection
