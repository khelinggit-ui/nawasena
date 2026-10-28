@extends('layouts.master')
@section('title', 'Admin Kredit - Tahap 2 | NSB')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Input Debitur - Tahap 2 (Admin Kredit)</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('debitur.index') }}">Debitur</a></li>
                            <li class="breadcrumb-item active">Tahap 2</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        
        <form id="edit-tahap2-form" action="{{ route('debitur.update', $debitur->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-3">
                        <div class="card-body bg-light-subtle">
                            <h6 class="mb-1 text-uppercase text-muted">Review Data Marketing</h6>
                            <p class="mb-0 text-dark fw-medium">Nama Peminjam: {{ $debitur->nama_peminjam }} | NIK: {{ $debitur->nomor_ktp }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="alert alert-info">Lengkapi dan verifikasi data Tahap 2 untuk Admin Kredit, lalu klik Simpan untuk melanjutkan ke Tahap 3 (Analis).</div>
                </div>

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Informasi Peminjaman & Domisili</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Batch <span class="text-danger">*</span></label>
                                    <input type="text" name="batch" class="form-control" required value="{{ $debitur->batch }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Tipe Peminjam</label>
                                    <input type="text" name="tipe_peminjam" class="form-control" placeholder="Contoh: Baru/Repeat" value="{{ $debitur->tipe_peminjam }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Nomor Anggota</label>
                                    <input type="text" name="nomor_anggota" class="form-control" value="{{ $debitur->nomor_anggota }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Pengajuan Plafond <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="pengajuan_plafond" class="form-control" required value="{{ floatval($debitur->pengajuan_plafond) }}">
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <label class="form-label">Alamat Domisili <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="alamat_domisili" rows="2" required placeholder="Jl. Raya Domisili..">{{ $debitur->alamat_domisili }}</textarea>
                                </div>
                                <div class="col-lg-2 col-6">
                                    <label class="form-label">RT</label>
                                    <input type="text" name="rt_domisili" class="form-control" value="{{ $debitur->rt_domisili }}">
                                </div>
                                <div class="col-lg-2 col-6">
                                    <label class="form-label">RW</label>
                                    <input type="text" name="rw_domisili" class="form-control" value="{{ $debitur->rw_domisili }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Provinsi</label>
                                    <select id="select-provinsi-domisili" name="kode_provinsi_domisili" class="form-select">
                                        <option value="">-- Pilih Provinsi --</option>
                                        @php $selectedProvinceDom = \App\Models\Province::whereRaw('LOWER(name) = ?', [strtolower($debitur->provinsi_domisili)])->first(); @endphp
                                        @foreach(\App\Models\Province::orderBy('name')->get() as $prov)
                                            <option value="{{ $prov->id }}" {{ $selectedProvinceDom && $selectedProvinceDom->id == $prov->id ? 'selected' : '' }}>{{ ucwords(strtolower($prov->name)) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="provinsi_domisili" id="input-provinsi-domisili-name" value="{{ $debitur->provinsi_domisili }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Kota/Kabupaten</label>
                                    <select id="select-kabupaten-domisili" name="kode_kabupaten_domisili" class="form-select" {{ $debitur->kota_kabupaten_domisili ? '' : 'disabled' }} data-selected="{{ $debitur->kota_kabupaten_domisili }}">
                                        <option value="">-- Pilih Kota/Kabupaten --</option>
                                        @if($debitur->kota_kabupaten_domisili)
                                            <option value="" selected>{{ $debitur->kota_kabupaten_domisili }}</option>
                                        @endif
                                    </select>
                                    <input type="hidden" name="kota_kabupaten_domisili" id="input-kabupaten-domisili-name" value="{{ $debitur->kota_kabupaten_domisili }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Kecamatan</label>
                                    <select id="select-kecamatan-domisili" name="kode_kecamatan_domisili" class="form-select" {{ $debitur->kecamatan_domisili ? '' : 'disabled' }} data-selected="{{ $debitur->kecamatan_domisili }}">
                                        <option value="">-- Pilih Kecamatan --</option>
                                        @if($debitur->kecamatan_domisili)
                                            <option value="" selected>{{ $debitur->kecamatan_domisili }}</option>
                                        @endif
                                    </select>
                                    <input type="hidden" name="kecamatan_domisili" id="input-kecamatan-domisili-name" value="{{ $debitur->kecamatan_domisili }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Kelurahan / Desa</label>
                                    <select id="select-kelurahan-domisili" name="kode_kelurahan_domisili" class="form-select" {{ $debitur->kelurahan_domisili ? '' : 'disabled' }} data-selected="{{ $debitur->kelurahan_domisili }}">
                                        <option value="">-- Pilih Kelurahan/Desa --</option>
                                        @if($debitur->kelurahan_domisili)
                                            <option value="" selected>{{ $debitur->kelurahan_domisili }}</option>
                                        @endif
                                    </select>
                                    <input type="hidden" name="kelurahan_domisili" id="input-kelurahan-domisili-name" value="{{ $debitur->kelurahan_domisili }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Kode Pos</label>
                                    <input type="text" name="kode_pos_domisili" class="form-control" value="{{ $debitur->kode_pos_domisili }}">
                                </div>
                                <div class="col-lg-12">
                                    <label class="form-label">Status Kepemilikan Rumah</label>
                                    <select class="form-select" name="status_kepemilikan_rumah">
                                        <option value="">Pilih</option>
                                        <option value="Milik Sendiri" {{ $debitur->status_kepemilikan_rumah == 'Milik Sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                                        <option value="Sewa/Kontrak" {{ $debitur->status_kepemilikan_rumah == 'Sewa/Kontrak' ? 'selected' : '' }}>Sewa/Kontrak</option>
                                        <option value="Tinggal dengan Orang Tua" {{ $debitur->status_kepemilikan_rumah == 'Tinggal dengan Orang Tua' ? 'selected' : '' }}>Tinggal dengan Orang Tua</option>
                                        <option value="Lainnya" {{ $debitur->status_kepemilikan_rumah == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Kontak Darurat / Keluarga (Tidak Serumah)</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Nama Keluarga <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_keluarga_tidak_serumah" class="form-control" required placeholder="Nama lengkap keluarga" value="{{ $debitur->nama_keluarga_tidak_serumah }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Hubungan</label>
                                    <input type="text" name="hubungan_keluarga" class="form-control" placeholder="Kakak/Adik/Paman dsb" value="{{ $debitur->hubungan_keluarga }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">No. Handphone Keluarga <span class="text-danger">*</span></label>
                                    <input type="text" name="no_handphone_keluarga" class="form-control" required value="{{ $debitur->no_handphone_keluarga }}">
                                </div>
                                <div class="col-lg-12">
                                    <label class="form-label">Alamat KTP Keluarga</label>
                                    <textarea class="form-control" name="alamat_ktp_keluarga" rows="2">{{ $debitur->alamat_ktp_keluarga }}</textarea>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <label class="form-label">Provinsi</label>
                                    <select id="select-provinsi-ktp-keluarga" name="kode_provinsi_ktp_keluarga" class="form-select">
                                        <option value="">-- Pilih Provinsi --</option>
                                        @php $selectedProvinceKeluarga = \App\Models\Province::whereRaw('LOWER(name) = ?', [strtolower($debitur->provinsi_ktp_keluarga)])->first(); @endphp
                                        @foreach(\App\Models\Province::orderBy('name')->get() as $prov)
                                            <option value="{{ $prov->id }}" {{ $selectedProvinceKeluarga && $selectedProvinceKeluarga->id == $prov->id ? 'selected' : '' }}>{{ ucwords(strtolower($prov->name)) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="provinsi_ktp_keluarga" id="input-provinsi-ktp-keluarga-name" value="{{ $debitur->provinsi_ktp_keluarga }}">
                                </div>
                                <div class="col-lg-3 col-6">
                                    <label class="form-label">Kota/Kabupaten</label>
                                    <select id="select-kabupaten-ktp-keluarga" name="kode_kabupaten_ktp_keluarga" class="form-select" {{ $debitur->kota_kabupaten_ktp_keluarga ? '' : 'disabled' }} data-selected="{{ $debitur->kota_kabupaten_ktp_keluarga }}">
                                        <option value="">-- Pilih Kota/Kabupaten --</option>
                                        @if($debitur->kota_kabupaten_ktp_keluarga)
                                            <option value="" selected>{{ $debitur->kota_kabupaten_ktp_keluarga }}</option>
                                        @endif
                                    </select>
                                    <input type="hidden" name="kota_kabupaten_ktp_keluarga" id="input-kabupaten-ktp-keluarga-name" value="{{ $debitur->kota_kabupaten_ktp_keluarga }}">
                                </div>
                                <div class="col-lg-3 col-6">
                                    <label class="form-label">Kecamatan</label>
                                    <select id="select-kecamatan-ktp-keluarga" name="kode_kecamatan_ktp_keluarga" class="form-select" {{ $debitur->kecamatan_ktp_keluarga ? '' : 'disabled' }} data-selected="{{ $debitur->kecamatan_ktp_keluarga }}">
                                        <option value="">-- Pilih Kecamatan --</option>
                                        @if($debitur->kecamatan_ktp_keluarga)
                                            <option value="" selected>{{ $debitur->kecamatan_ktp_keluarga }}</option>
                                        @endif
                                    </select>
                                    <input type="hidden" name="kecamatan_ktp_keluarga" id="input-kecamatan-ktp-keluarga-name" value="{{ $debitur->kecamatan_ktp_keluarga }}">
                                </div>
                                <div class="col-lg-3 col-6">
                                    <label class="form-label">Kelurahan / Desa</label>
                                    <select id="select-kelurahan-ktp-keluarga" name="kode_kelurahan_ktp_keluarga" class="form-select" {{ $debitur->kelurahan_ktp_keluarga ? '' : 'disabled' }} data-selected="{{ $debitur->kelurahan_ktp_keluarga }}">
                                        <option value="">-- Pilih Kelurahan/Desa --</option>
                                        @if($debitur->kelurahan_ktp_keluarga)
                                            <option value="" selected>{{ $debitur->kelurahan_ktp_keluarga }}</option>
                                        @endif
                                    </select>
                                    <input type="hidden" name="kelurahan_ktp_keluarga" id="input-kelurahan-ktp-keluarga-name" value="{{ $debitur->kelurahan_ktp_keluarga }}">
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Kode Pos</label>
                                    <input type="text" name="kode_pos_ktp_keluarga" class="form-control" value="{{ $debitur->kode_pos_ktp_keluarga }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Keuangan & Pekerjaan Tambahan</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info border-0 rounded-top mb-4">
                                Bagian ini diperlukan untuk verifikasi data profil risiko secara kasar.
                            </div>
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Pendapatan Per Bulan (Verifikasi)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="gaji" class="form-control" value="{{ floatval($debitur->gaji) }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Pengeluaran Per Bulan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="pengeluaran_per_bulan" class="form-control" value="{{ floatval($debitur->pengeluaran_per_bulan) }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Hutang Per Bulan (Cicilan Lain)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="hutang_per_bulan" class="form-control" value="{{ floatval($debitur->hutang_per_bulan) }}">
                                    </div>
                                </div>

                                <div class="col-lg-12 mt-4">
                                    <h6 class="fw-semibold font-size-12 text-uppercase mb-3">Pekerjaan Lainnya (Opsional)</h6>
                                </div>

                                <div class="col-lg-4">
                                    <label class="form-label">Pekerjaan Lain</label>
                                    <input type="text" name="pekerjaan_lain" class="form-control" placeholder="Contoh: Freelance Online" value="{{ $debitur->pekerjaan_lain }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Jenis Pekerjaan Lain</label>
                                    <input type="text" name="jenis_pekerjaan_lain" class="form-control" value="{{ $debitur->jenis_pekerjaan_lain }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Bidang Pekerjaan Lain</label>
                                    <input type="text" name="bidang_pekerjaan_lain" class="form-control" value="{{ $debitur->bidang_pekerjaan_lain }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Pengalaman Kerja Lain (Tahun)</label>
                                    <input type="number" name="pengalaman_kerja_lain_tahun" class="form-control" value="{{ $debitur->pengalaman_kerja_lain_tahun }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Nama Perusahaan Lain (Jika ada)</label>
                                    <input type="text" name="nama_perusahaan_lain" class="form-control" value="{{ $debitur->nama_perusahaan_lain }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Jabatan/Posisi Lain</label>
                                    <input type="text" name="jabatan_posisi_lain" class="form-control" value="{{ $debitur->jabatan_posisi_lain }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Pendapatan Lain</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="pendapatan_lain_rupiah" class="form-control" value="{{ floatval($debitur->pendapatan_lain_rupiah) }}">
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <label class="form-label">Alamat Perusahaan Lain</label>
                                    <textarea name="alamat_perusahaan_lain" class="form-control" rows="1">{{ $debitur->alamat_perusahaan_lain }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- ===== DOKUMEN UPLOAD (Preview) ===== --}}
                    <div class="card mt-3">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">
                                <i class="ri-file-upload-line me-2 text-primary"></i>Dokumen Debitur
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">

                                @php
                                    $docs = [
                                        'foto_ktp'          => ['label' => 'Foto KTP Debitur',      'icon' => 'ri-id-card-line',      'color' => 'primary',   'multiple' => false],
                                        'ktp_pasangan'      => ['label' => 'KTP Pasangan Debitur',  'icon' => 'ri-contacts-line',     'color' => 'success',   'multiple' => false],
                                        'foto_bpjs'         => ['label' => 'Foto Kartu BPJS',       'icon' => 'ri-health-book-line',  'color' => 'warning',   'multiple' => false],
                                        'selfie_ktp'        => ['label' => 'Selfie dengan KTP',     'icon' => 'ri-camera-line',       'color' => 'danger',    'multiple' => false],
                                        'privy_id_file'     => ['label' => 'Privy ID',              'icon' => 'ri-shield-check-line', 'color' => 'info',      'multiple' => false],
                                        'id_card'           => ['label' => 'ID Card',               'icon' => 'ri-id-card-2-line',   'color' => 'secondary', 'multiple' => false],
                                        'slip_gaji'         => ['label' => 'Slip Gaji',             'icon' => 'ri-file-text-line',   'color' => 'success',   'multiple' => false],
                                        'buku_nikah'        => ['label' => 'Buku Nikah',            'icon' => 'ri-book-line',        'color' => 'primary',   'multiple' => false],
                                        'dokumen_pendukung' => ['label' => 'Dokumen Pendukung',     'icon' => 'ri-attachment-line',  'color' => 'dark',      'multiple' => true],
                                        'mutasi_bank'       => ['label' => 'Mutasi Bank',           'icon' => 'ri-bank-line',        'color' => 'warning',   'multiple' => true],
                                    ];
                                @endphp

                                @foreach($docs as $collection => $info)
                                    @php
                                        $isMultiple = $info['multiple'];
                                        $mediaItems = $isMultiple ? $debitur->getMedia($collection) : [$debitur->getFirstMedia($collection)];
                                    @endphp
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label fw-semibold">{{ $info['label'] }}</label>
                                        @if($isMultiple)
                                            @if(count($mediaItems))
                                                <div class="rounded border p-2 bg-light" style="min-height:160px;">
                                                    @foreach($mediaItems as $media)
                                                        <div class="mb-2">
                                                            <div class="d-flex align-items-center justify-content-between">
                                                                <div class="me-2 text-truncate">
                                                                    <i class="{{ $info['icon'] }} text-{{ $info['color'] }} me-2"></i>
                                                                    <span class="small">{{ $media->file_name }}</span>
                                                                </div>
                                                                <a href="{{ parse_url($media->getUrl(), PHP_URL_PATH) }}" target="_blank" class="small">Buka</a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="rounded border d-flex align-items-center justify-content-center bg-light" style="height:160px;">
                                                    <div class="text-center text-muted">
                                                        <i class="{{ $info['icon'] }} fs-1 opacity-40"></i>
                                                        <p class="small mb-0 mt-1">Belum diupload</p>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            @php $media = $mediaItems[0]; @endphp
                                            @if($media)
                                                @if(str_contains($media->mime_type, 'image'))
                                                    <div class="rounded border overflow-hidden" style="height:160px;">
                                                        <img src="{{ parse_url($media->getUrl(), PHP_URL_PATH) }}" alt="{{ $info['label'] }}"
                                                             class="w-100 h-100" style="object-fit:cover;">
                                                    </div>
                                                    <a href="{{ parse_url($media->getUrl(), PHP_URL_PATH) }}" target="_blank"
                                                       class="btn btn-sm btn-outline-{{ $info['color'] }} mt-2 w-100">
                                                        <i class="ri-eye-line me-1"></i>Lihat Full
                                                    </a>
                                                @else
                                                    <div class="rounded border d-flex align-items-center justify-content-center bg-light" style="height:160px;">
                                                        <div class="text-center">
                                                            <i class="ri-file-pdf-line fs-1 text-{{ $info['color'] }}"></i>
                                                            <p class="small mb-0 mt-1 text-muted">{{ $media->file_name }}</p>
                                                        </div>
                                                    </div>
                                                    <a href="{{ parse_url($media->getUrl(), PHP_URL_PATH) }}" target="_blank"
                                                       class="btn btn-sm btn-outline-{{ $info['color'] }} mt-2 w-100">
                                                        <i class="ri-download-line me-1"></i>Download
                                                    </a>
                                                @endif
                                            @else
                                                <div class="rounded border d-flex align-items-center justify-content-center bg-light" style="height:160px;">
                                                    <div class="text-center text-muted">
                                                        <i class="{{ $info['icon'] }} fs-1 opacity-40"></i>
                                                        <p class="small mb-0 mt-1">Belum diupload</p>
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                    
                    {{-- ===== UPLOAD DOKUMEN TAMBAHAN ===== --}}
                    <div class="card mt-3">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">
                                <i class="ri-upload-cloud-2-line me-2 text-primary"></i>Upload Dokumen Tambahan Tahap 2
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Privy ID</label>
                                    <input type="file" name="privy_id_file" class="form-control" accept="image/*,application/pdf">
                                    @if($debitur->getFirstMedia('privy_id_file'))
                                        <div class="form-text">
                                            File saat ini: <a href="{{ parse_url($debitur->getFirstMedia('privy_id_file')->getUrl(), PHP_URL_PATH) }}" target="_blank">{{ $debitur->getFirstMedia('privy_id_file')->file_name }}</a>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">ID Card</label>
                                    <input type="file" name="id_card" class="form-control" accept="image/*,application/pdf">
                                    @if($debitur->getFirstMedia('id_card'))
                                        <div class="form-text">
                                            File saat ini: <a href="{{ parse_url($debitur->getFirstMedia('id_card')->getUrl(), PHP_URL_PATH) }}" target="_blank">{{ $debitur->getFirstMedia('id_card')->file_name }}</a>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Slip Gaji</label>
                                    <input type="file" name="slip_gaji" class="form-control" accept="image/*,application/pdf">
                                    @if($debitur->getFirstMedia('slip_gaji'))
                                        <div class="form-text">
                                            File saat ini: <a href="{{ parse_url($debitur->getFirstMedia('slip_gaji')->getUrl(), PHP_URL_PATH) }}" target="_blank">{{ $debitur->getFirstMedia('slip_gaji')->file_name }}</a>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Buku Nikah</label>
                                    <input type="file" name="buku_nikah" class="form-control" accept="image/*,application/pdf">
                                    @if($debitur->getFirstMedia('buku_nikah'))
                                        <div class="form-text">
                                            File saat ini: <a href="{{ parse_url($debitur->getFirstMedia('buku_nikah')->getUrl(), PHP_URL_PATH) }}" target="_blank">{{ $debitur->getFirstMedia('buku_nikah')->file_name }}</a>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Dokumen Pendukung</label>
                                    <input type="file" name="dokumen_pendukung[]" class="form-control" accept="image/*,application/pdf" multiple>
                                    @if($debitur->getMedia('dokumen_pendukung')->count())
                                        <div class="form-text">
                                            File saat ini:
                                            @foreach($debitur->getMedia('dokumen_pendukung') as $file)
                                                <div><a href="{{ parse_url($file->getUrl(), PHP_URL_PATH) }}" target="_blank">{{ $file->file_name }}</a></div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Saldo JMO</label>
                                    <input type="file" name="saldo_jmo" class="form-control" accept="image/*,application/pdf">
                                    @if($debitur->getFirstMedia('saldo_jmo'))
                                        <div class="form-text">
                                            File saat ini: <a href="{{ parse_url($debitur->getFirstMedia('saldo_jmo')->getUrl(), PHP_URL_PATH) }}" target="_blank">{{ $debitur->getFirstMedia('saldo_jmo')->file_name }}</a>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Mutasi Bank</label>
                                    <input type="file" name="mutasi_bank[]" class="form-control" accept="image/*,application/pdf" multiple>
                                    @if($debitur->getMedia('mutasi_bank')->count())
                                        <div class="form-text">
                                            File saat ini:
                                            @foreach($debitur->getMedia('mutasi_bank') as $file)
                                                <div><a href="{{ parse_url($file->getUrl(), PHP_URL_PATH) }}" target="_blank">{{ $file->file_name }}</a></div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mb-4">
                        <a href="{{ route('debitur.index') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" name="action_type" value="reject" class="btn btn-danger me-2" onclick="return confirm('Tolak pengajuan ini (Tahap 2)?');">Reject</button>
                        <button type="button" class="btn btn-warning me-2" onclick="promptRevisi(this)">Revisi</button>
                        <button type="submit" name="action_type" value="simpan" class="btn btn-primary">Simpan Lanjut Analis (Tahap 3)</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
<script>
function promptRevisi(btn) {
    let alasan = prompt("Masukkan alasan revisi (Wajib Diisi):");
    if (alasan) {
        let form = btn.closest('form');
        let inputAction = document.createElement('input');
        inputAction.type = 'hidden';
        inputAction.name = 'action_type';
        inputAction.value = 'revisi';
        form.appendChild(inputAction);
        
        let inputAlasan = document.createElement('input');
        inputAlasan.type = 'hidden';
        inputAlasan.name = 'alasan_revisi';
        inputAlasan.value = alasan;
        form.appendChild(inputAlasan);
        
        form.submit();
    } else if (alasan === "") {
        alert("Alasan revisi tidak boleh kosong!");
    }
}

function resetSelect(select, placeholder) {
    select.innerHTML = `<option value="">${placeholder}</option>`;
    select.disabled = true;
}

function toTitleCase(str) {
    return str.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
}

function setSelectedOptionByName(select, selectedName) {
    if (!selectedName) return;
    for (let option of select.options) {
        if (option.dataset.name && option.dataset.name.toLowerCase() === selectedName.toLowerCase()) {
            option.selected = true;
            return true;
        }
    }
    return false;
}

function loadWilayah(url, select, placeholder, selectedName) {
    select.disabled = true;
    select.innerHTML = `<option value="">Memuat data...</option>`;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            select.innerHTML = `<option value="">${placeholder}</option>`;
            data.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.dataset.name = item.name;
                opt.textContent = toTitleCase(item.name);
                select.appendChild(opt);
            });
            select.disabled = false;
            if (selectedName) {
                setSelectedOptionByName(select, selectedName);
                const selected = select.options[select.selectedIndex];
                if (selected?.dataset?.name) {
                    select.dispatchEvent(new Event('change'));
                }
            }
        })
        .catch(() => resetSelect(select, '-- Gagal memuat --'));
}

function setupWilayah(prefix) {
    const provinsi = document.getElementById(`select-provinsi-${prefix}`);
    const kabupaten = document.getElementById(`select-kabupaten-${prefix}`);
    const kecamatan = document.getElementById(`select-kecamatan-${prefix}`);
    const kelurahan = document.getElementById(`select-kelurahan-${prefix}`);

    const inputProvinsiName = document.getElementById(`input-provinsi-${prefix}-name`);
    const inputKabupatenName = document.getElementById(`input-kabupaten-${prefix}-name`);
    const inputKecamatanName = document.getElementById(`input-kecamatan-${prefix}-name`);
    const inputKelurahanName = document.getElementById(`input-kelurahan-${prefix}-name`);

    provinsi.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const name = selected?.dataset?.name ?? '';
        inputProvinsiName.value = name;

        resetSelect(kabupaten, '-- Pilih Kota/Kabupaten --');
        resetSelect(kecamatan, '-- Pilih Kecamatan --');
        resetSelect(kelurahan, '-- Pilih Kelurahan/Desa --');
        inputKabupatenName.value = '';
        inputKecamatanName.value = '';
        inputKelurahanName.value = '';

        if (!this.value) return;
        loadWilayah(`/apps-ecommerce/wilayah/kabupaten/${this.value}`, kabupaten, '-- Pilih Kota/Kabupaten --', kabupaten.dataset.selected || '');
    });

    kabupaten.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const name = selected?.dataset?.name ?? '';
        inputKabupatenName.value = name;

        resetSelect(kecamatan, '-- Pilih Kecamatan --');
        resetSelect(kelurahan, '-- Pilih Kelurahan/Desa --');
        inputKecamatanName.value = '';
        inputKelurahanName.value = '';

        if (!this.value) return;
        loadWilayah(`/apps-ecommerce/wilayah/kecamatan/${this.value}`, kecamatan, '-- Pilih Kecamatan --', kecamatan.dataset.selected || '');
    });

    kecamatan.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const name = selected?.dataset?.name ?? '';
        inputKecamatanName.value = name;

        resetSelect(kelurahan, '-- Pilih Kelurahan/Desa --');
        inputKelurahanName.value = '';

        if (!this.value) return;
        loadWilayah(`/apps-ecommerce/wilayah/kelurahan/${this.value}`, kelurahan, '-- Pilih Kelurahan/Desa --', kelurahan.dataset.selected || '');
    });

    kelurahan.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        inputKelurahanName.value = selected?.dataset?.name ?? '';
    });

    if (provinsi.value) {
        provinsi.dispatchEvent(new Event('change'));
    }
}

document.addEventListener('DOMContentLoaded', function () {
    setupWilayah('domisili');
    setupWilayah('ktp-keluarga');
});
</script>
@endsection





