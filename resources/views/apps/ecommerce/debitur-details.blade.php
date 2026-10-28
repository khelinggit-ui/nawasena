@extends('layouts.master')
@section('title', 'Debitur Details')
@section('content')
    @php
        $isLunasSemua = false;
        if (($debitur->status_tahap === 'approve' || $debitur->status_tahap === 'approved') && !empty($jadwal_angsuran)) {
            $isLunasSemua = (bool) $debitur->is_lunas || collect($jadwal_angsuran)->every(fn($item) => !empty($item['is_lunas']));
        }
    @endphp
    <!-- Page-content -->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Debitur Details</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Ecommerce</a></li>
                            <li class="breadcrumb-item active">Debitur Details</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xxl-3">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <div class="flex-shrink-0 avatar-md mx-auto">
                                <div class="avatar-title bg-light rounded">
                                    <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="" height="50" class="rounded-circle" />
                                </div>
                            </div>
                            <div class="mt-4 text-center">
                                <h5 class="mb-1">{{ $debitur->nama_peminjam ?? '-' }}</h5>
                                <p class="text-muted">NIK: {{ $debitur->nomor_ktp ?? '-' }}</p>
                            </div>
                            <div class="table-responsive">
                                <table class="table mb-0 table-borderless">
                                    <tbody>
                                        <tr>
                                            <th><span class="fw-medium">Full Name</span></th>
                                            <td>{{ $debitur->nama_peminjam ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Status Pinjaman</span></th>
                                            <td>
                                                @if($debitur->status_tahap === 'reject')
                                                    <span class="badge bg-danger fs-12 mt-1 p-2">Reject</span>
                                                @elseif($debitur->status_tahap === 'approve' || $debitur->status_tahap === 'approved')
                                                    @if($isLunasSemua)
                                                        <span class="badge bg-info text-white fs-12 mt-1 p-2">Lunas</span>
                                                    @else
                                                        <span class="badge bg-success fs-12">Aktif</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Email</span></th>
                                            <td>{{ $debitur->email ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Contact No.</span></th>
                                            <td>{{ $debitur->nomor_handphone ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Address</span></th>
                                            <td>{{ $debitur->alamat_domisili ?? $debitur->alamat_ktp ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Tempat Lahir</span></th>
                                            <td>{{ $debitur->tempat_lahir ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Tanggal Lahir</span></th>
                                            <td>{{ $debitur->tanggal_lahir ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Ibu Kandung</span></th>
                                            <td>{{ $debitur->nama_ibu_kandung ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Pekerjaan</span></th>
                                            <td>{{ $debitur->pekerjaan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Jabatan</span></th>
                                            <td>{{ $debitur->jabatan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Divisi</span></th>
                                            <td>{{ $debitur->nama_perusahaan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Perusahaan</span></th>
                                            <td>{{ $debitur->nama_perusahaan ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--end card-body-->
                </div>
                <!--end card-->
            </div>
            <!--end col-->
            <div class="col-xxl-9">
                <div class="card">
                    <div class="card-header border-0 align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Fasilitas Pinjaman</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-header p-0 border-0 bg-light-subtle">
                        <div class="row g-0 text-center">
                            <div class="col-6 col-md">
                                <div class="p-3 border border-dashed border-start-0">
                                    <h5 class="mb-1">Rp <span class="counter-value" data-target="{{ $summary['nominal_pinjaman'] }}">0</span></h5>
                                    <p class="text-muted mb-0">Total Loan</p>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-6 col-md">
                                <div class="p-3 border border-dashed border-start-0">
                                    <h5 class="mb-1">Rp <span class="counter-value" data-target="{{ $summary['total_paid'] }}">0</span></h5>
                                    <p class="text-muted mb-0">Total Paid</p>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-6 col-md">
                                <div class="p-3 border border-dashed border-start-0">
                                    <h5 class="mb-1">Rp <span class="counter-value" data-target="{{ $summary['remaining_balance'] }}">0</span></h5>
                                    <p class="text-muted mb-0">Outstanding Balance</p>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-6 col-md">
                                <div class="p-3 border border-dashed border-start-0 border-end-0">
                                    <h5 class="mb-1 text-danger"><span class="counter-value" data-target="{{ $summary['bulan_menunggak'] }}">0</span> Months</h5>
                                    <p class="text-muted mb-0">Overdue</p>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-6 col-md">
                                <div class="p-3 border border-dashed border-start-0">
                                    <h5 class="mb-1 text-danger"><span class="counter-value" data-target="{{ $summary['total_hari_telat'] }}">0</span> Hari</h5>
                                    <p class="text-muted mb-0">Total Hari Telat</p>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-6 col-md">
                                <div class="p-3 border border-dashed border-end-0">
                                    <h5 class="mb-1 text-danger">Rp <span class="counter-value" data-target="{{ $summary['total_denda'] }}">0</span></h5>
                                    <p class="text-muted mb-0">Denda</p>
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                    </div>
                    <!-- end card header -->
                </div>
                <!-- end card -->

                @if(isset($linkedLoans) && $linkedLoans->count())
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4 class="card-title mb-0 flex-grow-1">Riwayat Pinjaman Terkait</h4>
                    </div>
                    <div class="card-body">
                        <div class="loan-chain-list">
                            <div class="loan-chain-item current">
                                <div>
                                    <div class="fw-semibold">Pinjaman Saat Ini #{{ $debitur->id }}</div>
                                    <small class="text-muted">{{ $debitur->nama_peminjam ?? '-' }} - {{ $debitur->nomor_ktp ?? '-' }}</small>
                                </div>
                                <span class="badge {{ $isLunasSemua ? 'bg-info-subtle text-info' : 'bg-primary-subtle text-primary' }}">{{ $isLunasSemua ? 'Lunas' : 'Berjalan' }}</span>
                            </div>
                            @foreach($linkedLoans as $loanItem)
                                <div class="loan-chain-item">
                                    <div>
                                        <div class="fw-semibold">Pinjaman #{{ $loanItem['id'] }}</div>
                                        <small class="text-muted">{{ $loanItem['nama_peminjam'] ?? '-' }} - {{ $loanItem['nomor_ktp'] ?? '-' }}</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge {{ $loanItem['status_class'] }}">{{ $loanItem['status_label'] }}</span>
                                        <a href="{{ $loanItem['detail_url'] }}" class="btn btn-soft-secondary btn-sm">Lihat</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                 
                <!-- Fasilitas Pinjaman Card -->
                <div class="card">
                    <div class="card-body">
                        @if($isLunasSemua)
                            <div class="alert alert-info d-flex align-items-center justify-content-between" role="alert">
                                <div>
                                    <i class="ri-repeat-line me-1"></i>
                                    Pinjaman ini sudah lunas. Debitur dapat mengajukan pinjaman kembali dengan data prefill.
                                </div>
                                @if(auth()->user() && auth()->user()->isMarketing())
                                    <form method="POST" action="{{ route('debitur.reapply', $debitur->id) }}" class="ms-2">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-info text-white">
                                            <i class="ri-restart-line me-1"></i>Ajukan Kembali
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-lg-7 mb-2 mb-lg-0">
                                <table class="table table-sm table-borderless mb-0 detail-meta-table">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted">Nasabah (Anggota)</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">[{{ $debitur->id }}] - {{ $debitur->nama_peminjam ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">No Kredit</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">{{ $debitur->nomor_spk ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Jenis Pinjaman</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">{{ $debitur->jenis_pinjaman ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Deskripsi Pinjaman</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">{{ $debitur->tujuan_pinjaman ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Keterangan Jaminan</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">{{ $debitur->jaminanan ?? 'BPJS' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Tenor</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">{{ $summary['tenor_bulan'] }} Bulan</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Tanggal Pengajuan</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">{{ isset($debitur->tanggal_akad_kredit) ? \Carbon\Carbon::parse($debitur->tanggal_akad_kredit)->format('d M Y') : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Tanggal Disetujui</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">{{ isset($debitur->tanggal_akad_kredit) ? \Carbon\Carbon::parse($debitur->tanggal_akad_kredit)->format('d M Y') : '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted align-top">Alamat</td>
                                            <td class="text-center align-top">:</td>
                                            <td class="fw-medium">{{ $debitur->alamat_domisili ?? $debitur->alamat_ktp ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted align-top">Marketing</td>
                                            <td class="text-center align-top">:</td>
                                            <td class="fw-medium">{{ $debitur->marketing->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Jenis Jasa</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">{{ $debitur->jenis_pinjaman ?? '' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-5">
                                <table class="table table-sm table-borderless mb-0 detail-summary-table">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted">Jatuh Tempo</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">{{ $summary['next_jatuh_tempo'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Plafon</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">Rp{{ number_format($summary['nominal_pinjaman'], 2, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Jasa Terhitung</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">Rp{{ number_format($summary['total_jasa_terhitung'], 2, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Total Utang Berjalan</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">Rp{{ number_format($summary['total_utang_berjalan'], 2, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Uang Masuk</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">Rp{{ number_format($summary['total_paid'], 2, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Sisa Pokok Utang</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">Rp{{ number_format($summary['remaining_balance'], 2, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Batch</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">{{ $debitur->batch }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Kantor</td>
                                            <td class="text-center">:</td>
                                            <td class="fw-medium">{{ optional($debitur->kantor)->nama_kantor ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @if($debitur->status_tahap === 'approve' || $debitur->status_tahap === 'approved')
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Jadwal Angsuran</h4>
                        <div class="flex-shrink-0">
                            <a href="{{ route('debitur.print.angsuran', ['id' => $debitur->id]) }}" target="_blank" class="btn btn-soft-info btn-sm">
                                <i class="ri-file-download-line align-middle"></i> Print Kartu Angsuran
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table table-bordered table-centered align-middle table-nowrap mb-0">
                                <thead class="text-muted table-light text-center">
                                    <tr>
                                        <th rowspan="2" class="align-middle text-primary">NO</th>
                                        <th rowspan="2" class="align-middle text-primary">TANGGAL<br>TAGIHAN</th>
                                        <th rowspan="2" class="align-middle text-primary">TELAT<br>(HARI)</th>
                                        <th colspan="3" class="text-primary border-bottom-0 pb-0">TAGIHAN (RP)</th>
                                        <th colspan="3" class="text-primary border-bottom-0 pb-0">BAYAR (RP)</th>
                                        <th rowspan="2" class="align-middle text-primary">KETERANGAN</th>
                                        <th rowspan="2" class="align-middle text-primary">STATUS</th>
                                        <th rowspan="2" class="align-middle text-primary">ACTION</th>
                                    </tr>
                                    <tr>
                                        <th class="text-primary pt-0">POKOK</th>
                                        <th class="text-primary pt-0">JASA</th>
                                        <th class="text-primary pt-0">TOTAL</th>
                                        <th class="text-primary pt-0">POKOK DIBAYAR</th>
                                        <th class="text-primary pt-0">JASA DIBAYAR</th>
                                        <th class="text-primary pt-0">OUTSTANDING</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($jadwal_angsuran as $item)
                                        <tr>
                                            <td class="text-center">{{ $item['urutan'] }}</td>
                                            <td class="text-center">{{ $item['tgl_tagihan'] }}</td>
                                            <td class="text-center">{{ $item['hari_telat'] > 0 ? $item['hari_telat'] : '-' }}</td>
                                            <td class="text-end">{{ number_format($item['pokok'], 2, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($item['jasa'], 2, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format($item['total_angsuran'], 2, ',', '.') }}</td>
                                            <td class="text-end">{{ $item['has_payment'] ? number_format($item['pokok_dibayarkan'], 2, ',', '.') : '-' }}</td>
                                            <td class="text-end">{{ $item['has_payment'] ? number_format($item['jasa_dibayarkan'], 2, ',', '.') : '-' }}</td>
                                            <td class="text-end">{{ number_format($item['running_outstanding'], 2, ',', '.') }}</td>
                                            <td>{{ $item['has_payment'] ? 'Waktu bayar: ' . $item['tgl_bayar'] : '' }}</td>
                                            <td>
                                                @if($item['is_lunas'])
                                                    <span class="badge bg-success">Lunas</span>
                                                @elseif($item['is_sebagian'])
                                                    <span class="badge bg-warning">Sebagian</span>
                                                @else
                                                    <span class="badge bg-danger">Belum</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @can('manage payment')
                                                    @if(!$item['is_lunas'])
                                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal" onclick="openPaymentModal({{ $item['urutan'] }}, {{ $item['total_angsuran'] }})">Bayar</button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-soft-secondary" disabled>Bayar</button>
                                                    @endif
                                                @else
                                                    -
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <!-- CARD: Berkas Upload -->
                @php
                    $berkasDocs = [
                        'foto_ktp'          => ['label' => 'Foto KTP Debitur',      'icon' => 'ri-id-card-line',      'color' => 'primary',   'multiple' => false],
                        'ktp_pasangan'      => ['label' => 'KTP Pasangan Debitur',  'icon' => 'ri-contacts-line',     'color' => 'success',   'multiple' => false],
                        'foto_bpjs'         => ['label' => 'Foto Kartu BPJS',       'icon' => 'ri-health-book-line',  'color' => 'warning',   'multiple' => false],
                        'selfie_ktp'        => ['label' => 'Selfie dengan KTP',     'icon' => 'ri-camera-line',       'color' => 'danger',    'multiple' => false],
                        'kartu_keluarga'    => ['label' => 'Kartu Keluarga',        'icon' => 'ri-group-line',        'color' => 'info',      'multiple' => false],
                        'privy_id_file'     => ['label' => 'Privy ID',              'icon' => 'ri-shield-check-line', 'color' => 'info',      'multiple' => false],
                        'id_card'           => ['label' => 'ID Card',               'icon' => 'ri-id-card-2-line',   'color' => 'secondary', 'multiple' => false],
                        'slip_gaji'         => ['label' => 'Slip Gaji',             'icon' => 'ri-file-text-line',   'color' => 'success',   'multiple' => false],
                        'buku_nikah'        => ['label' => 'Buku Nikah',            'icon' => 'ri-book-line',        'color' => 'primary',   'multiple' => false],
                        'saldo_jmo'         => ['label' => 'Saldo JMO',             'icon' => 'ri-wallet-3-line',     'color' => 'warning',   'multiple' => false],
                        'mutasi_bank'       => ['label' => 'Mutasi Bank',           'icon' => 'ri-bank-line',        'color' => 'warning',   'multiple' => true],
                        'dokumen_pendukung' => ['label' => 'Dokumen Pendukung',     'icon' => 'ri-attachment-line',  'color' => 'dark',      'multiple' => true],
                    ];

                    $sortedKeys = collect(array_keys($berkasDocs))->sortBy(function ($collection) use ($berkasDocs, $debitur) {
                        $info = $berkasDocs[$collection];
                        $media = $info['multiple'] ? $debitur->getMedia($collection) : collect([$debitur->getFirstMedia($collection)])->filter();
                        return $media->isNotEmpty() ? $media->max('created_at') : '1970-01-01';
                    }, SORT_DESC)->values()->all();

                    $berkasDocs = collect($sortedKeys)->mapWithKeys(fn($k) => [$k => $berkasDocs[$k]])->all();
                @endphp
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4 class="card-title mb-0 flex-grow-1">
                            <i class="ri-file-upload-line me-2 text-primary"></i>Berkas Upload
                        </h4>
                        @php
                            $totalBerkas = 0;
                            foreach ($berkasDocs as $collection => $info) {
                                $totalBerkas += $info['multiple']
                                    ? $debitur->getMedia($collection)->count()
                                    : ($debitur->getFirstMedia($collection) ? 1 : 0);
                            }
                        @endphp
                        <span class="badge bg-soft-primary fs-12">{{ $totalBerkas }} Berkas</span>
                    </div>
                    <div class="card-body">
                        @if($totalBerkas > 0)
                            <div class="row g-3">
                                @foreach($berkasDocs as $collection => $info)
                                    @php
                                        $isMultiple = $info['multiple'];
                                        $mediaItems = $isMultiple ? $debitur->getMedia($collection) : [$debitur->getFirstMedia($collection)];
                                    @endphp
                                    <div class="col-lg-3 col-md-6">
                                        <div class="berkas-item rounded border p-2 h-100">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="fw-semibold small">
                                                    <i class="{{ $info['icon'] }} text-{{ $info['color'] }} me-1"></i>{{ $info['label'] }}
                                                </span>
                                            </div>
                                            @if($isMultiple)
                                                @if(count($mediaItems))
                                                    @foreach($mediaItems as $media)
                                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                                            <div class="me-2 text-truncate">
                                                                <i class="ri-file-line text-muted me-1"></i>
                                                                <span class="small">{{ $media->file_name }}</span>
                                                            </div>
                                                            <a href="{{ parse_url($media->getUrl(), PHP_URL_PATH) }}" target="_blank" class="small" title="Buka file">
                                                                <i class="ri-external-link-line"></i>
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="berkas-empty text-muted small">Belum diupload</div>
                                                @endif
                                            @else
                                                @php $media = $mediaItems[0]; @endphp
                                                @if($media)
                                                    @if(str_contains($media->mime_type, 'image'))
                                                        <a href="{{ parse_url($media->getUrl(), PHP_URL_PATH) }}" target="_blank" class="d-block berkas-thumb rounded overflow-hidden">
                                                            <img src="{{ parse_url($media->getUrl(), PHP_URL_PATH) }}" alt="{{ $info['label'] }}" class="w-100" style="height:110px;object-fit:cover;">
                                                        </a>
                                                        <div class="mt-2 d-flex align-items-center justify-content-between">
                                                            <span class="small text-truncate me-2">{{ $media->file_name }}</span>
                                                            <a href="{{ parse_url($media->getUrl(), PHP_URL_PATH) }}" target="_blank" class="btn btn-sm btn-soft-{{ $info['color'] }} flex-shrink-0">
                                                                <i class="ri-eye-line me-1"></i>Lihat
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="berkas-file text-center py-3">
                                                            <i class="ri-file-pdf-line fs-1 text-{{ $info['color'] }}"></i>
                                                            <p class="small mb-1 text-truncate" title="{{ $media->file_name }}">{{ $media->file_name }}</p>
                                                            <a href="{{ parse_url($media->getUrl(), PHP_URL_PATH) }}" target="_blank" class="btn btn-sm btn-soft-{{ $info['color'] }}">
                                                                <i class="ri-download-line me-1"></i>Download
                                                            </a>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="berkas-empty text-muted small">Belum diupload</div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="ri-file-upload-line fs-1 text-muted"></i>
                                <p class="text-muted mb-0 mt-2">Belum ada berkas yang diupload</p>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- CARD: Activities -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Activities</h5>
                            <div class="acitivity-timeline">
                                @forelse($histories as $history)
                                <div class="acitivity-item d-flex py-3">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs acitivity-avatar">
                                            <div class="avatar-title bg-primary-subtle text-primary rounded-circle material-shadow">
                                                {{ substr($history->user->name ?? 'System', 0, 1) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">
                                            {{ $history->user->name ?? 'System' }}
                                            <span class="badge bg-info-subtle text-info align-middle ms-1">{{ ucfirst(str_replace('_', ' ', $history->status_tahap)) }}</span>
                                        </h6>
                                        @if($history->catatan)
                                        <p class="text-muted mb-2">{{ $history->catatan }}</p>
                                        @endif
                                        <small class="mb-0 text-muted">{{ $history->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-4">
                                    <p class="text-muted">Belum ada histori aktivitas</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </div>
    <!-- End Page-content -->

    <!-- Modal Pembayaran Angsuran -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('debitur.payment') }}" method="POST" onsubmit="return confirmPayment()">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="paymentModalLabel">Bayar Angsuran Bulan Ke-<span id="displayBulan"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_debitur" value="{{ $debitur->id }}">
                        <input type="hidden" name="urutan_bulan" id="inputUrutanBulan" value="">
                        
                        <div class="mb-3">
                            <label class="form-label">Total Tagihan (Rp)</label>
                            <input type="text" class="form-control" id="displayTotalTagihan" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tipe Pembayaran</label>
                            <select class="form-select" id="tipePembayaran" name="tipe_pembayaran" required onchange="changeTipePembayaran()">
                                <option value="1">Full (Lunas)</option>
                                <option value="2">Sebagian</option>
                                <option value="3">Lunas Keseluruhan</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nominal Bayar (Rp)</label>
                            <input type="number" class="form-control" name="nominal_bayar" id="inputNominalBayar" required readonly>
                        </div>
                        <input type="hidden" name="status" id="inputStatus" value="1">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <style>
    .detail-meta-table td:nth-child(1),
    .detail-summary-table td:nth-child(1) {
        width: 40%;
        white-space: normal;
    }

    .berkas-item {
        background: #fff;
        display: flex;
        flex-direction: column;
    }

    .berkas-thumb img {
        transition: transform .2s ease;
    }

    .berkas-thumb:hover img {
        transform: scale(1.05);
    }

    .berkas-empty {
        border: 1px dashed #e2e8f0;
        border-radius: 8px;
        padding: 22px 8px;
        text-align: center;
        background: #f8fafc;
    }

    .berkas-file {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
    }

    .loan-chain-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .loan-chain-item {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        background: #fff;
    }

    .loan-chain-item.current {
        border-color: #93c5fd;
        background: #f0f7ff;
    }

    .detail-meta-table td:nth-child(2),
    .detail-summary-table td:nth-child(2) {
        width: 4%;
    }

    .detail-meta-table td:nth-child(3),
    .detail-summary-table td:nth-child(3) {
        width: 56%;
    }

    @media (max-width: 575.98px) {
        .detail-meta-table,
        .detail-summary-table {
            font-size: 13px;
        }

        .detail-meta-table td:nth-child(1),
        .detail-summary-table td:nth-child(1) {
            width: 45%;
        }

        .detail-meta-table td:nth-child(3),
        .detail-summary-table td:nth-child(3) {
            width: 51%;
            font-weight: 600;
        }
    }
    </style>

    <script>
    function confirmPayment() {
        let tipe = document.getElementById('tipePembayaran').value;
        if (tipe == "3") {
            return confirm("Apakah Anda yakin ingin melakukan pelunasan keseluruhan?\nSeluruh sisa jadwal angsuran akan ditandai Lunas!");
        }
        return true;
    }
    
    let currentTagihan = 0;
    let totalTenor = {{ intval($debitur->tenor_bulan ?? 1) }};
    
    function openPaymentModal(bulan, tagihan) {
        currentTagihan = tagihan;
        document.getElementById('displayBulan').innerText = bulan;
        document.getElementById('inputUrutanBulan').value = bulan;
        
        let rupiahFormat = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2 }).format(tagihan);
        document.getElementById('displayTotalTagihan').value = rupiahFormat;
        
        // Reset to Full
        document.getElementById('tipePembayaran').value = "1";
        changeTipePembayaran();
    }
    
    function changeTipePembayaran() {
        let tipe = document.getElementById('tipePembayaran').value;
        let inputNominal = document.getElementById('inputNominalBayar');
        let inputStatus = document.getElementById('inputStatus');
        
        if(tipe == "1") {
            // Full
            inputNominal.value = currentTagihan;
            inputNominal.readOnly = true;
            inputStatus.value = 1;
        } else if(tipe == "2") {
            // Sebagian
            inputNominal.value = "";
            inputNominal.readOnly = false;
            inputStatus.value = 2;
        } else if(tipe == "3") {
            // Lunas Keseluruhan
            let currBulan = parseInt(document.getElementById('inputUrutanBulan').value);
            let sisaBulan = (totalTenor - currBulan) + 1;
            inputNominal.value = currentTagihan * sisaBulan;
            inputNominal.readOnly = true;
            inputStatus.value = 1;
        }
    }
    </script>
@endsection
