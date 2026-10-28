@extends('layouts.master')
@section('title', 'Template Pesan Pengingat')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box mt-3 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Template Pesan Pengingat</h4>
                    <p class="text-muted mb-0">Gunakan variabel <code>@{{nama}}</code>, <code>@{{angsuran_ke}}</code>, <code>@{{nominal_angsuran}}</code>, <code>@{{tanggal_jatuh_tempo}}</code>, <code>@{{hari_terlambat}}</code>, <code>@{{total_tunggakan}}</code>.</p>
                </div>
                <a href="{{ route('whatsapp.bot.index') }}" class="btn btn-light">
                    <i class="ri-arrow-left-line align-middle me-1"></i> Pengaturan Bot
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <form action="{{ route('whatsapp.bot.templates.update') }}" method="POST">
        @csrf
        <div class="row mt-3">
            @foreach($templates as $template)
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">{{ $template->name }}</h5>
                            <code>{{ $template->key }}</code>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Template</label>
                                <input type="text" name="templates[{{ $template->key }}][name]" class="form-control" value="{{ old('templates.'.$template->key.'.name', $template->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Isi Pesan</label>
                                <textarea name="templates[{{ $template->key }}][body]" class="form-control" rows="8" required>{{ old('templates.'.$template->key.'.body', $template->body) }}</textarea>
                            </div>
                            <div class="form-check form-switch">
                                <input type="hidden" name="templates[{{ $template->key }}][is_active]" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" id="active-{{ $template->key }}" name="templates[{{ $template->key }}][is_active]" value="1" @checked(old('templates.'.$template->key.'.is_active', $template->is_active))>
                                <label class="form-check-label" for="active-{{ $template->key }}">Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-end mb-4">
            <button type="submit" class="btn btn-primary">
                <i class="ri-save-line align-middle me-1"></i> Simpan Template
            </button>
        </div>
    </form>
</div>
@endsection
