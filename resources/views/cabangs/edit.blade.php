@extends('layouts.master')
@section('title', 'Edit Data Cabang')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
             <div class="page-title-box mt-3">
                 <h4 class="mb-sm-0">Edit Data Cabang</h4>
             </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-8">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('cabangs.update', $cabang->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label>Nama Cabang</label>
                            <input type="text" name="nama_cabang" class="form-control" value="{{ old('nama_cabang', $cabang->nama_cabang) }}" required>
                        </div>
                        <div class="mb-3">
                            <label>SPV Cabang</label>
                            <input type="text" name="spv_cabang" class="form-control" value="{{ old('spv_cabang', $cabang->spv_cabang) }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Target Cabang (Rp)</label>
                            <input type="number" name="target_cabang" class="form-control" value="{{ old('target_cabang', $cabang->target_cabang) }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Pencapaian Bulan Lalu (Rp)</label>
                            <input type="number" name="pencapaian_bulan_lalu" class="form-control" value="{{ old('pencapaian_bulan_lalu', $cabang->pencapaian_bulan_lalu) }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="Aktif" {{ old('status', $cabang->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak Aktif" {{ old('status', $cabang->status) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Update</button>
                        <a href="{{ route('cabangs.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
