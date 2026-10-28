@extends('layouts.master')
@section('title', 'WhatsApp API')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box mt-3">
                <h4 class="mb-sm-0">WhatsApp API</h4>
                <p class="text-muted mb-0">Kelola instance WhatsApp melalui Evolution API.</p>
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

    <div class="row mt-3">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Buat Instance Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('whatsapp.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="instance_name" class="form-label">Nama Instance</label>
                            <input type="text" class="form-control @error('instance_name') is-invalid @enderror"
                                id="instance_name" name="instance_name" value="{{ old('instance_name') }}"
                                placeholder="contoh: cs_utama" required>
                            @error('instance_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Gunakan huruf, angka, garis bawah, atau tanda hubung.</div>
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Nomor HP</label>
                            <input type="tel" class="form-control @error('phone_number') is-invalid @enderror"
                                id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                                placeholder="contoh: 628123456789">
                            @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Opsional. Nomor akan tersimpan sebagai informasi instance.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-add-line align-middle me-1"></i> Buat Instance
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Daftar Instance</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Instance</th>
                                    <th>Nomor</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($instances as $instance)
                                @php
                                    $statusClass = match($instance->status) {
                                        'connected' => 'bg-success',
                                        'connecting' => 'bg-warning text-dark',
                                        default => 'bg-danger',
                                    };
                                @endphp
                                <tr>
                                    <td class="fw-medium">{{ $instance->instance_name }}</td>
                                    <td>{{ $instance->phone_number ?: '-' }}</td>
                                    <td>
                                        <span class="badge {{ $statusClass }}">{{ ucfirst($instance->status) }}</span>
                                    </td>
                                    <td>{{ $instance->created_at?->format('d M Y H:i') }}</td>
                                    <td>
                                        <div class="d-flex gap-2 flex-wrap">
                                            @if($instance->status === 'connected')
                                            <form action="{{ route('whatsapp.disconnect', $instance->instance_name) }}" method="POST"
                                                onsubmit="return confirm('Putuskan koneksi instance {{ $instance->instance_name }}?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="ri-link-unlink align-middle me-1"></i> Disconnect
                                                </button>
                                            </form>
                                            @else
                                            <a href="{{ route('whatsapp.show', $instance->instance_name) }}" class="btn btn-sm btn-primary">
                                                <i class="ri-qr-code-line align-middle me-1"></i> QR / Koneksi
                                            </a>
                                            @endif

                                            <a href="{{ route('whatsapp.settings', $instance->instance_name) }}" class="btn btn-sm btn-secondary">
                                                <i class="ri-settings-3-line align-middle me-1"></i> Setting
                                            </a>

                                            <form action="{{ route('whatsapp.destroy', $instance->instance_name) }}" method="POST"
                                                onsubmit="return confirm('Hapus instance {{ $instance->instance_name }} secara permanen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="ri-delete-bin-6-line align-middle me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada instance WhatsApp.</td>
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
