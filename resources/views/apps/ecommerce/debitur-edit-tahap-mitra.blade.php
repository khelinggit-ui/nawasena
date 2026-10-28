@extends('layouts.master')
@section('title', 'Mitra - Tahap Final | NSB')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Review Debitur - Tahap Final (Mitra)</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('debitur.index') }}">Debitur</a></li>
                            <li class="breadcrumb-item active">Tahap Final Mitra</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <form id="edit-tahap-mitra-form" action="{{ route('debitur.update.tahap.mitra', $debitur->id) }}" method="POST">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <strong><i class="ri-error-warning-line me-1"></i>Terdapat kesalahan:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-3">
                        <div class="card-body bg-light-subtle">
                            <h6 class="mb-1 text-uppercase text-muted">Data Peminjam</h6>
                            <p class="mb-0 text-dark fw-medium">Nama: {{ $debitur->nama_peminjam }} | NIK: {{ $debitur->nomor_ktp }} | Batch: {{ $debitur->batch }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card mb-3">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Review Tahap 1 - Data Debitur</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $namaProvinsiKtp = $debitur->provinsi_ktp ?: optional(\App\Models\Province::find($debitur->kode_provinsi))->name;
                                $namaKabupatenKtp = $debitur->kota_kabupaten_ktp ?: optional(\App\Models\Regency::find($debitur->kode_kabupaten))->name;
                                $namaKecamatanKtp = $debitur->kecamatan_ktp ?: optional(\App\Models\District::find($debitur->kode_kecamatan))->name;
                                $namaKelurahanKtp = $debitur->kelurahan_ktp ?: optional(\App\Models\Village::find($debitur->kode_kelurahan))->name;
                            @endphp
                            <dl class="row">
                                <dt class="col-sm-3">Email</dt>
                                <dd class="col-sm-9">{{ $debitur->email ?: '-' }}</dd>

                                <dt class="col-sm-3">Nomor Handphone</dt>
                                <dd class="col-sm-9">{{ $debitur->nomor_handphone ?: '-' }}</dd>

                                <dt class="col-sm-3">Privy ID</dt>
                                <dd class="col-sm-9">{{ $debitur->privy_id ?: '-' }}</dd>

                                <dt class="col-sm-3">Jenis Kelamin</dt>
                                <dd class="col-sm-9">{{ $debitur->jenis_kelamin ?: '-' }}</dd>

                                <dt class="col-sm-3">Tempat Lahir</dt>
                                <dd class="col-sm-9">{{ $debitur->tempat_lahir ?: '-' }}</dd>

                                <dt class="col-sm-3">Tanggal Lahir</dt>
                                <dd class="col-sm-9">{{ $debitur->tanggal_lahir ? \Carbon\Carbon::parse($debitur->tanggal_lahir)->format('d M Y') : '-' }}</dd>

                                <dt class="col-sm-3">Nama Ibu Kandung</dt>
                                <dd class="col-sm-9">{{ $debitur->nama_ibu_kandung ?: '-' }}</dd>

                                <dt class="col-sm-3">Agama</dt>
                                <dd class="col-sm-9">{{ $debitur->agama ?: '-' }}</dd>

                                <dt class="col-sm-3">Pendidikan</dt>
                                <dd class="col-sm-9">{{ $debitur->pendidikan ?: '-' }}</dd>

                                <dt class="col-sm-3">Status Perkawinan</dt>
                                <dd class="col-sm-9">{{ $debitur->status_perkawinan ?: '-' }}</dd>

                                <dt class="col-sm-3">Alamat KTP Lengkap</dt>
                                <dd class="col-sm-9">{{ $debitur->alamat_ktp ?: '-' }}</dd>

                                <dt class="col-sm-3">RT</dt>
                                <dd class="col-sm-9">{{ $debitur->rt_ktp ?: '-' }}</dd>

                                <dt class="col-sm-3">RW</dt>
                                <dd class="col-sm-9">{{ $debitur->rw_ktp ?: '-' }}</dd>

                                <dt class="col-sm-3">Provinsi</dt>
                                <dd class="col-sm-9">{{ $namaProvinsiKtp ? ucwords(strtolower($namaProvinsiKtp)) : '-' }}</dd>

                                <dt class="col-sm-3">Kota/Kabupaten</dt>
                                <dd class="col-sm-9">{{ $namaKabupatenKtp ? ucwords(strtolower($namaKabupatenKtp)) : '-' }}</dd>

                                <dt class="col-sm-3">Kecamatan</dt>
                                <dd class="col-sm-9">{{ $namaKecamatanKtp ? ucwords(strtolower($namaKecamatanKtp)) : '-' }}</dd>

                                <dt class="col-sm-3">Kelurahan/Desa</dt>
                                <dd class="col-sm-9">{{ $namaKelurahanKtp ? ucwords(strtolower($namaKelurahanKtp)) : '-' }}</dd>

                                <dt class="col-sm-3">Kode Pos</dt>
                                <dd class="col-sm-9">{{ $debitur->kode_pos_ktp ?: '-' }}</dd>

                                <dt class="col-sm-3">Nama Perusahaan</dt>
                                <dd class="col-sm-9">{{ $debitur->nama_perusahaan ?: '-' }}</dd>

                                <dt class="col-sm-3">Jabatan / Posisi</dt>
                                <dd class="col-sm-9">{{ $debitur->jabatan_posisi ?: '-' }}</dd>

                                <dt class="col-sm-3">Pengalaman Kerja</dt>
                                <dd class="col-sm-9">{{ $debitur->pengalaman_kerjatahun ?: '-' }} tahun</dd>

                                <dt class="col-sm-3">Alamat Perusahaan</dt>
                                <dd class="col-sm-9">{{ $debitur->alamat_perusahaan ?: '-' }}</dd>

                                <dt class="col-sm-3">Nama Bank</dt>
                                <dd class="col-sm-9">{{ $debitur->nama_bank ?: '-' }}</dd>

                                <dt class="col-sm-3">Nama Pemilik Rekening</dt>
                                <dd class="col-sm-9">{{ $debitur->nama_pemilik_rekening ?: '-' }}</dd>

                                <dt class="col-sm-3">Nomor Rekening</dt>
                                <dd class="col-sm-9">{{ $debitur->nomer_rekening ?: '-' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card mb-3">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Review Tahap 2 - Pinjaman & Domisili</h5>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-3">Batch</dt>
                                <dd class="col-sm-9">{{ $debitur->batch ?: '-' }}</dd>

                                <dt class="col-sm-3">Tipe Peminjam</dt>
                                <dd class="col-sm-9">{{ $debitur->tipe_peminjam ?: '-' }}</dd>

                                <dt class="col-sm-3">Nomor Anggota</dt>
                                <dd class="col-sm-9">{{ $debitur->nomor_anggota ?: '-' }}</dd>

                                <dt class="col-sm-3">Pengajuan Plafond</dt>
                                <dd class="col-sm-9">Rp {{ number_format(floatval($debitur->pengajuan_plafond), 0, ',', '.') }}</dd>

                                <dt class="col-sm-3">Alamat Domisili</dt>
                                <dd class="col-sm-9">{{ $debitur->alamat_domisili ?: '-' }}</dd>

                                <dt class="col-sm-3">RT</dt>
                                <dd class="col-sm-9">{{ $debitur->rt_domisili ?: '-' }}</dd>

                                <dt class="col-sm-3">RW</dt>
                                <dd class="col-sm-9">{{ $debitur->rw_domisili ?: '-' }}</dd>

                                <dt class="col-sm-3">Provinsi</dt>
                                <dd class="col-sm-9">{{ $debitur->provinsi_domisili ?: '-' }}</dd>

                                <dt class="col-sm-3">Kota/Kabupaten</dt>
                                <dd class="col-sm-9">{{ $debitur->kota_kabupaten_domisili ?: '-' }}</dd>

                                <dt class="col-sm-3">Kecamatan</dt>
                                <dd class="col-sm-9">{{ $debitur->kecamatan_domisili ?: '-' }}</dd>

                                <dt class="col-sm-3">Kelurahan/Desa</dt>
                                <dd class="col-sm-9">{{ $debitur->kelurahan_domisili ?: '-' }}</dd>

                                <dt class="col-sm-3">Kode Pos</dt>
                                <dd class="col-sm-9">{{ $debitur->kode_pos_domisili ?: '-' }}</dd>

                                <dt class="col-sm-3">Status Kepemilikan Rumah</dt>
                                <dd class="col-sm-9">{{ $debitur->status_kepemilikan_rumah ?: '-' }}</dd>

                                <dt class="col-sm-3">Nama Keluarga</dt>
                                <dd class="col-sm-9">{{ $debitur->nama_keluarga_tidak_serumah ?: '-' }}</dd>

                                <dt class="col-sm-3">Hubungan</dt>
                                <dd class="col-sm-9">{{ $debitur->hubungan_keluarga ?: '-' }}</dd>

                                <dt class="col-sm-3">No. HP Keluarga</dt>
                                <dd class="col-sm-9">{{ $debitur->no_handphone_keluarga ?: '-' }}</dd>

                                <dt class="col-sm-3">Alamat KTP Keluarga</dt>
                                <dd class="col-sm-9">{{ $debitur->alamat_ktp_keluarga ?: '-' }}</dd>

                                <dt class="col-sm-3">Provinsi Keluarga</dt>
                                <dd class="col-sm-9">{{ $debitur->provinsi_ktp_keluarga ?: '-' }}</dd>

                                <dt class="col-sm-3">Kota/Kabupaten Keluarga</dt>
                                <dd class="col-sm-9">{{ $debitur->kota_kabupaten_ktp_keluarga ?: '-' }}</dd>

                                <dt class="col-sm-3">Kecamatan Keluarga</dt>
                                <dd class="col-sm-9">{{ $debitur->kecamatan_ktp_keluarga ?: '-' }}</dd>

                                <dt class="col-sm-3">Kelurahan/Desa Keluarga</dt>
                                <dd class="col-sm-9">{{ $debitur->kelurahan_ktp_keluarga ?: '-' }}</dd>

                                <dt class="col-sm-3">Kode Pos Keluarga</dt>
                                <dd class="col-sm-9">{{ $debitur->kode_pos_ktp_keluarga ?: '-' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card mb-3">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Review Tahap 3 - Skema & Jadwal Pinjaman</h5>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-3">Nomor Pinjaman (No. Kontrak)</dt>
                                <dd class="col-sm-9">{{ $debitur->nomor_spk ?: '-' }}</dd>

                                <dt class="col-sm-3">Nominal Pinjaman</dt>
                                <dd class="col-sm-9">Rp {{ number_format(floatval($debitur->nominal_pinjaman), 0, ',', '.') }}</dd>

                                <dt class="col-sm-3">Tenor (Bulan)</dt>
                                <dd class="col-sm-9">{{ $debitur->tenor_bulan ?: '-' }} bulan</dd>

                                <dt class="col-sm-3">Suku Bunga</dt>
                                <dd class="col-sm-9">{{ $debitur->suku_bunga ?: '-' }}</dd>

                                <dt class="col-sm-3">Bunga (%) per Bulan/Tahun</dt>
                                <dd class="col-sm-9">{{ $debitur->bunga_persen ?: '-' }}%</dd>

                                <dt class="col-sm-3">Tanggal Akad Kredit</dt>
                                <dd class="col-sm-9">{{ $debitur->tanggal_akad_kredit ? \Carbon\Carbon::parse($debitur->tanggal_akad_kredit)->format('d M Y') : '-' }}</dd>

                                <dt class="col-sm-3">Tanggal Angsuran Pertama</dt>
                                <dd class="col-sm-9">{{ $debitur->tanggal_angsuran_pertama_dibayar ? \Carbon\Carbon::parse($debitur->tanggal_angsuran_pertama_dibayar)->format('d M Y') : '-' }}</dd>

                                <dt class="col-sm-3">Tanggal Jatuh Tempo</dt>
                                <dd class="col-sm-9">{{ $debitur->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($debitur->tanggal_jatuh_tempo)->format('d M Y') : '-' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                {{-- ===== DOKUMEN UPLOAD (Preview) ===== --}}
                <div class="col-lg-12">
                    <div class="card mb-3">
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
                                        'saldo_jmo'         => ['label' => 'Saldo JMO',             'icon' => 'ri-wallet-line',      'color' => 'secondary', 'multiple' => false],
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
                </div>

                {{-- ===== ACTION BUTTONS ===== --}}
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Keputusan Akhir</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label class="form-label">Status Verifikasi Data <span class="text-danger">*</span></label>
                                    <select name="status_verifikasi_data" id="statusVerifikasiSelect" class="form-select" required onchange="handleStatusChange()">
                                        <option value="">-- Pilih Status --</option>
                                        <option value="Debt Service Ratio (DSR)" {{ old('status_verifikasi_data', $debitur->status_verifikasi_data ?? '') == 'Debt Service Ratio (DSR)' ? 'selected' : '' }}>Debt Service Ratio (DSR)</option>
                                        <option value="Fintech Data Center (FDC)" {{ old('status_verifikasi_data', $debitur->status_verifikasi_data ?? '') == 'Fintech Data Center (FDC)' ? 'selected' : '' }}>Fintech Data Center (FDC)</option>
                                        <option value="Revisi Data" {{ old('status_verifikasi_data', $debitur->status_verifikasi_data ?? '') == 'Revisi Data' ? 'selected' : '' }}>Revisi Data</option>
                                        <option value="Take Out Data" {{ old('status_verifikasi_data', $debitur->status_verifikasi_data ?? '') == 'Take Out Data' ? 'selected' : '' }}>Take Out Data</option>
                                        <option value="Approve" {{ old('status_verifikasi_data', $debitur->status_verifikasi_data ?? '') == 'Approve' ? 'selected' : '' }}>Approve</option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label" id="catatanLabel">Catatan</label>
                                    <textarea name="catatan_mitra" id="catatanMitra" class="form-control" rows="3"
                                              placeholder="Tambahkan catatan jika diperlukan...">{{ old('catatan_mitra') }}</textarea>
                                </div>
                            </div>

                            {{-- DSR Section --}}
                            <div id="dsrSection" class="mt-3" style="display:none;">
                                <div class="alert alert-warning border border-warning-subtle">
                                    <h6 class="alert-heading mb-2"><i class="ri-percent-line me-1"></i>Penurunan Plafond (Debt Service Ratio)</h6>
                                    <p class="mb-3 small text-muted">Terdapat penurunan plafond berdasarkan perhitungan DSR. Masukkan nominal pinjaman yang disetujui.</p>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Pengajuan Plafond Awal</label>
                                            <input type="text" class="form-control bg-light"
                                                   value="Rp {{ number_format(floatval($debitur->pengajuan_plafond ?? $debitur->nominal_pinjaman ?? 0), 0, ',', '.') }}"
                                                   readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Nominal Pinjaman setelah DSR <span class="text-danger">*</span></label>
                                            <input type="number" name="nominal_pinjaman_dsr" id="nominalPinjamanDsr"
                                                   class="form-control @error('nominal_pinjaman_dsr') is-invalid @enderror"
                                                   placeholder="Masukkan nominal pinjaman..."
                                                   value="{{ old('nominal_pinjaman_dsr') }}"
                                                   min="1" step="1">
                                            @error('nominal_pinjaman_dsr')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <hr class="my-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="dsrLangsungApprove"
                                               name="dsr_langsung_approve" value="1"
                                               onchange="handleDsrCheckbox(this)"
                                               {{ old('dsr_langsung_approve') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="dsrLangsungApprove">
                                            Langsung Approve (tanpa konfirmasi ke marketing)
                                        </label>
                                    </div>
                                    <div id="dsrLangsungInfo" class="alert alert-success border border-success-subtle mt-2 mb-0 py-2 px-3 small" style="display:none;">
                                        <i class="ri-checkbox-circle-line me-1"></i>Pengajuan akan langsung disetujui tanpa menunggu konfirmasi penurunan plafond dari marketing.
                                    </div>
                                    <div id="dsrKonfirmasiInfo" class="alert alert-info border border-info-subtle mt-2 mb-0 py-2 px-3 small">
                                        <i class="ri-information-line me-1"></i>Marketing akan menerima notifikasi untuk mengkonfirmasi penurunan plafond kepada debitur.
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('debitur.index') }}" class="btn btn-light me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ri-save-line me-1"></i>Simpan Data
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        function handleStatusChange() {
            var status = document.getElementById('statusVerifikasiSelect').value;
            var dsrSection = document.getElementById('dsrSection');
            var nominalInput = document.getElementById('nominalPinjamanDsr');
            var catatanLabel = document.getElementById('catatanLabel');
            var catatanInput = document.getElementById('catatanMitra');

            // Reset
            dsrSection.style.display = 'none';
            if (nominalInput) nominalInput.removeAttribute('required');
            catatanLabel.textContent = 'Catatan';
            catatanInput.removeAttribute('required');
            catatanInput.placeholder = 'Tambahkan catatan jika diperlukan...';

            if (status === 'Debt Service Ratio (DSR)') {
                dsrSection.style.display = '';
                if (nominalInput) nominalInput.setAttribute('required', 'required');
                // Reset checkbox state
                var cbDsr = document.getElementById('dsrLangsungApprove');
                if (cbDsr) cbDsr.checked = false;
                var infoApprove = document.getElementById('dsrLangsungInfo');
                var infoKonfirmasi = document.getElementById('dsrKonfirmasiInfo');
                if (infoApprove) infoApprove.style.display = 'none';
                if (infoKonfirmasi) infoKonfirmasi.style.display = '';
            } else if (status === 'Revisi Data') {
                catatanLabel.textContent = 'Catatan Revisi';
                catatanInput.setAttribute('required', 'required');
                catatanInput.placeholder = 'Tuliskan alasan revisi untuk marketing...';
            } else if (status === 'Fintech Data Center (FDC)') {
                catatanLabel.textContent = 'Catatan (alasan FDC)';
            } else if (status === 'Take Out Data') {
                catatanLabel.textContent = 'Keterangan Take Out';
            }
        }

        function handleDsrCheckbox(cb) {
            var infoApprove = document.getElementById('dsrLangsungInfo');
            var infoKonfirmasi = document.getElementById('dsrKonfirmasiInfo');
            if (cb.checked) {
                if (infoApprove) infoApprove.style.display = '';
                if (infoKonfirmasi) infoKonfirmasi.style.display = 'none';
            } else {
                if (infoApprove) infoApprove.style.display = 'none';
                if (infoKonfirmasi) infoKonfirmasi.style.display = '';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            handleStatusChange();
        });
    </script>
@endsection



