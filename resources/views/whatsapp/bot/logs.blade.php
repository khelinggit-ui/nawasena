@extends('layouts.master')
@section('title', 'Riwayat Pengingat WhatsApp')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box mt-3 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Riwayat Pengingat</h4>
                    <p class="text-muted mb-0">Catatan pesan pengingat yang pernah direncanakan atau dikirim.</p>
                </div>
                <a href="{{ route('whatsapp.bot.index') }}" class="btn btn-light">
                    <i class="ri-arrow-left-line align-middle me-1"></i> Pengaturan Bot
                </a>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <form action="{{ route('whatsapp.bot.logs') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">- Semua -</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nomor HP</label>
                    <input type="text" name="phone" class="form-control" value="{{ request('phone') }}" placeholder="0812...">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Debitur</th>
                            <th>HP</th>
                            <th>Tipe</th>
                            <th>Angsuran</th>
                            <th>Status</th>
                            <th>Dikirim</th>
                            <th>Pesan/Error</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ optional($log->reminder_date)->format('d/m/Y') }}</td>
                                <td>
                                    {{ $log->debitur->nama_peminjam ?? '-' }}
                                    <small class="text-muted d-block">{{ $log->debitur->nomor_debitur ?? '' }}</small>
                                </td>
                                <td>{{ $log->phone_number }}</td>
                                <td><span class="badge bg-primary">{{ $log->reminder_type }}</span></td>
                                <td>{{ $log->installment_number }}</td>
                                <td>
                                    @php
                                        $badge = match($log->status) {
                                            'sent', 'delivered', 'read' => 'bg-success',
                                            'failed' => 'bg-danger',
                                            'skipped' => 'bg-secondary',
                                            default => 'bg-warning text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $log->status }}</span>
                                </td>
                                <td>{{ optional($log->sent_at)->format('d/m/Y H:i') ?? '-' }}</td>
                                <td style="max-width: 320px">
                                    @if($log->error_message)
                                        <span class="text-danger">{{ \Illuminate\Support\Str::limit($log->error_message, 80) }}</span>
                                    @else
                                        <span class="text-muted">{{ \Illuminate\Support\Str::limit($log->message, 80) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">Belum ada riwayat pengingat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
