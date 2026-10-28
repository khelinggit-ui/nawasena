@extends('layouts.master')
@section('title', 'Setting Webhook WhatsApp')
@section('content')
@php
    $selectedEvents = old('events', $webhook['events'] ?? ['CONNECTION_UPDATE', 'MESSAGES_UPSERT']);
    $webhookUrl = old('url', $webhook['url'] ?? url('/api/webhook/whatsapp'));
    $webhookEnabled = old('enabled', $webhook['enabled'] ?? true);
    $webhookBase64 = old('base64', $webhook['base64'] ?? false);
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box mt-3 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Setting Webhook</h4>
                    <p class="text-muted mb-0">Instance: {{ $instance->instance_name }}</p>
                </div>
                <a href="{{ route('whatsapp.index') }}" class="btn btn-light">
                    <i class="ri-arrow-left-line align-middle me-1"></i> Kembali
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

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($error)
    <div class="alert alert-warning">{{ $error }}</div>
    @endif

    <div class="row mt-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Konfigurasi Webhook</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('whatsapp.settings.update', $instance->instance_name) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="enabled" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" id="enabled"
                                    name="enabled" value="1" @checked($webhookEnabled)>
                                <label class="form-check-label" for="enabled">Aktifkan Webhook</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="url" class="form-label">URL Webhook</label>
                            <input type="url" class="form-control @error('url') is-invalid @enderror" id="url"
                                name="url" value="{{ $webhookUrl }}" placeholder="https://domain-anda.com/api/webhook/whatsapp" required>
                            @error('url')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                URL harus dapat diakses publik oleh server Evolution API.
                                Endpoint aplikasi ini: <code>{{ url('/api/webhook/whatsapp') }}</code>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="hidden" name="base64" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" id="base64"
                                    name="base64" value="1" @checked($webhookBase64)>
                                <label class="form-check-label" for="base64">Kirim media sebagai base64</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Event Webhook</label>
                            @error('events')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                            @enderror
                            <div class="row">
                                @foreach($events as $value => $label)
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="events[]"
                                            id="event-{{ $value }}" value="{{ $value }}"
                                            @checked(in_array($value, (array) $selectedEvents))>
                                        <label class="form-check-label" for="event-{{ $value }}">
                                            {{ $label }} <small class="text-muted">({{ $value }})</small>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line align-middle me-1"></i> Simpan Pengaturan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Status Instance</h5>
                </div>
                <div class="card-body">
                    @php
                        $statusClass = match($instance->status) {
                            'connected' => 'bg-success',
                            'connecting' => 'bg-warning text-dark',
                            default => 'bg-danger',
                        };
                    @endphp
                    <p class="mb-2">
                        <span class="badge {{ $statusClass }}">{{ ucfirst($instance->status) }}</span>
                    </p>
                    <p class="text-muted mb-0">
                        Nomor: {{ $instance->phone_number ?: '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
