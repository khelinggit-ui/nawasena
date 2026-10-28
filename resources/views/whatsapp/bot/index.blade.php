@extends('layouts.master')
@section('title', 'Pengaturan Bot Pengingat WhatsApp')
@section('content')
@php
    $activeDays = old('active_days', $setting->active_days ?: [1,2,3,4,5,6]);
    $selectedRules = old('rules', $selectedRules);
    $branchIds = old('branch_ids', $setting->branch_ids ?: []);
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box mt-3 d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Pengaturan Bot Pengingat</h4>
                    <p class="text-muted mb-0">Atur kapan dan bagaimana pesan pengingat WhatsApp dikirim ke debitur.</p>
                </div>
                <a href="{{ route('whatsapp.bot.dashboard') }}" class="btn btn-light">
                    <i class="ri-dashboard-3-line align-middle me-1"></i> Dashboard
                </a>
                <a href="{{ route('whatsapp.index') }}" class="btn btn-light">
                    <i class="ri-arrow-left-line align-middle me-1"></i> Instance WhatsApp
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
        <strong>Periksa kembali isian Anda:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row mt-3">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Pengaturan Pengiriman</h5>
                    @if($setting->exists)
                        <span class="badge {{ $setting->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $setting->is_active ? 'AKTIF' : 'NONAKTIF' }}
                        </span>
                    @else
                        <span class="badge bg-secondary">BELUM DISIMPAN</span>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ route('whatsapp.bot.settings.update') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Instance Pengirim</label>
                                <select name="instance_name" class="form-select">
                                    <option value="">- Pilih instance -</option>
                                    @foreach($instances as $instance)
                                        <option value="{{ $instance->instance_name }}" @selected(old('instance_name', $setting->instance_name) === $instance->instance_name)>
                                            {{ $instance->instance_name }} ({{ $instance->status }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Timezone</label>
                                <input type="text" name="timezone" class="form-control" value="{{ old('timezone', $setting->timezone ?: 'Asia/Jakarta') }}">
                            </div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $setting->is_active))>
                            <label class="form-check-label" for="is_active">
                                Aktifkan bot pengingat
                                <small class="text-danger d-block">Setelah aktif, command <code>reminders:send --send</code> akan mulai mengirim pesan.</small>
                            </label>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jam Kirim Mulai</label>
                                <input type="time" name="send_window_start" class="form-control" value="{{ old('send_window_start', substr((string) $setting->send_window_start, 0, 5) ?: '09:00') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jam Kirim Selesai</label>
                                <input type="time" name="send_window_end" class="form-control" value="{{ old('send_window_end', substr((string) $setting->send_window_end, 0, 5) ?: '16:00') }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Batas per Hari</label>
                                <input type="number" name="max_per_day" class="form-control" min="1" max="200" value="{{ old('max_per_day', $setting->max_per_day ?: 50) }}">
                                <small class="text-muted">Maksimal 200 per nomor.</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jeda Minimum (detik)</label>
                                <input type="number" name="delay_min_seconds" class="form-control" min="5" max="600" value="{{ old('delay_min_seconds', $setting->delay_min_seconds ?: 20) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Jeda Maksimum (detik)</label>
                                <input type="number" name="delay_max_seconds" class="form-control" min="5" max="600" value="{{ old('delay_max_seconds', $setting->delay_max_seconds ?: 30) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Hari Aktif</label>
                            @php
                                $dayLabels = [1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab', 7 => 'Min'];
                            @endphp
                            @foreach($dayLabels as $dayValue => $dayLabel)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="day-{{ $dayValue }}" name="active_days[]" value="{{ $dayValue }}" @checked(in_array($dayValue, array_map('intval', (array) $activeDays), true))>
                                    <label class="form-check-label" for="day-{{ $dayValue }}">{{ $dayLabel }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Jadwal Pengingat</label>
                            @foreach($rules as $ruleKey => $rule)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="rule-{{ $ruleKey }}" name="rules[]" value="{{ $ruleKey }}" @checked(in_array($ruleKey, (array) $selectedRules, true))>
                                    <label class="form-check-label" for="rule-{{ $ruleKey }}" title="{{ $rule['label'] }}">
                                        {{ $ruleKey }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="opt_out_enabled" name="opt_out_enabled" value="1" @checked(old('opt_out_enabled', $setting->opt_out_enabled))>
                                <label class="form-check-label" for="opt_out_enabled">Hormati daftar blacklist (opt-out) manual</label>
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3">Data Pendukung Pesan</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kontak CS</label>
                                <input type="text" name="contact_cs" class="form-control" value="{{ old('contact_cs', $setting->contact_cs) }}" placeholder="cth. 0812xxxx">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Bank</label>
                                <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $setting->bank_name) }}" placeholder="cth. BCA">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Rekening</label>
                                <input type="text" name="bank_account_number" class="form-control" value="{{ old('bank_account_number', $setting->bank_account_number) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Pemilik Rekening</label>
                                <input type="text" name="bank_account_name" class="form-control" value="{{ old('bank_account_name', $setting->bank_account_name) }}">
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line align-middle me-1"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Test Kirim Pesan</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Kirim pesan uji ke nomor admin tanpa menunggu jadwal. Nomor default: <strong>081390844197</strong>.</p>
                    <form action="{{ route('whatsapp.bot.test-send') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Instance</label>
                            <select name="instance_name" class="form-select" required>
                                <option value="">- Pilih instance -</option>
                                @foreach($instances as $instance)
                                    <option value="{{ $instance->instance_name }}" @selected($instance->status === 'connected')>
                                        {{ $instance->instance_name }} ({{ $instance->status }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nomor Tujuan</label>
                            <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', '081390844197') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Isi Pesan</label>
                            <textarea name="message" class="form-control" rows="4" required>{{ old('message', "Halo, ini pesan uji dari sistem pengingat WhatsApp NSB. Jika Anda menerima pesan ini, koneksi bot berfungsi dengan baik.") }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="ri-send-plane-line align-middle me-1"></i> Kirim Pesan Uji
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Pratinjau Calon Penerima (Dry Run)</h5>
                    <form action="{{ route('whatsapp.bot.index') }}" method="GET" class="d-flex gap-2">
                        <input type="hidden" name="preview" value="1">
                        <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date', now('Asia/Jakarta')->toDateString()) }}">
                        <input type="number" name="limit" class="form-control form-control-sm" style="width: 90px" placeholder="Limit" value="{{ request('limit') }}">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Hitung</button>
                    </form>
                </div>
                <div class="card-body">
                    @if($preview)
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <tr>
                                    <th>Tanggal</th><td>{{ $preview['date'] }}</td>
                                    <th>Calon Terkirim</th><td>{{ $preview['stats']['candidates_total'] }}</td>
                                </tr>
                                <tr>
                                    <th>Sudah tercatat</th><td>{{ $preview['stats']['today_logged'] }}</td>
                                    <th>Batas harian</th><td>{{ $preview['stats']['daily_cap'] }}</td>
                                </tr>
                                <tr>
                                    <th>Dilewati: lunas</th><td>{{ $preview['stats']['skipped']['lunas'] }}</td>
                                    <th>Dilewati: sudah pernah</th><td>{{ $preview['stats']['skipped']['already_sent'] }}</td>
                                </tr>
                                <tr>
                                    <th>Dilewati: tanpa aturan</th><td>{{ $preview['stats']['skipped']['no_rule'] }}</td>
                                    <th>Dilewati: opt-out</th><td>{{ $preview['stats']['skipped']['opt_out'] }}</td>
                                </tr>
                            </table>
                        </div>

                        @if($preview['candidates']->isEmpty())
                            <p class="text-muted mb-0">Tidak ada calon penerima untuk tanggal ini.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Nomor Debitur</th>
                                            <th>Nama</th>
                                            <th>HP</th>
                                            <th>Tipe</th>
                                            <th>Jatuh Tempo</th>
                                            <th>Telat</th>
                                            <th>Sisa</th>
                                            <th>Total Tunggakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($preview['candidates'] as $candidate)
                                            <tr>
                                                <td>{{ $candidate['nomor_debitur'] }}</td>
                                                <td>{{ $candidate['nama'] }}@if(($candidate['loan_count'] ?? 1) > 1) <span class="badge bg-info">{{ $candidate['loan_count'] }} pinjaman</span>@endif</td>
                                                <td>{{ $candidate['phone_number'] }}</td>
                                                <td><span class="badge bg-primary">{{ $candidate['reminder_type'] }}</span></td>
                                                <td>{{ $candidate['due_date'] }}</td>
                                                <td>{{ $candidate['days_late'] }} hari</td>
                                                <td>Rp {{ number_format((float) $candidate['amount_due'], 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format((float) ($candidate['total_tunggakan_group'] ?? $candidate['total_tunggakan']), 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <h6 class="mt-3">Contoh Pesan</h6>
                            <pre class="bg-light p-3 rounded border" style="white-space: pre-wrap">{{ $preview['candidates']->first()['message'] }}</pre>
                        @endif
                    @else
                        <p class="text-muted mb-0">Klik <strong>Hitung</strong> untuk melihat calon penerima tanpa mengirim pesan apa pun.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
