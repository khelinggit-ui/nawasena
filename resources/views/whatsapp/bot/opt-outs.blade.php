@extends('layouts.master')
@section('title', 'Blacklist Nomor WhatsApp')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box mt-3 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Blacklist Nomor</h4>
                    <p class="text-muted mb-0">Nomor pada daftar ini tidak akan menerima pesan pengingat.</p>
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

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
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

    <div class="row mt-3">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tambah Nomor</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('whatsapp.bot.opt-outs.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nomor HP</label>
                            <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}" placeholder="0812xxxx" required>
                            <small class="text-muted">Format 08xx atau 62xx akan dinormalisasi otomatis.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alasan (opsional)</label>
                            <input type="text" name="reason" class="form-control" value="{{ old('reason') }}" placeholder="cth. minta berhenti">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-forbid-2-line align-middle me-1"></i> Tambahkan ke Blacklist
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Daftar Blacklist</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nomor</th>
                                    <th>Alasan</th>
                                    <th>Sumber</th>
                                    <th>Ditambahkan</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($optOuts as $optOut)
                                    <tr>
                                        <td>{{ $optOut->id }}</td>
                                        <td>{{ $optOut->phone_number }}</td>
                                        <td>{{ $optOut->reason ?: '-' }}</td>
                                        <td><span class="badge bg-secondary">{{ $optOut->source }}</span></td>
                                        <td>{{ optional($optOut->created_at)->format('d/m/Y H:i') }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('whatsapp.bot.opt-outs.destroy', $optOut->id) }}" method="POST" onsubmit="return confirm('Hapus nomor ini dari blacklist?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Belum ada nomor pada blacklist.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $optOuts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
