@extends('layouts.master')
@section('title', 'Persetujuan Analisis Kredit | NSB')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Memo Persetujuan Pembiayaan</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('analisis-kredit.index') }}">Analisis Kredit</a></li>
                        <li class="breadcrumb-item active">Persetujuan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Rekomendasi & Persetujuan ({{ $analisa->debitur->nama_peminjam ?? '-' }})</h5>
            <p class="text-muted mb-0">Anda saat ini login sebagai: <span class="badge bg-primary text-uppercase">{{ $roleType ?? 'Unknown' }}</span></p>
        </div>
        <div class="card-body">
            
            <!-- 1. LENDING OFFICER (MARKETING) -->
            <div class="border p-3 mb-4 rounded">
                <h5 class="mb-3">Lending Officer</h5>
                <form action="{{ route('analisis-kredit.approve', $analisa->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-8">
                            <label class="form-label">Rekomendasi / Catatan</label>
                            <textarea name="marketing_note" class="form-control" rows="3" {{ $roleType !== 'marketing' ? 'readonly' : '' }}>{{ old('marketing_note', $analisa->marketing_note) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status Persetujuan</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="marketing_status" id="m_setuju" value="Setuju" {{ old('marketing_status', $analisa->marketing_status) == 'Setuju' ? 'checked' : '' }} {{ $roleType !== 'marketing' ? 'disabled' : '' }}>
                                <label class="form-check-label text-success fw-bold" for="m_setuju">Setuju</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="marketing_status" id="m_tidak" value="Tidak Setuju" {{ old('marketing_status', $analisa->marketing_status) == 'Tidak Setuju' ? 'checked' : '' }} {{ $roleType !== 'marketing' ? 'disabled' : '' }}>
                                <label class="form-check-label text-danger fw-bold" for="m_tidak">Tidak Setuju</label>
                            </div>
                            @if($roleType === 'marketing')
                                <button type="submit" class="btn btn-primary w-100">Approve / Simpan (Marketing)</button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- 2. SPV -->
            <div class="border p-3 mb-4 rounded">
                <h5 class="mb-3">SPV</h5>
                <form action="{{ route('analisis-kredit.approve', $analisa->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-8">
                            <label class="form-label">Rekomendasi / Catatan</label>
                            <textarea name="spv_note" class="form-control" rows="3" {{ $roleType !== 'spv' ? 'readonly' : '' }}>{{ old('spv_note', $analisa->spv_note) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status Persetujuan</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="spv_status" id="s_setuju" value="Setuju" {{ old('spv_status', $analisa->spv_status) == 'Setuju' ? 'checked' : '' }} {{ $roleType !== 'spv' ? 'disabled' : '' }}>
                                <label class="form-check-label text-success fw-bold" for="s_setuju">Setuju</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="spv_status" id="s_tidak" value="Tidak Setuju" {{ old('spv_status', $analisa->spv_status) == 'Tidak Setuju' ? 'checked' : '' }} {{ $roleType !== 'spv' ? 'disabled' : '' }}>
                                <label class="form-check-label text-danger fw-bold" for="s_tidak">Tidak Setuju</label>
                            </div>
                            @if($roleType === 'spv')
                                <button type="submit" class="btn btn-primary w-100">Approve / Simpan (SPV)</button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- 3. AREA CREDIT ANALIS (ANALIS) -->
            <div class="border p-3 mb-4 rounded bg-light">
                <h5 class="mb-3">Area Credit Analis</h5>
                <form action="{{ route('analisis-kredit.approve', $analisa->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-8">
                            <label class="form-label">Rekomendasi / Catatan</label>
                            <textarea name="analis_note" class="form-control" rows="3" {{ $roleType !== 'analis' ? 'readonly' : '' }}>{{ old('analis_note', $analisa->analis_note) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status Persetujuan</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="analis_status" id="a_setuju" value="Setuju" {{ old('analis_status', $analisa->analis_status) == 'Setuju' ? 'checked' : '' }} {{ $roleType !== 'analis' ? 'disabled' : '' }}>
                                <label class="form-check-label text-success fw-bold" for="a_setuju">Setuju</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="analis_status" id="a_tidak" value="Tidak Setuju" {{ old('analis_status', $analisa->analis_status) == 'Tidak Setuju' ? 'checked' : '' }} {{ $roleType !== 'analis' ? 'disabled' : '' }}>
                                <label class="form-check-label text-danger fw-bold" for="a_tidak">Tidak Setuju</label>
                            </div>
                            @if($roleType === 'analis')
                                <button type="submit" class="btn btn-primary w-100">Approve / Simpan (Analis)</button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- 4. KADIV BISNIS -->
            <div class="border p-3 mb-4 rounded">
                <h5 class="mb-3">Kadiv Bisnis</h5>
                <form action="{{ route('analisis-kredit.approve', $analisa->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-8">
                            <label class="form-label">Rekomendasi / Catatan</label>
                            <textarea name="kadivbisnis_note" class="form-control" rows="3" {{ $roleType !== 'kadivbisnis' ? 'readonly' : '' }}>{{ old('kadivbisnis_note', $analisa->kadivbisnis_note) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status Persetujuan</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="kadivbisnis_status" id="k_setuju" value="Setuju" {{ old('kadivbisnis_status', $analisa->kadivbisnis_status) == 'Setuju' ? 'checked' : '' }} {{ $roleType !== 'kadivbisnis' ? 'disabled' : '' }}>
                                <label class="form-check-label text-success fw-bold" for="k_setuju">Setuju</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="kadivbisnis_status" id="k_tidak" value="Tidak Setuju" {{ old('kadivbisnis_status', $analisa->kadivbisnis_status) == 'Tidak Setuju' ? 'checked' : '' }} {{ $roleType !== 'kadivbisnis' ? 'disabled' : '' }}>
                                <label class="form-check-label text-danger fw-bold" for="k_tidak">Tidak Setuju</label>
                            </div>
                            @if($roleType === 'kadivbisnis')
                                <button type="submit" class="btn btn-primary w-100">Approve / Simpan (Kadiv Bisnis)</button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- 5. PENGELOLA -->
            <div class="border p-3 mb-4 rounded bg-light">
                <h5 class="mb-3">Pengelola</h5>
                <form action="{{ route('analisis-kredit.approve', $analisa->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-8">
                            <label class="form-label">Rekomendasi / Catatan</label>
                            <textarea name="pengelola_note" class="form-control" rows="3" {{ $roleType !== 'pengelola' ? 'readonly' : '' }}>{{ old('pengelola_note', $analisa->pengelola_note) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status Persetujuan</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="pengelola_status" id="p_setuju" value="Setuju" {{ old('pengelola_status', $analisa->pengelola_status) == 'Setuju' ? 'checked' : '' }} {{ $roleType !== 'pengelola' ? 'disabled' : '' }}>
                                <label class="form-check-label text-success fw-bold" for="p_setuju">Setuju</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="pengelola_status" id="p_tidak" value="Tidak Setuju" {{ old('pengelola_status', $analisa->pengelola_status) == 'Tidak Setuju' ? 'checked' : '' }} {{ $roleType !== 'pengelola' ? 'disabled' : '' }}>
                                <label class="form-check-label text-danger fw-bold" for="p_tidak">Tidak Setuju</label>
                            </div>
                            @if($roleType === 'pengelola')
                                <button type="submit" class="btn btn-primary w-100">Approve / Simpan (Pengelola)</button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="text-end mt-4">
                <a href="{{ route('analisis-kredit.index') }}" class="btn btn-light">Kembali ke Daftar</a>
            </div>
        </div>
    </div>
</div>
@endsection
