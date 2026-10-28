@extends('layouts.master')
@section('title', 'Edit Kantor')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box mt-3">
                <h4 class="mb-sm-0">Edit Kantor</h4>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card">
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

                    <form action="{{ route('kantors.update', $kantor->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Nama Kantor</label>
                            <input type="text" name="nama_kantor" class="form-control" value="{{ old('nama_kantor', $kantor->nama_kantor) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Unit</label>
                            <select name="unit" class="form-control">
                                <option value="">-- Pilih Unit --</option>
                                <option value="NSB" {{ old('unit', $kantor->unit) === 'NSB' ? 'selected' : '' }}>NSB</option>
                                <option value="HAEDA" {{ old('unit', $kantor->unit) === 'HAEDA' ? 'selected' : '' }}>HAEDA</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $kantor->alamat) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="Aktif" {{ old('status', $kantor->status) === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Nonaktif" {{ old('status', $kantor->status) === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">Update</button>
                        <a href="{{ route('kantors.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
