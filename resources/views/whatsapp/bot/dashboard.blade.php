@extends('layouts.master')
@section('title', 'Dashboard Bot Pengingat WhatsApp')
@section('content')
@php
    $sentStatuses = ['sent', 'delivered', 'read'];
    $trendMax = max(1, (int) collect($trend)->max('total'));
    $botActive = (bool) optional($setting)->is_active;
    $instanceConnected = $instance && $instance->status === 'connected';
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box mt-3 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Dashboard Bot Pengingat</h4>
                    <p class="text-muted mb-0">Ringkasan pengiriman pengingat WhatsApp — {{ $today }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('whatsapp.bot.logs') }}" class="btn btn-light">
                        <i class="ri-history-line align-middle me-1"></i> Riwayat
                    </a>
                    <a href="{{ route('whatsapp.bot.index') }}" class="btn btn-primary">
                        <i class="ri-settings-3-line align-middle me-1"></i> Pengaturan Bot
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="alert {{ $botActive ? 'alert-success' : 'alert-warning' }} d-flex align-items-center justify-content-between mb-3" role="alert">
                <div>
                    <i class="ri-robot-2-line align-middle me-1"></i>
                    Status bot: <strong>{{ $botActive ? 'AKTIF' : 'NONAKTIF' }}</strong>
                    @if($botActive)
                        — pengingat akan dikirim otomatis sesuai jadwal.
                    @else
                        — pengingat belum dikirim otomatis. Aktifkan di <a href="{{ route('whatsapp.bot.index') }}" class="alert-link">Pengaturan Bot</a>.
                    @endif
                </div>
                <div>
                    Instance:
                    @if($instance)
                        <span class="badge {{ $instanceConnected ? 'bg-success' : 'bg-secondary' }}">
                            {{ $instance->instance_name }} ({{ $instance->status }})
                        </span>
                    @else
                        <span class="badge bg-secondary">belum dipilih</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Terkirim Hari Ini</p>
                            <h3 class="mb-0">{{ number_format($todaySent, 0, ',', '.') }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-success-subtle text-success rounded fs-3">
                                <i class="ri-send-plane-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Menunggu (Pending)</p>
                            <h3 class="mb-0">{{ number_format($todayCounts['pending'], 0, ',', '.') }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-warning-subtle text-warning rounded fs-3">
                                <i class="ri-time-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Gagal Hari Ini</p>
                            <h3 class="mb-0">{{ number_format($todayCounts['failed'], 0, ',', '.') }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-danger-subtle text-danger rounded fs-3">
                                <i class="ri-error-warning-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Dilewati Hari Ini</p>
                            <h3 class="mb-0">{{ number_format($todayCounts['skipped'], 0, ',', '.') }}</h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-secondary-subtle text-secondary rounded fs-3">
                                <i class="ri-skip-forward-line"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tren 7 Hari Terakhir</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th style="width: 45%">Volume</th>
                                    <th class="text-end">Terkirim</th>
                                    <th class="text-end">Gagal</th>
                                    <th class="text-end">Dilewati</th>
                                    <th class="text-end">Pending</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trend as $day)
                                    <tr>
                                        <td>{{ $day['label'] }}</td>
                                        <td>
                                            <div class="progress" style="height: 8px">
                                                <div class="progress-bar bg-success" style="width: {{ round($day['sent'] / $trendMax * 100) }}%"></div>
                                                <div class="progress-bar bg-danger" style="width: {{ round($day['failed'] / $trendMax * 100) }}%"></div>
                                                <div class="progress-bar bg-secondary" style="width: {{ round($day['skipped'] / $trendMax * 100) }}%"></div>
                                                <div class="progress-bar bg-warning" style="width: {{ round($day['pending'] / $trendMax * 100) }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $day['total'] }} pesan</small>
                                        </td>
                                        <td class="text-end">{{ $day['sent'] }}</td>
                                        <td class="text-end">{{ $day['failed'] }}</td>
                                        <td class="text-end">{{ $day['skipped'] }}</td>
                                        <td class="text-end">{{ $day['pending'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ringkasan Keseluruhan</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Total terkirim
                            <span class="badge bg-success rounded-pill">{{ number_format($overallSent, 0, ',', '.') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Total gagal
                            <span class="badge bg-danger rounded-pill">{{ number_format($overallCounts['failed'], 0, ',', '.') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Total dilewati
                            <span class="badge bg-secondary rounded-pill">{{ number_format($overallCounts['skipped'], 0, ',', '.') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Nomor unik hari ini
                            <span class="badge bg-primary rounded-pill">{{ number_format($uniquePhonesToday, 0, ',', '.') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Blacklist (opt-out)
                            <span class="badge bg-dark rounded-pill">{{ number_format($optOutTotal, 0, ',', '.') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Opt-out baru hari ini
                            <span class="badge bg-warning text-dark rounded-pill">{{ number_format($optOutToday, 0, ',', '.') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Jenis Pengingat Hari Ini</h5>
                </div>
                <div class="card-body">
                    @forelse($todayByType as $type => $total)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><span class="badge bg-primary">{{ $type }}</span></span>
                            <span class="fw-semibold">{{ $total }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Belum ada pengingat hari ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Aktivitas Terbaru</h5>
                    <a href="{{ route('whatsapp.bot.logs') }}" class="btn btn-sm btn-outline-primary">Lihat semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Debitur</th>
                                    <th>HP</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                    <th>Pesan/Error</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLogs as $log)
                                    <tr>
                                        <td>{{ optional($log->created_at)->format('d/m H:i') }}</td>
                                        <td>
                                            {{ $log->debitur->nama_peminjam ?? '-' }}
                                            <small class="text-muted d-block">{{ $log->debitur->nomor_debitur ?? '' }}</small>
                                        </td>
                                        <td>{{ $log->phone_number }}</td>
                                        <td><span class="badge bg-primary">{{ $log->reminder_type }}</span></td>
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
                                        <td style="max-width: 320px">
                                            @if($log->error_message)
                                                <span class="text-danger">{{ \Illuminate\Support\Str::limit($log->error_message, 60) }}</span>
                                            @else
                                                <span class="text-muted">{{ \Illuminate\Support\Str::limit($log->message, 60) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Belum ada aktivitas pengingat.</td>
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
