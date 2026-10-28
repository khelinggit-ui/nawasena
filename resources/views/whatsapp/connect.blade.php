@extends('layouts.master')
@section('title', 'Koneksi WhatsApp')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box mt-3 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Koneksi WhatsApp</h4>
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

    <div class="row mt-3">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail Instance</h5>
                </div>
                <div class="card-body">
                    @php
                        $statusClass = match($instance->status) {
                            'connected' => 'bg-success',
                            'connecting' => 'bg-warning text-dark',
                            default => 'bg-danger',
                        };
                    @endphp
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <th class="text-muted" style="width: 40%">Nama Instance</th>
                            <td>{{ $instance->instance_name }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Nomor WhatsApp</th>
                            <td>{{ $instance->phone_number ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Status</th>
                            <td><span class="badge {{ $statusClass }}">{{ ucfirst($instance->status) }}</span></td>
                        </tr>
                        <tr>
                            <th class="text-muted">Dibuat</th>
                            <td>{{ $instance->created_at?->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Scan QR Code</h5>
                </div>
                <div class="card-body text-center">
                    @if($instance->status === 'connected')
                    <div class="alert alert-success mb-0">
                        Instance sudah terhubung ke WhatsApp. Tidak perlu scan QR code.
                    </div>
                    @elseif($error)
                    <div class="alert alert-danger mb-0">{{ $error }}</div>
                    @elseif($qrCode)
                    <img src="{{ $qrCode }}" alt="WhatsApp QR Code" class="img-fluid mb-3" style="max-width: 320px;">
                    <p class="text-muted mb-0">
                        Buka WhatsApp di ponsel Anda, pilih <strong>Perangkat Tertaut</strong>,
                        lalu pindai QR code di atas.
                    </p>
                    @else
                    <div class="alert alert-info mb-0">
                        QR code tidak tersedia. Instance mungkin sudah terhubung atau belum siap.
                        Muat ulang halaman ini untuk mengambil QR code terbaru.
                    </div>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('whatsapp.show', $instance->instance_name) }}" class="btn btn-outline-primary">
                            <i class="ri-refresh-line align-middle me-1"></i> Muat Ulang QR
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
