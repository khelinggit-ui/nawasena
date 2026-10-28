@extends('layouts.master')
@section('title', 'Add Debitur | NSB')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Input Debitur - Tahap 1 (Marketing)</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('debitur.index') }}">Debitur</a></li>
                            <li class="breadcrumb-item active">Tahap 1</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <h5 class="alert-heading mb-2">Terjadi kesalahan input</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="create-debitur-form" action="{{ route('debitur.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="debitur_id" id="input-debitur-id" value="">
            <input type="hidden" name="previous_loan_id" id="input-previous-loan-id" value="{{ old('previous_loan_id', $draftDebitur->previous_loan_id ?? '') }}">

            <div class="card mb-3" id="debitur-stepper-card">
                <div class="card-body py-3">
                    <div class="stepper-track">
                        <div class="stepper-item" data-step-indicator="1">
                            <span class="stepper-dot">1</span>
                            <div class="stepper-label">Informasi Dasar</div>
                            <button type="button" class="btn btn-sm btn-outline-primary step-edit-btn d-none" data-edit-step="1">
                                <i class="ri-edit-2-line me-1"></i>Edit
                            </button>
                        </div>
                        <div class="stepper-line"></div>
                        <div class="stepper-item" data-step-indicator="2">
                            <span class="stepper-dot">2</span>
                            <div class="stepper-label">Domisili & Pekerjaan</div>
                            <button type="button" class="btn btn-sm btn-outline-primary step-edit-btn d-none" data-edit-step="2">
                                <i class="ri-edit-2-line me-1"></i>Edit
                            </button>
                        </div>
                        <div class="stepper-line"></div>
                        <div class="stepper-item" data-step-indicator="3">
                            <span class="stepper-dot">3</span>
                            <div class="stepper-label">Keuangan & Dokumen</div>
                            <button type="button" class="btn btn-sm btn-outline-primary step-edit-btn d-none" data-edit-step="3">
                                <i class="ri-edit-2-line me-1"></i>Edit
                            </button>
                        </div>
                    </div>
                    <div id="stepper-helper-text" class="text-muted small mt-2">Isi Step 1 lalu simpan untuk lanjut ke Step 2.</div>
                    <div id="stepper-alert" class="alert d-none mt-3 mb-0" role="alert"></div>
                    <div id="card-progress-list" class="row g-2 mt-2"></div>
                    <div class="mt-3">
                        <button type="button" id="btn-reset-draft" class="btn btn-outline-danger btn-sm d-none">
                            <i class="ri-delete-bin-line me-1"></i>Reset Draft
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="card" data-step-card="1" data-card-title="Informasi Pribadi">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Informasi Pribadi</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div>
                                        <label class="form-label">Nama Peminjam <span class="text-danger">*</span></label>
                                        <input type="text" name="nama_peminjam" class="form-control" required placeholder="Masukkan nama peminjam">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div>
                                        <label class="form-label">Nomor KTP <span class="text-danger">*</span></label>
                                        <input type="text" name="nomor_ktp" class="form-control @error('nomor_ktp') is-invalid @enderror" value="{{ old('nomor_ktp') }}" required placeholder="16 Digit NIK" maxlength="16">
                                        @error('nomor_ktp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div>
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="Email aktif">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div>
                                        <label class="form-label">Nomor Handphone <span class="text-danger">*</span></label>
                                        <input type="text" name="nomor_handphone" class="form-control @error('nomor_handphone') is-invalid @enderror" value="{{ old('nomor_handphone') }}" required placeholder="Contoh: 08123456789">
                                        @error('nomor_handphone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div>
                                        <label class="form-label">Jenis Kelamin</label>
                                        <select class="form-select" name="jenis_kelamin">
                                            <option value="">Pilih Jenis Kelamin</option>
                                            <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div>
                                        <label class="form-label">Tempat Lahir</label>
                                        <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" placeholder="Kota lahir">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div>
                                        <label class="form-label">Tanggal Lahir</label>
                                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div>
                                        <label class="form-label">Nama Ibu Kandung <span class="text-danger">*</span></label>
                                        <input type="text" name="nama_ibu_kandung" class="form-control @error('nama_ibu_kandung') is-invalid @enderror" value="{{ old('nama_ibu_kandung') }}" required placeholder="Nama ibu">
                                        @error('nama_ibu_kandung')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Agama</label>
                                    <select class="form-select" name="agama">
                                        <option value="">Pilih Agama</option>
                                        <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                        <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                        <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                        <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                        <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                        <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Pendidikan</label>
                                    <select class="form-select" name="pendidikan">
                                        <option value="">Pilih Pendidikan</option>
                                        <option value="SD" {{ old('pendidikan') == 'SD' ? 'selected' : '' }}>SD</option>
                                        <option value="SMP" {{ old('pendidikan') == 'SMP' ? 'selected' : '' }}>SMP</option>
                                        <option value="SMA" {{ old('pendidikan') == 'SMA' ? 'selected' : '' }}>SMA</option>
                                        <option value="D3" {{ old('pendidikan') == 'D3' ? 'selected' : '' }}>D3</option>
                                        <option value="S1" {{ old('pendidikan') == 'S1' ? 'selected' : '' }}>S1</option>
                                        <option value="S2" {{ old('pendidikan') == 'S2' ? 'selected' : '' }}>S2</option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Status Perkawinan <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status_perkawinan') is-invalid @enderror" name="status_perkawinan" id="select-status-perkawinan" required>
                                        <option value="">Pilih Status</option>
                                        <option value="Belum Menikah" {{ old('status_perkawinan') == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                                        <option value="Menikah" {{ old('status_perkawinan') == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                        <option value="Cerai" {{ old('status_perkawinan') == 'Cerai' ? 'selected' : '' }}>Cerai</option>
                                    </select>
                                    @error('status_perkawinan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div id="spouse-section" class="w-100 d-none">
                                    <div class="row g-3">
                                        <div class="col-lg-4">
                                            <label class="form-label">Nama Pasangan <span class="text-danger">*</span></label>
                                            <input type="text" name="nama_pasangan" id="input-nama-pasangan" class="form-control @error('nama_pasangan') is-invalid @enderror" value="{{ old('nama_pasangan') }}" placeholder="Nama pasangan">
                                            @error('nama_pasangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="form-label">NIK Pasangan <span class="text-danger">*</span></label>
                                            <input type="text" name="nik_pasangan" id="input-nik-pasangan" class="form-control @error('nik_pasangan') is-invalid @enderror" value="{{ old('nik_pasangan') }}" placeholder="NIK pasangan">
                                            @error('nik_pasangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="form-label">No Handphone Pasangan <span class="text-danger">*</span></label>
                                            <input type="text" name="nomor_hp_pasangan" id="input-hp-pasangan" class="form-control @error('nomor_hp_pasangan') is-invalid @enderror" value="{{ old('nomor_hp_pasangan') }}" placeholder="No HP pasangan">
                                            @error('nomor_hp_pasangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Privy ID</label>
                                    <input type="text" name="privy_id" class="form-control" value="{{ old('privy_id') }}" placeholder="Privy ID">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Nomor NPWP</label>
                                    <input type="text" name="nomor_npwp" class="form-control @error('nomor_npwp') is-invalid @enderror" value="{{ old('nomor_npwp') }}" placeholder="Nomor NPWP">
                                    @error('nomor_npwp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Pengajuan Plafond <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="pengajuan_plafond" class="form-control @error('pengajuan_plafond') is-invalid @enderror" required value="{{ old('pengajuan_plafond') }}">
                                        @error('pengajuan_plafond')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Tenor (Bulan) <span class="text-danger">*</span></label>
                                    <select class="form-select @error('tenor_bulan') is-invalid @enderror" name="tenor_bulan" required>
                                        <option value="">Pilih Tenor</option>
                                        <option value="6" {{ old('tenor_bulan') == '6' ? 'selected' : '' }}>6 Bulan</option>
                                        <option value="10" {{ old('tenor_bulan') == '10' ? 'selected' : '' }}>10 Bulan</option>
                                        <option value="12" {{ old('tenor_bulan') == '12' ? 'selected' : '' }}>12 Bulan</option>
                                        <option value="24" {{ old('tenor_bulan') == '24' ? 'selected' : '' }}>24 Bulan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end col -->

                <div class="col-lg-12">
                    <div class="card" data-step-card="1" data-card-title="Informasi Alamat (Sesuai KTP)">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Informasi Alamat (Sesuai KTP)</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <div>
                                        <label class="form-label">Alamat KTP Lengkap <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="alamat_ktp" rows="3" required placeholder="Nama Jalan, Gedung, No. Rumah"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <label class="form-label">RT</label>
                                    <input type="text" name="rt_ktp" class="form-control" placeholder="000">
                                </div>
                                <div class="col-lg-3 col-6">
                                    <label class="form-label">RW</label>
                                    <input type="text" name="rw_ktp" class="form-control" placeholder="000">
                                </div>

                                {{-- PROVINSI --}}
                                <div class="col-lg-6">
                                    <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                                    <select id="select-provinsi" name="kode_provinsi" class="form-select" required>
                                        <option value="">-- Pilih Provinsi --</option>
                                        @foreach(\App\Models\Province::orderBy('name')->get() as $prov)
                                            <option value="{{ $prov->id }}" data-name="{{ $prov->name }}">{{ ucwords(strtolower($prov->name)) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="provinsi_ktp" id="input-provinsi-name">
                                </div>

                                {{-- KOTA/KABUPATEN --}}
                                <div class="col-lg-6">
                                    <label class="form-label">Kota / Kabupaten <span class="text-danger">*</span></label>
                                    <select id="select-kabupaten" name="kode_kabupaten" class="form-select" required disabled>
                                        <option value="">-- Pilih Kota/Kabupaten --</option>
                                    </select>
                                    <input type="hidden" name="kota_kabupaten_ktp" id="input-kabupaten-name">
                                </div>

                                {{-- KECAMATAN --}}
                                <div class="col-lg-6">
                                    <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                    <select id="select-kecamatan" name="kode_kecamatan" class="form-select" required disabled>
                                        <option value="">-- Pilih Kecamatan --</option>
                                    </select>
                                    <input type="hidden" name="kecamatan_ktp" id="input-kecamatan-name">
                                </div>

                                {{-- KELURAHAN/DESA --}}
                                <div class="col-lg-6">
                                    <label class="form-label">Kelurahan / Desa <span class="text-danger">*</span></label>
                                    <select id="select-kelurahan" name="kode_kelurahan" class="form-select" required disabled>
                                        <option value="">-- Pilih Kelurahan/Desa --</option>
                                    </select>
                                    <input type="hidden" name="kelurahan_ktp" id="input-kelurahan-name">
                                </div>

                                <div class="col-lg-4">
                                    <label class="form-label">Kode Pos</label>
                                    <input type="text" name="kode_pos_ktp" class="form-control" placeholder="00000">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card" data-step-card="2" data-card-title="Data Pekerjaan">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Data Pekerjaan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Pekerjaan</label>
                                    <select class="form-select" name="pekerjaan">
                                        <option value="">Pilih</option>
                                        <option value="Karyawan swasta" {{ old('pekerjaan') == 'Karyawan swasta' ? 'selected' : '' }}>Karyawan swasta</option>
                                        <option value="Wiraswasta" {{ old('pekerjaan') == 'Wiraswasta' ? 'selected' : '' }}>Wiraswasta</option>
                                        <option value="PNS" {{ old('pekerjaan') == 'PNS' ? 'selected' : '' }}>PNS</option>
                                        <option value="Lainnya" {{ old('pekerjaan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Jenis Pekerjaan</label>
                                    <select class="form-select" name="jenis_pekerjaan">
                                        <option value="">Pilih</option>
                                        <option value="Offline" {{ old('jenis_pekerjaan') == 'Offline' ? 'selected' : '' }}>Offline</option>
                                        <option value="Online" {{ old('jenis_pekerjaan') == 'Online' ? 'selected' : '' }}>Online</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Bidang Pekerjaan</label>
                                    <select class="form-select" name="bidang_pekerjaan">
                                        <option value="">Pilih</option>
                                        <option value="Industri Pengolahan" {{ old('bidang_pekerjaan') == 'Industri Pengolahan' ? 'selected' : '' }}>Industri Pengolahan</option>
                                        <option value="Manufaktur" {{ old('bidang_pekerjaan') == 'Manufaktur' ? 'selected' : '' }}>Manufaktur</option>
                                        <option value="Lainnya" {{ old('bidang_pekerjaan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Pengalaman Kerja (Tahun)</label>
                                    <input type="number" name="pengalaman_kerjatahun" class="form-control" placeholder="0">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                                    <select id="select-perusahaan" name="nama_perusahaan" class="form-select @error('nama_perusahaan') is-invalid @enderror" required>
                                        <option value="">-- Pilih Perusahaan --</option>
                                        @foreach($perusahaans as $p)
                                            <option value="{{ $p->nama_perusahaan }}"
                                                data-id="{{ $p->id }}"
                                                data-alamat="{{ $p->alamat_perusahaan }}"
                                                {{ old('nama_perusahaan') == $p->nama_perusahaan ? 'selected' : '' }}>
                                                {{ $p->nama_perusahaan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="perusahaan_id" id="input-perusahaan-id" value="{{ old('perusahaan_id') }}">
                                    @error('nama_perusahaan')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Jabatan/Posisi</label>
                                    <input type="text" name="jabatan_posisi" class="form-control" value="{{ old('jabatan_posisi') }}">
                                </div>
                                <div class="col-lg-12">
                                    <label class="form-label">Alamat Perusahaan</label>
                                    <textarea id="textarea-alamat-perusahaan" name="alamat_perusahaan" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card" data-step-card="1" data-card-title="Data Rekening">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Data Rekening</h5>
                        </div>
                        <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-lg-4">
                                        <label class="form-label">Nama Bank <span class="text-danger">*</span></label>
                                        <input type="text" name="nama_bank" class="form-control @error('nama_bank') is-invalid @enderror" value="{{ old('nama_bank') }}" required placeholder="BCA/Mandiri/dsb">
                                        @error('nama_bank')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-lg-4">
                                        <label class="form-label">Nama Pemilik Rekening</label>
                                        <input type="text" name="nama_pemilik_rekening" class="form-control" value="{{ old('nama_pemilik_rekening') }}">
                                    </div>
                                    <div class="col-lg-4">
                                        <label class="form-label">Nomor Rekening</label>
                                        <input type="text" name="nomer_rekening" class="form-control @error('nomer_rekening') is-invalid @enderror" value="{{ old('nomer_rekening') }}">
                                        @error('nomer_rekening')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    {{-- ===== CARD INFORMASI PEMINJAMAN & DOMISILI ===== --}}
                    <div class="card mt-3" data-step-card="2" data-card-title="Informasi Peminjaman & Domisili">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Informasi Peminjaman & Domisili</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <label class="form-label">Alamat Domisili <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="alamat_domisili" rows="2" required placeholder="Jl. Raya Domisili..">{{ old('alamat_domisili') }}</textarea>
                                </div>
                                <div class="col-lg-2 col-6">
                                    <label class="form-label">RT</label>
                                    <input type="text" name="rt_domisili" class="form-control" value="{{ old('rt_domisili') }}">
                                </div>
                                <div class="col-lg-2 col-6">
                                    <label class="form-label">RW</label>
                                    <input type="text" name="rw_domisili" class="form-control" value="{{ old('rw_domisili') }}">
                                </div>

                                {{-- PROVINSI DOMISILI --}}
                                <div class="col-lg-4">
                                    <label class="form-label">Provinsi Domisili <span class="text-danger">*</span></label>
                                    <select id="select-provinsi-domisili" name="kode_provinsi_domisili" class="form-select" required>
                                        <option value="">-- Pilih Provinsi --</option>
                                        @foreach(\App\Models\Province::orderBy('name')->get() as $prov)
                                            <option value="{{ $prov->id }}" data-name="{{ $prov->name }}">{{ ucwords(strtolower($prov->name)) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="provinsi_domisili" id="input-provinsi-domisili-name">
                                </div>

                                {{-- KOTA/KABUPATEN DOMISILI --}}
                                <div class="col-lg-4">
                                    <label class="form-label">Kota / Kabupaten Domisili <span class="text-danger">*</span></label>
                                    <select id="select-kabupaten-domisili" name="kode_kabupaten_domisili" class="form-select" required disabled>
                                        <option value="">-- Pilih Kota/Kabupaten --</option>
                                    </select>
                                    <input type="hidden" name="kota_kabupaten_domisili" id="input-kabupaten-domisili-name">
                                </div>

                                {{-- KECAMATAN DOMISILI --}}
                                <div class="col-lg-4">
                                    <label class="form-label">Kecamatan Domisili <span class="text-danger">*</span></label>
                                    <select id="select-kecamatan-domisili" name="kode_kecamatan_domisili" class="form-select" required disabled>
                                        <option value="">-- Pilih Kecamatan --</option>
                                    </select>
                                    <input type="hidden" name="kecamatan_domisili" id="input-kecamatan-domisili-name">
                                </div>

                                {{-- KELURAHAN/DESA DOMISILI --}}
                                <div class="col-lg-4">
                                    <label class="form-label">Kelurahan / Desa Domisili <span class="text-danger">*</span></label>
                                    <select id="select-kelurahan-domisili" name="kode_kelurahan_domisili" class="form-select" required disabled>
                                        <option value="">-- Pilih Kelurahan/Desa --</option>
                                    </select>
                                    <input type="hidden" name="kelurahan_domisili" id="input-kelurahan-domisili-name">
                                </div>

                                <div class="col-lg-4">
                                    <label class="form-label">Kode Pos Domisili</label>
                                    <input type="text" name="kode_pos_domisili" class="form-control" value="{{ old('kode_pos_domisili') }}">
                                </div>
                                <div class="col-lg-12">
                                    <label class="form-label">Status Kepemilikan Rumah</label>
                                    <select class="form-select" name="status_kepemilikan_rumah">
                                        <option value="">Pilih</option>
                                        <option value="Milik Sendiri" {{ old('status_kepemilikan_rumah') == 'Milik Sendiri' ? 'selected' : '' }}>Milik Sendiri</option>
                                        <option value="Sewa/Kontrak" {{ old('status_kepemilikan_rumah') == 'Sewa/Kontrak' ? 'selected' : '' }}>Sewa/Kontrak</option>
                                        <option value="Tinggal dengan Orang Tua" {{ old('status_kepemilikan_rumah') == 'Tinggal dengan Orang Tua' ? 'selected' : '' }}>Tinggal dengan Orang Tua</option>
                                        <option value="Lainnya" {{ old('status_kepemilikan_rumah') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== CARD KONTAK DARURAT / KELUARGA ===== --}}
                    <div class="card mt-3" data-step-card="2" data-card-title="Kontak Darurat / Keluarga (Tidak Serumah)">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Kontak Darurat / Keluarga (Tidak Serumah)</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Nama Keluarga <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_keluarga_tidak_serumah" class="form-control" required placeholder="Nama lengkap keluarga" value="{{ old('nama_keluarga_tidak_serumah') }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Hubungan</label>
                                    <input type="text" name="hubungan_keluarga" class="form-control" placeholder="Kakak/Adik/Paman dsb" value="{{ old('hubungan_keluarga') }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">No. Handphone Keluarga <span class="text-danger">*</span></label>
                                    <input type="text" name="no_handphone_keluarga" class="form-control" required value="{{ old('no_handphone_keluarga') }}">
                                </div>
                                <div class="col-lg-12">
                                    <label class="form-label">Alamat KTP Keluarga</label>
                                    <textarea class="form-control" name="alamat_ktp_keluarga" rows="2">{{ old('alamat_ktp_keluarga') }}</textarea>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Provinsi KTP Keluarga</label>
                                    <select id="select-provinsi-ktp-keluarga" name="kode_provinsi_ktp_keluarga" class="form-select">
                                        <option value="">-- Pilih Provinsi --</option>
                                        @foreach(\App\Models\Province::orderBy('name')->get() as $prov)
                                            <option value="{{ $prov->id }}" data-name="{{ $prov->name }}">{{ ucwords(strtolower($prov->name)) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="provinsi_ktp_keluarga" id="input-provinsi-ktp-keluarga-name">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Kota/Kabupaten KTP Keluarga</label>
                                    <select id="select-kabupaten-ktp-keluarga" name="kode_kabupaten_ktp_keluarga" class="form-select" disabled>
                                        <option value="">-- Pilih Kota/Kabupaten --</option>
                                    </select>
                                    <input type="hidden" name="kota_kabupaten_ktp_keluarga" id="input-kabupaten-ktp-keluarga-name">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Kecamatan KTP Keluarga</label>
                                    <select id="select-kecamatan-ktp-keluarga" name="kode_kecamatan_ktp_keluarga" class="form-select" disabled>
                                        <option value="">-- Pilih Kecamatan --</option>
                                    </select>
                                    <input type="hidden" name="kecamatan_ktp_keluarga" id="input-kecamatan-ktp-keluarga-name">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Kelurahan / Desa KTP Keluarga</label>
                                    <select id="select-kelurahan-ktp-keluarga" name="kode_kelurahan_ktp_keluarga" class="form-select" disabled>
                                        <option value="">-- Pilih Kelurahan/Desa --</option>
                                    </select>
                                    <input type="hidden" name="kelurahan_ktp_keluarga" id="input-kelurahan-ktp-keluarga-name">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Kode Pos KTP Keluarga</label>
                                    <input type="text" name="kode_pos_ktp_keluarga" class="form-control" value="{{ old('kode_pos_ktp_keluarga') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== CARD KEUANGAN & PEKERJAAN TAMBAHAN ===== --}}
                    <div class="card mt-3" data-step-card="3" data-card-title="Keuangan & Pekerjaan Tambahan">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Keuangan & Pekerjaan Tambahan</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info border-0 rounded-top mb-4">
                                Bagian ini diperlukan untuk verifikasi data profil risiko secara kasar.
                            </div>
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Gaji / Penghasilan Per Bulan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="pendapatan_per_bulan" class="form-control" value="{{ old('pendapatan_per_bulan') }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Pengeluaran Per Bulan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="pengeluaran_per_bulan" class="form-control" value="{{ old('pengeluaran_per_bulan') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Hutang Per Bulan (Cicilan Lain)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="hutang_per_bulan" class="form-control" value="{{ old('hutang_per_bulan') }}">
                                    </div>
                                </div>

                                <div class="col-lg-12 mt-4">
                                    <h6 class="fw-semibold font-size-12 text-uppercase mb-3">Pekerjaan Lainnya (Opsional)</h6>
                                </div>

                                <div class="col-lg-4">
                                    <label class="form-label">Pekerjaan Lain</label>
                                    <input type="text" name="pekerjaan_lain" class="form-control" placeholder="Contoh: Freelance Online" value="{{ old('pekerjaan_lain') }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Jenis Pekerjaan Lain</label>
                                    <input type="text" name="jenis_pekerjaan_lain" class="form-control" value="{{ old('jenis_pekerjaan_lain') }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Bidang Pekerjaan Lain</label>
                                    <input type="text" name="bidang_pekerjaan_lain" class="form-control" value="{{ old('bidang_pekerjaan_lain') }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Pengalaman Kerja Lain (Tahun)</label>
                                    <input type="number" name="pengalaman_kerja_lain_tahun" class="form-control" value="{{ old('pengalaman_kerja_lain_tahun') }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Nama Perusahaan Lain (Jika ada)</label>
                                    <input type="text" name="nama_perusahaan_lain" class="form-control" value="{{ old('nama_perusahaan_lain') }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Jabatan/Posisi Lain</label>
                                    <input type="text" name="jabatan_posisi_lain" class="form-control" value="{{ old('jabatan_posisi_lain') }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Pendapatan Lain</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="pendapatan_lain_rupiah" class="form-control" value="{{ old('pendapatan_lain_rupiah') }}">
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <label class="form-label">Alamat Perusahaan Lain</label>
                                    <textarea name="alamat_perusahaan_lain" class="form-control" rows="1">{{ old('alamat_perusahaan_lain') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ===== CARD DOKUMEN UPLOAD ===== --}}
                    <div class="card mt-3" data-step-card="3" data-card-title="Upload Dokumen">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">
                                <i class="ri-file-upload-line me-2 text-primary"></i>Upload Dokumen
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">

                                {{-- Foto KTP Debitur --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold">Foto KTP Debitur <span class="text-danger">*</span></label>
                                    <div class="upload-area" id="area-foto-ktp" onclick="document.getElementById('file-foto-ktp').click()">
                                        <div class="upload-placeholder" id="ph-foto-ktp">
                                            <i class="ri-id-card-line fs-1 text-primary opacity-75"></i>
                                            <p class="mb-0 mt-2 text-muted small">Klik untuk upload KTP Debitur</p>
                                            <span class="badge bg-primary-subtle text-primary mt-1">JPG / PNG / PDF</span>
                                        </div>
                                        <img id="prev-foto-ktp" src="" class="upload-preview d-none" alt="Preview KTP">
                                    </div>
                                    <input type="file" id="file-foto-ktp" name="foto_ktp" class="d-none" accept="image/*,application/pdf" onchange="handleFilePreview(this,'prev-foto-ktp','ph-foto-ktp','name-foto-ktp')">
                                    <small id="name-foto-ktp" class="text-muted d-none mt-1 d-block"></small>
                                </div>

                                {{-- KTP Pasangan --}}
                                <div id="ktp-pasangan-wrapper" class="col-lg-3 col-md-6 d-none">
                                    <label class="form-label fw-semibold">KTP Pasangan Debitur <span class="text-danger">*</span></label>
                                    <div class="upload-area" id="area-ktp-pasangan" onclick="document.getElementById('file-ktp-pasangan').click()">
                                        <div class="upload-placeholder" id="ph-ktp-pasangan">
                                            <i class="ri-contacts-line fs-1 text-success opacity-75"></i>
                                            <p class="mb-0 mt-2 text-muted small">Klik untuk upload KTP Pasangan</p>
                                            <span class="badge bg-success-subtle text-success mt-1">JPG / PNG / PDF</span>
                                        </div>
                                        <img id="prev-ktp-pasangan" src="" class="upload-preview d-none" alt="Preview KTP Pasangan">
                                    </div>
                                    <input type="file" id="file-ktp-pasangan" name="ktp_pasangan" class="d-none" accept="image/*,application/pdf" onchange="handleFilePreview(this,'prev-ktp-pasangan','ph-ktp-pasangan','name-ktp-pasangan')">
                                    <small id="name-ktp-pasangan" class="text-muted d-none mt-1 d-block"></small>
                                    @error('ktp_pasangan')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Foto Kartu BPJS --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold">Foto Kartu BPJS <span class="text-danger">*</span></label>
                                    <div class="upload-area" id="area-foto-bpjs" onclick="document.getElementById('file-foto-bpjs').click()">
                                        <div class="upload-placeholder" id="ph-foto-bpjs">
                                            <i class="ri-health-book-line fs-1 text-warning opacity-75"></i>
                                            <p class="mb-0 mt-2 text-muted small">Klik untuk upload Kartu BPJS</p>
                                            <span class="badge bg-warning-subtle text-warning mt-1">JPG / PNG / PDF</span>
                                        </div>
                                        <img id="prev-foto-bpjs" src="" class="upload-preview d-none" alt="Preview BPJS">
                                    </div>
                                    <input type="file" id="file-foto-bpjs" name="foto_bpjs" class="d-none" accept="image/*,application/pdf" onchange="handleFilePreview(this,'prev-foto-bpjs','ph-foto-bpjs','name-foto-bpjs')">
                                    <small id="name-foto-bpjs" class="text-muted d-none mt-1 d-block"></small>
                                    @error('foto_bpjs')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Selfie dengan KTP --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold">Selfie dengan KTP <span class="text-danger">*</span></label>
                                    <div class="upload-area" id="area-selfie-ktp" onclick="document.getElementById('file-selfie-ktp').click()">
                                        <div class="upload-placeholder" id="ph-selfie-ktp">
                                            <i class="ri-camera-line fs-1 text-danger opacity-75"></i>
                                            <p class="mb-0 mt-2 text-muted small">Klik untuk upload Selfie + KTP</p>
                                            <span class="badge bg-danger-subtle text-danger mt-1">JPG / PNG</span>
                                        </div>
                                        <img id="prev-selfie-ktp" src="" class="upload-preview d-none" alt="Preview Selfie">
                                    </div>
                                    <input type="file" id="file-selfie-ktp" name="selfie_ktp" class="d-none" accept="image/*" onchange="handleFilePreview(this,'prev-selfie-ktp','ph-selfie-ktp','name-selfie-ktp')">
                                    <small id="name-selfie-ktp" class="text-muted d-none mt-1 d-block"></small>
                                    @error('selfie_ktp')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Kartu Keluarga --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold">Kartu Keluarga (KK)</label>
                                    <div class="upload-area" id="area-kartu-keluarga" onclick="document.getElementById('file-kartu-keluarga').click()">
                                        <div class="upload-placeholder" id="ph-kartu-keluarga">
                                            <i class="ri-file-list-line fs-1 text-info opacity-75"></i>
                                            <p class="mb-0 mt-2 text-muted small">Klik untuk upload Kartu Keluarga</p>
                                            <span class="badge bg-info-subtle text-info mt-1">JPG / PNG / PDF</span>
                                        </div>
                                        <img id="prev-kartu-keluarga" src="" class="upload-preview d-none" alt="Preview KK">
                                    </div>
                                    <input type="file" id="file-kartu-keluarga" name="kartu_keluarga" class="d-none" accept="image/*,application/pdf" onchange="handleFilePreview(this,'prev-kartu-keluarga','ph-kartu-keluarga','name-kartu-keluarga')">
                                    <small id="name-kartu-keluarga" class="text-muted d-none mt-1 d-block"></small>
                                    @error('kartu_keluarga')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Privy ID --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold">Privy ID</label>
                                    <div class="upload-area" id="area-privy-id" onclick="document.getElementById('file-privy-id').click()">
                                        <div class="upload-placeholder" id="ph-privy-id">
                                            <i class="ri-shield-user-line fs-1 text-primary opacity-75"></i>
                                            <p class="mb-0 mt-2 text-muted small">Klik untuk upload Privy ID</p>
                                            <span class="badge bg-primary-subtle text-primary mt-1">JPG / PNG / PDF</span>
                                        </div>
                                        <img id="prev-privy-id" src="" class="upload-preview d-none" alt="Preview Privy ID">
                                    </div>
                                    <input type="file" id="file-privy-id" name="privy_id_file" class="d-none" accept="image/*,application/pdf" onchange="handleFilePreview(this,'prev-privy-id','ph-privy-id','name-privy-id')">
                                    <small id="name-privy-id" class="text-muted d-none mt-1 d-block"></small>
                                    @error('privy_id_file')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ID Card --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold">ID Card</label>
                                    <div class="upload-area" id="area-id-card" onclick="document.getElementById('file-id-card').click()">
                                        <div class="upload-placeholder" id="ph-id-card">
                                            <i class="ri-id-card-line fs-1 text-secondary opacity-75"></i>
                                            <p class="mb-0 mt-2 text-muted small">Klik untuk upload ID Card</p>
                                            <span class="badge bg-secondary-subtle text-secondary mt-1">JPG / PNG / PDF</span>
                                        </div>
                                        <img id="prev-id-card" src="" class="upload-preview d-none" alt="Preview ID Card">
                                    </div>
                                    <input type="file" id="file-id-card" name="id_card" class="d-none" accept="image/*,application/pdf" onchange="handleFilePreview(this,'prev-id-card','ph-id-card','name-id-card')">
                                    <small id="name-id-card" class="text-muted d-none mt-1 d-block"></small>
                                    @error('id_card')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Slip Gaji --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold">Slip Gaji</label>
                                    <div class="upload-area" id="area-slip-gaji" onclick="document.getElementById('file-slip-gaji').click()">
                                        <div class="upload-placeholder" id="ph-slip-gaji">
                                            <i class="ri-money-dollar-circle-line fs-1 text-success opacity-75"></i>
                                            <p class="mb-0 mt-2 text-muted small">Klik untuk upload Slip Gaji</p>
                                            <span class="badge bg-success-subtle text-success mt-1">JPG / PNG / PDF</span>
                                        </div>
                                        <img id="prev-slip-gaji" src="" class="upload-preview d-none" alt="Preview Slip Gaji">
                                    </div>
                                    <input type="file" id="file-slip-gaji" name="slip_gaji" class="d-none" accept="image/*,application/pdf" onchange="handleFilePreview(this,'prev-slip-gaji','ph-slip-gaji','name-slip-gaji')">
                                    <small id="name-slip-gaji" class="text-muted d-none mt-1 d-block"></small>
                                    @error('slip_gaji')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Buku Nikah --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold">Buku Nikah</label>
                                    <div class="upload-area" id="area-buku-nikah" onclick="document.getElementById('file-buku-nikah').click()">
                                        <div class="upload-placeholder" id="ph-buku-nikah">
                                            <i class="ri-heart-line fs-1 text-danger opacity-75"></i>
                                            <p class="mb-0 mt-2 text-muted small">Klik untuk upload Buku Nikah</p>
                                            <span class="badge bg-danger-subtle text-danger mt-1">JPG / PNG / PDF</span>
                                        </div>
                                        <img id="prev-buku-nikah" src="" class="upload-preview d-none" alt="Preview Buku Nikah">
                                    </div>
                                    <input type="file" id="file-buku-nikah" name="buku_nikah" class="d-none" accept="image/*,application/pdf" onchange="handleFilePreview(this,'prev-buku-nikah','ph-buku-nikah','name-buku-nikah')">
                                    <small id="name-buku-nikah" class="text-muted d-none mt-1 d-block"></small>
                                    @error('buku_nikah')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                 {{-- Mutasi Bank --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold">Mutasi Bank</label>
                                    <div class="upload-area" id="area-mutasi-bank" onclick="document.getElementById('file-mutasi-bank').click()">
                                        <div class="upload-placeholder" id="ph-mutasi-bank">
                                            <i class="ri-bank-line fs-1 text-info opacity-75"></i>
                                            <p class="mb-0 mt-2 text-muted small">Klik untuk upload Mutasi Bank</p>
                                            <span class="badge bg-info-subtle text-info mt-1">JPG / PNG / PDF (Multiple)</span>
                                        </div>
                                        <img id="prev-mutasi-bank" src="" class="upload-preview d-none" alt="Preview Mutasi Bank">
                                    </div>
                                    <input type="file" id="file-mutasi-bank" name="mutasi_bank[]" class="d-none" accept="image/*,application/pdf" multiple onchange="handleFilePreview(this,'prev-mutasi-bank','ph-mutasi-bank','name-mutasi-bank','mutasi-bank-preview-list')">
                                    <small id="name-mutasi-bank" class="text-muted d-none mt-1 d-block"></small>
                                    <div id="mutasi-bank-preview-list" class="multi-file-preview mt-2 d-none"></div>
                                    @error('mutasi_bank')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Dokumen Pendukung --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold">Dokumen Pendukung</label>
                                    <div class="upload-area" id="area-dokumen-pendukung" onclick="document.getElementById('file-dokumen-pendukung').click()">
                                        <div class="upload-placeholder" id="ph-dokumen-pendukung">
                                            <i class="ri-file-text-line fs-1 text-warning opacity-75"></i>
                                            <p class="mb-0 mt-2 text-muted small">Klik untuk upload Dokumen Pendukung</p>
                                            <span class="badge bg-warning-subtle text-warning mt-1">JPG / PNG / PDF (Multiple)</span>
                                        </div>
                                        <img id="prev-dokumen-pendukung" src="" class="upload-preview d-none" alt="Preview Dokumen Pendukung">
                                    </div>
                                    <input type="file" id="file-dokumen-pendukung" name="dokumen_pendukung[]" class="d-none" accept="image/*,application/pdf" multiple onchange="handleFilePreview(this,'prev-dokumen-pendukung','ph-dokumen-pendukung','name-dokumen-pendukung','dokumen-pendukung-preview-list')">
                                    <small id="name-dokumen-pendukung" class="text-muted d-none mt-1 d-block"></small>
                                    <div id="dokumen-pendukung-preview-list" class="multi-file-preview mt-2 d-none"></div>
                                    @error('dokumen_pendukung')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @error('dokumen_pendukung.*')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Saldo JMO --}}
                                <div class="col-lg-3 col-md-6">
                                    <label class="form-label fw-semibold">Saldo JMO</label>
                                    <div class="upload-area" id="area-saldo-jmo" onclick="document.getElementById('file-saldo-jmo').click()">
                                        <div class="upload-placeholder" id="ph-saldo-jmo">
                                            <i class="ri-wallet-line fs-1 text-secondary opacity-75"></i>
                                            <p class="mb-0 mt-2 text-muted small">Klik untuk upload Saldo JMO</p>
                                            <span class="badge bg-secondary-subtle text-secondary mt-1">JPG / PNG / PDF</span>
                                        </div>
                                        <img id="prev-saldo-jmo" src="" class="upload-preview d-none" alt="Preview Saldo JMO">
                                    </div>
                                    <input type="file" id="file-saldo-jmo" name="saldo_jmo" class="d-none" accept="image/*,application/pdf" onchange="handleFilePreview(this,'prev-saldo-jmo','ph-saldo-jmo','name-saldo-jmo')">
                                    <small id="name-saldo-jmo" class="text-muted d-none mt-1 d-block"></small>
                                    @error('saldo_jmo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                        <a href="{{ route('debitur.index') }}" class="btn btn-light">Batal</a>
                        <div class="d-flex gap-2">
                            <button type="button" id="btn-prev-step" class="btn btn-outline-secondary d-none">
                                <i class="ri-arrow-left-line me-1"></i>Kembali
                            </button>
                            <button type="button" id="btn-next-step" class="btn btn-outline-primary d-none">
                                Next<i class="ri-arrow-right-line ms-1"></i>
                            </button>
                            <button type="button" id="btn-save-step" class="btn btn-success">
                                <i class="ri-save-line me-1"></i>Simpan Step 1 & Lanjut
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

@section('styles')
<style>
.stepper-track {
    display: flex;
    align-items: center;
    gap: 10px;
}

.stepper-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 120px;
    opacity: .5;
    transition: opacity .2s ease;
}

.stepper-item.active,
.stepper-item.completed {
    opacity: 1;
}

.stepper-dot {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    border: 2px solid #cbd5e1;
    background: #fff;
    color: #64748b;
}

.stepper-item.active .stepper-dot {
    border-color: #0ea5e9;
    color: #0f172a;
    background: #e0f2fe;
}

.stepper-item.completed .stepper-dot {
    border-color: #198754;
    background: #198754;
    color: #fff;
}

.stepper-label {
    margin-top: 6px;
    font-size: 12px;
    font-weight: 600;
    text-align: center;
    color: #334155;
}

.step-edit-btn {
    margin-top: 6px;
    font-size: 12px;
    font-weight: 600;
    border-width: 1px;
    padding: 3px 10px;
    border-radius: 999px;
    background: #ffffff;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
}

.step-edit-btn:hover,
.step-edit-btn:focus {
    background: #e0f2fe;
    border-color: #0ea5e9;
    color: #0369a1;
}

.step-edit-btn.text-success {
    background: #ecfdf3;
    border-color: #22c55e;
    color: #15803d !important;
}

.stepper-line {
    flex: 1;
    height: 2px;
    background: #dbe5f0;
}

.card-progress-item {
    border: 1px solid #dbe5f0;
    border-radius: 8px;
    padding: 6px 10px;
    background: #f8fafc;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card-progress-item .status {
    font-weight: 700;
}

.card-progress-item.done {
    border-color: #198754;
    background: #eefbf3;
}

.card-progress-item.done .status {
    color: #198754;
}

[data-step-card].is-hidden-step {
    display: none;
}

[data-step-card].is-locked-step {
    opacity: .85;
}

[data-step-card].is-locked-step .card-body {
    pointer-events: none;
}

.upload-area {
    border: 2px dashed #ced4da;
    border-radius: 12px;
    padding: 20px 12px;
    text-align: center;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    min-height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    background: #f8f9fa;
    overflow: hidden;
}
.upload-area:hover {
    border-color: #0d6efd;
    background: #e9f2ff;
}
.upload-area.has-file {
    border-color: #198754;
    background: #f0fdf4;
}
.upload-preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
    top: 0; left: 0;
    border-radius: 10px;
}
.upload-placeholder { pointer-events: none; }

.multi-file-preview {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
}

.multi-file-item {
    border: 1px solid #dbe3ec;
    border-radius: 8px;
    background: #ffffff;
    padding: 6px;
}

.multi-file-thumb {
    width: 100%;
    height: 72px;
    border-radius: 6px;
    object-fit: cover;
    background: #f1f5f9;
}

.multi-file-pdf {
    height: 72px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8fafc;
    color: #64748b;
    font-size: 11px;
    font-weight: 600;
    border: 1px dashed #cbd5e1;
}

.multi-file-name {
    margin-top: 4px;
    font-size: 11px;
    line-height: 1.3;
    color: #475569;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.multi-file-remove {
    margin-top: 6px;
    width: 100%;
    border: 1px solid #fecaca;
    border-radius: 6px;
    background: #fff1f2;
    color: #b91c1c;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 6px;
    cursor: pointer;
}

.multi-file-remove:hover {
    background: #ffe4e6;
}
</style>
@endsection

@section('script')
<script>
(function () {
    const accumulatedFiles = new WeakMap();
    const form = document.getElementById('create-debitur-form');
    const btnSaveStep = document.getElementById('btn-save-step');
    const btnPrevStep = document.getElementById('btn-prev-step');
    const btnNextStep = document.getElementById('btn-next-step');
    const btnResetDraft = document.getElementById('btn-reset-draft');
    const inputDebiturId = document.getElementById('input-debitur-id');
    const inputPreviousLoanId = document.getElementById('input-previous-loan-id');
    const stepCards = Array.from(document.querySelectorAll('[data-step-card]'));
    const stepIndicators = Array.from(document.querySelectorAll('[data-step-indicator]'));
    const stepEditButtons = Array.from(document.querySelectorAll('.step-edit-btn'));
    const helperText = document.getElementById('stepper-helper-text');
    const alertBox = document.getElementById('stepper-alert');
    const cardProgressList = document.getElementById('card-progress-list');
    const csrfToken = form.querySelector('input[name="_token"]')?.value || '';
    const debiturBaseUrl = "{{ url('apps-ecommerce/debitur') }}";
    const existingDraft = @json($draftDebitur ? $draftDebitur->toArray() : null);
    const existingDraftMedia = @json($draftMedia ?? []);
    const hasOldInput = @json(session()->hasOldInput());

    let currentStep = 1;
    let completedStep = 0;
    let debiturId = '';
    let isSavingStep = false;
    let editingStep = null;

    const stepTitles = {
        1: 'Step 1: Informasi Pribadi, Alamat KTP, dan Data Rekening',
        2: 'Step 2: Informasi Domisili, Kontak Keluarga, dan Data Pekerjaan',
        3: 'Step 3: Keuangan tambahan dan Upload Dokumen',
    };

    function syncInputFiles(input, files) {
        if (typeof DataTransfer === 'undefined') return;
        const dt = new DataTransfer();
        files.forEach(f => dt.items.add(f));
        input.files = dt.files;
    }

    function showAlert(type, message) {
        if (!alertBox) return;
        alertBox.className = `alert alert-${type} mt-3 mb-0`;
        alertBox.textContent = message;
        alertBox.classList.remove('d-none');
    }

    function clearAlert() {
        if (!alertBox) return;
        alertBox.classList.add('d-none');
        alertBox.textContent = '';
    }

    function renderStepper() {
        stepCards.forEach(card => {
            const stepNum = Number(card.dataset.stepCard || 0);
            const locked = stepNum <= completedStep && editingStep !== stepNum;
            card.classList.toggle('is-hidden-step', stepNum !== currentStep);
            card.classList.toggle('is-locked-step', locked);
        });

        stepIndicators.forEach(item => {
            const stepNum = Number(item.dataset.stepIndicator || 0);
            item.classList.toggle('active', stepNum === currentStep);
            item.classList.toggle('completed', stepNum <= completedStep);
        });

        if (btnPrevStep) {
            btnPrevStep.classList.toggle('d-none', currentStep === 1);
        }

        if (btnNextStep) {
            const canMoveNext = currentStep < 3 && currentStep <= completedStep;
            btnNextStep.classList.toggle('d-none', !canMoveNext);
        }

        if (btnSaveStep) {
            const isEditingCurrent = editingStep === currentStep;
            const text = isEditingCurrent
                ? `Update Step ${currentStep}`
                : (currentStep === 3 ? 'Simpan Step 3 & Submit' : `Simpan Step ${currentStep} & Lanjut`);
            btnSaveStep.innerHTML = '<i class="ri-save-line me-1"></i>' + text;

            // Enable save button for the current step when it's allowed to be saved.
            // Allow saving if the current step is already completed or is the immediate next step
            // after the last completed step (e.g., after Step 1 saved, Step 2 can be saved).
            if (isSavingStep) {
                btnSaveStep.disabled = true;
            } else {
                const canSave = currentStep <= (completedStep + 1);
                btnSaveStep.disabled = !canSave;
            }
        }

        if (helperText) {
            const modeText = editingStep === currentStep ? 'Mode edit aktif untuk step ini.' : '';
            helperText.textContent = `${stepTitles[currentStep] || ''} ${modeText}`.trim();
        }

        if (btnResetDraft) {
            btnResetDraft.classList.toggle('d-none', !debiturId);
        }

        stepEditButtons.forEach(button => {
            const stepNum = Number(button.dataset.editStep || 0);
            const canEdit = !!debiturId && stepNum <= completedStep;
            button.classList.toggle('d-none', !canEdit);
            button.classList.toggle('text-success', editingStep === stepNum);
        });

        updateCardProgress();
    }

    function isFieldFilled(field) {
        if (!field || field.disabled || field.type === 'hidden') return true;

        if (field.type === 'file') {
            return field.files.length > 0 || field.dataset.hasSavedFile === '1';
        }

        if (field.type === 'checkbox' || field.type === 'radio') {
            const named = form.querySelectorAll(`[name="${field.name}"]`);
            return Array.from(named).some(item => item.checked);
        }

        return String(field.value || '').trim() !== '';
    }

    function isCardComplete(card) {
        const requiredFields = Array.from(card.querySelectorAll('input[required], select[required], textarea[required]'));
        if (!requiredFields.length) return true;
        return requiredFields.every(isFieldFilled);
    }

    function updateCardProgress() {
        if (!cardProgressList) return;

        cardProgressList.innerHTML = '';

        stepCards.forEach(card => {
            const title = card.dataset.cardTitle || 'Card';
            const done = isCardComplete(card);

            const titleNode = card.querySelector('.card-title');
            if (titleNode && !titleNode.querySelector('.card-status-badge')) {
                const badge = document.createElement('span');
                badge.className = 'badge ms-2 card-status-badge';
                titleNode.appendChild(badge);
            }

            const badgeNode = card.querySelector('.card-status-badge');
            if (badgeNode) {
                badgeNode.className = `badge ms-2 card-status-badge ${done ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning'}`;
                badgeNode.textContent = done ? 'Lengkap' : 'Belum Lengkap';
            }

            const col = document.createElement('div');
            col.className = 'col-lg-4 col-md-6';
            col.innerHTML = `
                <div class="card-progress-item ${done ? 'done' : ''}">
                    <span>${title}</span>
                    <span class="status">${done ? 'OK' : '...'}</span>
                </div>
            `;
            cardProgressList.appendChild(col);
        });
    }

    function bindProgressWatcher() {
        const fields = form.querySelectorAll('input, select, textarea');
        fields.forEach(field => {
            field.addEventListener('change', updateCardProgress);
            field.addEventListener('input', updateCardProgress);
        });
    }

    async function waitForOption(selectElement, value, maxTry = 30) {
        if (!selectElement || !value) return;

        for (let i = 0; i < maxTry; i++) {
            const found = Array.from(selectElement.options).some(opt => String(opt.value) === String(value));
            if (found) {
                selectElement.value = String(value);
                selectElement.dispatchEvent(new Event('change'));
                return;
            }
            await new Promise(resolve => setTimeout(resolve, 100));
        }
    }

    async function hydrateCascade(provId, kabId, kecId, kelId, provSel, kabSel, kecSel, kelSel) {
        if (!provSel || !provId) return;

        provSel.value = String(provId);
        provSel.dispatchEvent(new Event('change'));
        await waitForOption(kabSel, kabId);
        await waitForOption(kecSel, kecId);
        await waitForOption(kelSel, kelId);
    }

    function applySavedMediaIndicators() {
        const mediaMap = {
            foto_ktp: { inputId: 'file-foto-ktp', nameId: 'name-foto-ktp', areaId: 'area-foto-ktp' },
            ktp_pasangan: { inputId: 'file-ktp-pasangan', nameId: 'name-ktp-pasangan', areaId: 'area-ktp-pasangan' },
            foto_bpjs: { inputId: 'file-foto-bpjs', nameId: 'name-foto-bpjs', areaId: 'area-foto-bpjs' },
            selfie_ktp: { inputId: 'file-selfie-ktp', nameId: 'name-selfie-ktp', areaId: 'area-selfie-ktp' },
            kartu_keluarga: { inputId: 'file-kartu-keluarga', nameId: 'name-kartu-keluarga', areaId: 'area-kartu-keluarga' },
            privy_id_file: { inputId: 'file-privy-id', nameId: 'name-privy-id', areaId: 'area-privy-id' },
            id_card: { inputId: 'file-id-card', nameId: 'name-id-card', areaId: 'area-id-card' },
            slip_gaji: { inputId: 'file-slip-gaji', nameId: 'name-slip-gaji', areaId: 'area-slip-gaji' },
            buku_nikah: { inputId: 'file-buku-nikah', nameId: 'name-buku-nikah', areaId: 'area-buku-nikah' },
            dokumen_pendukung: { inputId: 'file-dokumen-pendukung', nameId: 'name-dokumen-pendukung', areaId: 'area-dokumen-pendukung' }
        };

        Object.keys(mediaMap).forEach(key => {
            const fileName = existingDraftMedia?.[key];
            if (!fileName) return;

            const cfg = mediaMap[key];
            const inputEl = document.getElementById(cfg.inputId);
            const nameEl = document.getElementById(cfg.nameId);
            const areaEl = document.getElementById(cfg.areaId);

            if (inputEl) {
                inputEl.dataset.hasSavedFile = '1';
                inputEl.required = false;
            }
            if (nameEl) {
                nameEl.textContent = `Tersimpan: ${fileName}`;
                nameEl.classList.remove('d-none');
            }
            if (areaEl) {
                areaEl.classList.add('has-file');
            }
        });

        const dokumenPendukungCount = Number(existingDraftMedia?.dokumen_pendukung_count || 0);
        if (dokumenPendukungCount > 0) {
            const nameEl = document.getElementById('name-dokumen-pendukung');
            const areaEl = document.getElementById('area-dokumen-pendukung');
            if (nameEl) {
                nameEl.textContent = `${dokumenPendukungCount} file pendukung tersimpan`;
                nameEl.classList.remove('d-none');
            }
            if (areaEl) {
                areaEl.classList.add('has-file');
            }
        }

        const mutasiCount = Number(existingDraftMedia?.mutasi_bank_count || 0);
        if (mutasiCount > 0) {
            const nameEl = document.getElementById('name-mutasi-bank');
            const areaEl = document.getElementById('area-mutasi-bank');
            if (nameEl) {
                nameEl.textContent = `${mutasiCount} file mutasi tersimpan`;
                nameEl.classList.remove('d-none');
            }
            if (areaEl) {
                areaEl.classList.add('has-file');
            }
        }
    }

    async function resumeDraftIfAny() {
        if (hasOldInput || !existingDraft || !existingDraft.id) return;

        Object.keys(existingDraft).forEach(key => {
            const value = existingDraft[key];
            if (value === null || value === undefined) return;

            const field = form.querySelector(`[name="${key}"]`);
            if (!field) return;

            if (field.tagName === 'SELECT' || field.tagName === 'TEXTAREA' || field.type !== 'radio') {
                field.value = String(value);
            }
        });

        debiturId = String(existingDraft.id);
        if (inputDebiturId) {
            inputDebiturId.value = debiturId;
        }

        applySavedMediaIndicators();

        if (typeof toggleSpouseFields === 'function') {
            toggleSpouseFields();
        }

        await hydrateCascade(
            existingDraft.kode_provinsi,
            existingDraft.kode_kabupaten,
            existingDraft.kode_kecamatan,
            existingDraft.kode_kelurahan,
            document.getElementById('select-provinsi'),
            document.getElementById('select-kabupaten'),
            document.getElementById('select-kecamatan'),
            document.getElementById('select-kelurahan')
        );

        await hydrateCascade(
            existingDraft.kode_provinsi_domisili,
            existingDraft.kode_kabupaten_domisili,
            existingDraft.kode_kecamatan_domisili,
            existingDraft.kode_kelurahan_domisili,
            document.getElementById('select-provinsi-domisili'),
            document.getElementById('select-kabupaten-domisili'),
            document.getElementById('select-kecamatan-domisili'),
            document.getElementById('select-kelurahan-domisili')
        );

        await hydrateCascade(
            existingDraft.kode_provinsi_ktp_keluarga,
            existingDraft.kode_kabupaten_ktp_keluarga,
            existingDraft.kode_kecamatan_ktp_keluarga,
            existingDraft.kode_kelurahan_ktp_keluarga,
            document.getElementById('select-provinsi-ktp-keluarga'),
            document.getElementById('select-kabupaten-ktp-keluarga'),
            document.getElementById('select-kecamatan-ktp-keluarga'),
            document.getElementById('select-kelurahan-ktp-keluarga')
        );

        if (existingDraft.status_tahap === 'draft_step1') {
            completedStep = 1;
            currentStep = 2;
        } else if (existingDraft.status_tahap === 'draft_step2') {
            completedStep = 2;
            currentStep = 3;
        }

        showAlert('info', `Draft debitur #${debiturId} berhasil dimuat. Lanjutkan dari step berikutnya.`);
    }

    function validateCurrentStep() {
        const activeCards = stepCards.filter(card => Number(card.dataset.stepCard || 0) === currentStep);
        if (!activeCards.length) return true;

        for (const card of activeCards) {
            const fields = card.querySelectorAll('input, select, textarea');
            for (const field of fields) {
                if (field.disabled) continue;
                if (field.type === 'file' && field.required && field.files.length === 0 && field.dataset.hasSavedFile === '1') {
                    continue;
                }
                if (!field.checkValidity()) {
                    field.reportValidity();
                    return false;
                }
            }
        }
        return true;
    }

    function collectCurrentStepFormData() {
        const activeCards = stepCards.filter(card => Number(card.dataset.stepCard || 0) === currentStep);
        const payload = new FormData();
        payload.append('_token', csrfToken);

        if (debiturId) {
            payload.append('debitur_id', debiturId);
        }

        if (inputPreviousLoanId && inputPreviousLoanId.value) {
            payload.append('previous_loan_id', inputPreviousLoanId.value);
        }

        if (!activeCards.length) return payload;

        activeCards.forEach(card => {
            const fields = card.querySelectorAll('input, select, textarea');
            fields.forEach(field => {
                if (!field.name || field.disabled) return;

                if (field.type === 'file') {
                    Array.from(field.files || []).forEach(file => payload.append(field.name, file));
                    return;
                }

                if ((field.type === 'checkbox' || field.type === 'radio') && !field.checked) {
                    return;
                }

                payload.append(field.name, field.value ?? '');
            });
        });

        if (currentStep > 1) {
            payload.append('_method', 'PUT');
        }

        return payload;
    }

    function stepEndpoint(step) {
        if (step === 1) {
            return "{{ route('debitur.store.step1') }}";
        }
        if (!debiturId) return '';
        if (step === 2) {
            return `${debiturBaseUrl}/${debiturId}/store-step2`;
        }
        return `${debiturBaseUrl}/${debiturId}/store-step3`;
    }

    async function resetDraft() {
        if (!debiturId) return;

        const confirmed = window.confirm('Draft saat ini akan dihapus permanen. Lanjutkan reset draft?');
        if (!confirmed) return;

        try {
            if (btnResetDraft) {
                btnResetDraft.disabled = true;
            }

            const payload = new FormData();
            payload.append('_token', csrfToken);
            payload.append('_method', 'DELETE');
            payload.append('debitur_id', debiturId);

            const response = await fetch("{{ route('debitur.reset.draft') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: payload
            });

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result?.message || 'Gagal reset draft.');
            }

            showAlert('success', result.message || 'Draft berhasil direset.');
            setTimeout(() => window.location.reload(), 450);
        } catch (error) {
            showAlert('danger', error.message || 'Terjadi kesalahan saat reset draft.');
            if (btnResetDraft) {
                btnResetDraft.disabled = false;
            }
        }
    }

    async function saveCurrentStep() {
        if (isSavingStep) return;
        clearAlert();

        const isEditingCurrent = editingStep === currentStep;
        const needsImpactConfirm = isEditingCurrent && completedStep >= 3 && (currentStep === 1 || currentStep === 2);
        if (needsImpactConfirm) {
            const proceed = window.confirm(
                `Anda sedang mengubah Step ${currentStep} setelah Step 3 tersimpan. Perubahan ini dapat mempengaruhi data di step berikutnya. Lanjutkan update?`
            );
            if (!proceed) {
                return;
            }
        }

        if (!validateCurrentStep()) {
            return;
        }

        if (currentStep > 1 && !debiturId) {
            showAlert('danger', 'Step 1 belum tersimpan. Simpan Step 1 terlebih dahulu.');
            currentStep = 1;
            renderStepper();
            return;
        }

        const url = stepEndpoint(currentStep);
        if (!url) {
            showAlert('danger', 'URL simpan step tidak valid.');
            return;
        }

        try {
            isSavingStep = true;
            if (btnSaveStep) {
                btnSaveStep.disabled = true;
                btnSaveStep.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';
            }

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: collectCurrentStepFormData()
            });

            const result = await response.json();

            if (!response.ok) {
                const firstError = result?.errors ? Object.values(result.errors)[0]?.[0] : null;
                throw new Error(firstError || result?.message || 'Gagal menyimpan step.');
            }

            debiturId = String(result.debitur_id || debiturId || '');
            if (inputDebiturId) {
                inputDebiturId.value = debiturId;
            }

            completedStep = Math.max(completedStep, currentStep);
            editingStep = null;
            showAlert('success', result.message || `Step ${currentStep} berhasil disimpan.`);

            if (currentStep < 3) {
                currentStep += 1;
                renderStepper();
                return;
            }

            if (result.redirect_url) {
                window.location.href = result.redirect_url;
            }
        } catch (error) {
            showAlert('danger', error.message || 'Terjadi kesalahan saat menyimpan step.');
        } finally {
            isSavingStep = false;
            renderStepper();
        }
    }

    if (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
        });
    }

    if (btnSaveStep) {
        btnSaveStep.addEventListener('click', saveCurrentStep);
    }

    if (btnPrevStep) {
        btnPrevStep.addEventListener('click', function () {
            if (currentStep <= 1) return;
            currentStep -= 1;
            editingStep = null;
            clearAlert();
            renderStepper();
        });
    }

    if (btnNextStep) {
        btnNextStep.addEventListener('click', function () {
            if (currentStep >= 3) return;
            if (currentStep > completedStep) return;
            currentStep += 1;
            editingStep = null;
            clearAlert();
            renderStepper();
        });
    }

    stepEditButtons.forEach(button => {
        button.addEventListener('click', function () {
            const stepNum = Number(this.dataset.editStep || 0);
            if (!debiturId || stepNum < 1 || stepNum > completedStep) return;

            currentStep = stepNum;
            editingStep = stepNum;
            showAlert('info', `Mode edit aktif pada Step ${stepNum}. Silakan ubah data lalu klik Update Step ${stepNum}.`);
            renderStepper();
        });
    });

    if (btnResetDraft) {
        btnResetDraft.addEventListener('click', resetDraft);
    }

    // ── File Upload Preview ────────────────────────────────────────────────────
    window.handleFilePreview = function (input, previewId, placeholderId, nameId, listId = null) {
        let files         = Array.from(input.files || []);
        const file        = files[0];
        const preview     = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        const nameEl      = document.getElementById(nameId);
        const listEl      = listId ? document.getElementById(listId) : null;
        const area        = input.closest('.col-lg-3, .col-md-6')?.querySelector('.upload-area');

        if (input.multiple) {
            const previousFiles = accumulatedFiles.get(input) || [];
            const mergedFiles = [...previousFiles];

            files.forEach(newFile => {
                const alreadyExists = mergedFiles.some(existingFile =>
                    existingFile.name === newFile.name &&
                    existingFile.size === newFile.size &&
                    existingFile.lastModified === newFile.lastModified
                );

                if (!alreadyExists) {
                    mergedFiles.push(newFile);
                }
            });

            files = mergedFiles;
            accumulatedFiles.set(input, files);
            syncInputFiles(input, files);
        }

        if (!files.length) return;

        if (listEl) {
            listEl.innerHTML = '';
            listEl.classList.add('d-none');
        }

        if (input.multiple && listEl) {
            const renderMultiFileList = () => {
                listEl.innerHTML = '';

                if (!files.length) {
                    nameEl.textContent = '';
                    nameEl.title = '';
                    nameEl.classList.add('d-none');
                    preview.classList.add('d-none');
                    if (placeholder) placeholder.style.opacity = '1';
                    if (area) area.classList.remove('has-file');
                    listEl.classList.add('d-none');
                    return;
                }

                nameEl.textContent = `${files.length} file dipilih`;
                nameEl.title = files.map(f => f.name).join('\n');
                nameEl.classList.remove('d-none');

                preview.classList.add('d-none');
                if (placeholder) placeholder.style.opacity = '1';
                if (area) area.classList.add('has-file');

                files.forEach((fileItem, index) => {
                    const itemEl = document.createElement('div');
                    itemEl.className = 'multi-file-item';

                    if (fileItem.type.startsWith('image/')) {
                        const imgEl = document.createElement('img');
                        imgEl.className = 'multi-file-thumb';
                        imgEl.alt = fileItem.name;
                        imgEl.src = URL.createObjectURL(fileItem);
                        imgEl.onload = () => URL.revokeObjectURL(imgEl.src);
                        itemEl.appendChild(imgEl);
                    } else {
                        const pdfEl = document.createElement('div');
                        pdfEl.className = 'multi-file-pdf';
                        pdfEl.textContent = 'PDF';
                        itemEl.appendChild(pdfEl);
                    }

                    const nameItemEl = document.createElement('div');
                    nameItemEl.className = 'multi-file-name';
                    nameItemEl.textContent = fileItem.name;
                    nameItemEl.title = fileItem.name;
                    itemEl.appendChild(nameItemEl);

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'multi-file-remove';
                    removeBtn.textContent = 'Hapus';
                    removeBtn.addEventListener('click', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        files = files.filter((_, fileIndex) => fileIndex !== index);
                        accumulatedFiles.set(input, files);
                        syncInputFiles(input, files);
                        renderMultiFileList();
                    });
                    itemEl.appendChild(removeBtn);

                    listEl.appendChild(itemEl);
                });

                listEl.classList.remove('d-none');
            };

            renderMultiFileList();
            return;
        }

        nameEl.textContent = file.name;
        nameEl.title = '';
        nameEl.classList.remove('d-none');

        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                if (placeholder) placeholder.style.opacity = '0';
                if (area) area.classList.add('has-file');
            };
            reader.readAsDataURL(file);
        } else {
            // PDF — show icon only
            preview.classList.add('d-none');
            if (placeholder) { placeholder.style.opacity = '1'; }
            if (area) area.classList.add('has-file');
        }
    };

    // ── Perusahaan → auto-fill alamat ─────────────────────────────────────────
    const selPerusahaan       = document.getElementById('select-perusahaan');
    const inpPerusahaanId     = document.getElementById('input-perusahaan-id');
    const txtAlamatPerusahaan = document.getElementById('textarea-alamat-perusahaan');

    selPerusahaan.addEventListener('change', function () {
        const opt   = this.options[this.selectedIndex];
        const id    = opt?.dataset?.id    ?? '';
        const alamat= opt?.dataset?.alamat ?? '';

        inpPerusahaanId.value     = id;
        txtAlamatPerusahaan.value = alamat;
    });

    // ── IndoRegion cascading dropdowns ────────────────────────────────────────
    const selProvinsi  = document.getElementById('select-provinsi');
    const selKabupaten = document.getElementById('select-kabupaten');
    const selKecamatan = document.getElementById('select-kecamatan');
    const selKelurahan = document.getElementById('select-kelurahan');

    const inpProvinsiName  = document.getElementById('input-provinsi-name');
    const inpKabupatenName = document.getElementById('input-kabupaten-name');
    const inpKecamatanName = document.getElementById('input-kecamatan-name');
    const inpKelurahanName = document.getElementById('input-kelurahan-name');

    function resetSelect(sel, placeholder) {
        sel.innerHTML = `<option value="">${placeholder}</option>`;
        sel.disabled  = true;
    }

    function toTitleCase(str) {
        return str.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
    }

    // ── Provinsi changed ──────────────────────────────────────────────────────
    selProvinsi.addEventListener('change', function () {
        const id   = this.value;
        const name = this.options[this.selectedIndex]?.dataset?.name ?? '';

        inpProvinsiName.value = name;

        resetSelect(selKabupaten, '-- Pilih Kota/Kabupaten --');
        resetSelect(selKecamatan, '-- Pilih Kecamatan --');
        resetSelect(selKelurahan, '-- Pilih Kelurahan/Desa --');
        inpKabupatenName.value = '';
        inpKecamatanName.value = '';
        inpKelurahanName.value = '';

        if (!id) return;

        selKabupaten.disabled = true;
        selKabupaten.innerHTML = '<option value="">Memuat data...</option>';

        fetch(`/apps-ecommerce/wilayah/kabupaten/${id}`)
            .then(r => r.json())
            .then(data => {
                selKabupaten.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value        = item.id;
                    opt.dataset.name = item.name;
                    opt.textContent  = toTitleCase(item.name);
                    selKabupaten.appendChild(opt);
                });
                selKabupaten.disabled = false;
            })
            .catch(() => resetSelect(selKabupaten, '-- Gagal memuat --'));
    });

    // ── Kabupaten changed ─────────────────────────────────────────────────────
    selKabupaten.addEventListener('change', function () {
        const id   = this.value;
        const name = this.options[this.selectedIndex]?.dataset?.name ?? '';

        inpKabupatenName.value = name;

        resetSelect(selKecamatan, '-- Pilih Kecamatan --');
        resetSelect(selKelurahan, '-- Pilih Kelurahan/Desa --');
        inpKecamatanName.value = '';
        inpKelurahanName.value = '';

        if (!id) return;

        selKecamatan.disabled = true;
        selKecamatan.innerHTML = '<option value="">Memuat data...</option>';

        fetch(`/apps-ecommerce/wilayah/kecamatan/${id}`)
            .then(r => r.json())
            .then(data => {
                selKecamatan.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value        = item.id;
                    opt.dataset.name = item.name;
                    opt.textContent  = toTitleCase(item.name);
                    selKecamatan.appendChild(opt);
                });
                selKecamatan.disabled = false;
            })
            .catch(() => resetSelect(selKecamatan, '-- Gagal memuat --'));
    });

    // ── Kecamatan changed ─────────────────────────────────────────────────────
    selKecamatan.addEventListener('change', function () {
        const id   = this.value;
        const name = this.options[this.selectedIndex]?.dataset?.name ?? '';

        inpKecamatanName.value = name;

        resetSelect(selKelurahan, '-- Pilih Kelurahan/Desa --');
        inpKelurahanName.value = '';

        if (!id) return;

        selKelurahan.disabled = true;
        selKelurahan.innerHTML = '<option value="">Memuat data...</option>';

        fetch(`/apps-ecommerce/wilayah/kelurahan/${id}`)
            .then(r => r.json())
            .then(data => {
                selKelurahan.innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>';
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value        = item.id;
                    opt.dataset.name = item.name;
                    opt.textContent  = toTitleCase(item.name);
                    selKelurahan.appendChild(opt);
                });
                selKelurahan.disabled = false;
            })
            .catch(() => resetSelect(selKelurahan, '-- Gagal memuat --'));
    });

    // ── Kelurahan changed ─────────────────────────────────────────────────────
    selKelurahan.addEventListener('change', function () {
        const name = this.options[this.selectedIndex]?.dataset?.name ?? '';
        inpKelurahanName.value = name;
    });

    const selStatusPerkawinan = document.getElementById('select-status-perkawinan');
    const spouseSection = document.getElementById('spouse-section');
    const ktpPasanganWrapper = document.getElementById('ktp-pasangan-wrapper');
    const spouseRequiredInputs = [
        document.getElementById('input-nama-pasangan'),
        document.getElementById('input-nik-pasangan'),
        document.getElementById('input-hp-pasangan'),
        document.getElementById('file-ktp-pasangan')
    ];

    function toggleSpouseFields() {
        const married = selStatusPerkawinan.value === 'Menikah';
        spouseSection.classList.toggle('d-none', !married);
        ktpPasanganWrapper.classList.toggle('d-none', !married);

        spouseRequiredInputs.forEach(element => {
            if (!element) return;
            element.required = married;
        });
    }

    selStatusPerkawinan.addEventListener('change', toggleSpouseFields);
    toggleSpouseFields();

    // ── IndoRegion cascading dropdowns for DOMISILI ──────────────────────────
    const selProvinsiDomisili  = document.getElementById('select-provinsi-domisili');
    const selKabupatenDomisili = document.getElementById('select-kabupaten-domisili');
    const selKecamatanDomisili = document.getElementById('select-kecamatan-domisili');
    const selKelurahanDomisili = document.getElementById('select-kelurahan-domisili');

    const inpProvinsiDomisiliName  = document.getElementById('input-provinsi-domisili-name');
    const inpKabupatenDomisiliName = document.getElementById('input-kabupaten-domisili-name');
    const inpKecamatanDomisiliName = document.getElementById('input-kecamatan-domisili-name');
    const inpKelurahanDomisiliName = document.getElementById('input-kelurahan-domisili-name');

    // ── Provinsi Domisili changed ────────────────────────────────────────────
    selProvinsiDomisili.addEventListener('change', function () {
        const id   = this.value;
        const name = this.options[this.selectedIndex]?.dataset?.name ?? '';

        inpProvinsiDomisiliName.value = name;

        resetSelect(selKabupatenDomisili, '-- Pilih Kota/Kabupaten --');
        resetSelect(selKecamatanDomisili, '-- Pilih Kecamatan --');
        resetSelect(selKelurahanDomisili, '-- Pilih Kelurahan/Desa --');
        inpKabupatenDomisiliName.value = '';
        inpKecamatanDomisiliName.value = '';
        inpKelurahanDomisiliName.value = '';

        if (!id) return;

        selKabupatenDomisili.disabled = true;
        selKabupatenDomisili.innerHTML = '<option value="">Memuat data...</option>';

        fetch(`/apps-ecommerce/wilayah/kabupaten/${id}`)
            .then(r => r.json())
            .then(data => {
                selKabupatenDomisili.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value        = item.id;
                    opt.dataset.name = item.name;
                    opt.textContent  = toTitleCase(item.name);
                    selKabupatenDomisili.appendChild(opt);
                });
                selKabupatenDomisili.disabled = false;
            })
            .catch(() => resetSelect(selKabupatenDomisili, '-- Gagal memuat --'));
    });

    // ── Kabupaten Domisili changed ───────────────────────────────────────────
    selKabupatenDomisili.addEventListener('change', function () {
        const id   = this.value;
        const name = this.options[this.selectedIndex]?.dataset?.name ?? '';

        inpKabupatenDomisiliName.value = name;

        resetSelect(selKecamatanDomisili, '-- Pilih Kecamatan --');
        resetSelect(selKelurahanDomisili, '-- Pilih Kelurahan/Desa --');
        inpKecamatanDomisiliName.value = '';
        inpKelurahanDomisiliName.value = '';

        if (!id) return;

        selKecamatanDomisili.disabled = true;
        selKecamatanDomisili.innerHTML = '<option value="">Memuat data...</option>';

        fetch(`/apps-ecommerce/wilayah/kecamatan/${id}`)
            .then(r => r.json())
            .then(data => {
                selKecamatanDomisili.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value        = item.id;
                    opt.dataset.name = item.name;
                    opt.textContent  = toTitleCase(item.name);
                    selKecamatanDomisili.appendChild(opt);
                });
                selKecamatanDomisili.disabled = false;
            })
            .catch(() => resetSelect(selKecamatanDomisili, '-- Gagal memuat --'));
    });

    // ── Kecamatan Domisili changed ────────────────────────────────────────────
    selKecamatanDomisili.addEventListener('change', function () {
        const id   = this.value;
        const name = this.options[this.selectedIndex]?.dataset?.name ?? '';

        inpKecamatanDomisiliName.value = name;

        resetSelect(selKelurahanDomisili, '-- Pilih Kelurahan/Desa --');
        inpKelurahanDomisiliName.value = '';

        if (!id) return;

        selKelurahanDomisili.disabled = true;
        selKelurahanDomisili.innerHTML = '<option value="">Memuat data...</option>';

        fetch(`/apps-ecommerce/wilayah/kelurahan/${id}`)
            .then(r => r.json())
            .then(data => {
                selKelurahanDomisili.innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>';
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value        = item.id;
                    opt.dataset.name = item.name;
                    opt.textContent  = toTitleCase(item.name);
                    selKelurahanDomisili.appendChild(opt);
                });
                selKelurahanDomisili.disabled = false;
            })
            .catch(() => resetSelect(selKelurahanDomisili, '-- Gagal memuat --'));
    });

    // ── Kelurahan Domisili changed ────────────────────────────────────────────
    selKelurahanDomisili.addEventListener('change', function () {
        const name = this.options[this.selectedIndex]?.dataset?.name ?? '';
        inpKelurahanDomisiliName.value = name;
    });

    // ── IndoRegion cascading dropdowns for KTP KELUARGA ──────────────────────
    const selProvinsiKtpKeluarga  = document.getElementById('select-provinsi-ktp-keluarga');
    const selKabupatenKtpKeluarga = document.getElementById('select-kabupaten-ktp-keluarga');
    const selKecamatanKtpKeluarga = document.getElementById('select-kecamatan-ktp-keluarga');
    const selKelurahanKtpKeluarga = document.getElementById('select-kelurahan-ktp-keluarga');

    const inpProvinsiKtpKeluargaName  = document.getElementById('input-provinsi-ktp-keluarga-name');
    const inpKabupatenKtpKeluargaName = document.getElementById('input-kabupaten-ktp-keluarga-name');
    const inpKecamatanKtpKeluargaName = document.getElementById('input-kecamatan-ktp-keluarga-name');
    const inpKelurahanKtpKeluargaName = document.getElementById('input-kelurahan-ktp-keluarga-name');

    // ── Provinsi KTP Keluarga changed ─────────────────────────────────────────
    selProvinsiKtpKeluarga.addEventListener('change', function () {
        const id   = this.value;
        const name = this.options[this.selectedIndex]?.dataset?.name ?? '';

        inpProvinsiKtpKeluargaName.value = name;

        resetSelect(selKabupatenKtpKeluarga, '-- Pilih Kota/Kabupaten --');
        resetSelect(selKecamatanKtpKeluarga, '-- Pilih Kecamatan --');
        resetSelect(selKelurahanKtpKeluarga, '-- Pilih Kelurahan/Desa --');
        inpKabupatenKtpKeluargaName.value = '';
        inpKecamatanKtpKeluargaName.value = '';
        inpKelurahanKtpKeluargaName.value = '';

        if (!id) return;

        selKabupatenKtpKeluarga.disabled = true;
        selKabupatenKtpKeluarga.innerHTML = '<option value="">Memuat data...</option>';

        fetch(`/apps-ecommerce/wilayah/kabupaten/${id}`)
            .then(r => r.json())
            .then(data => {
                selKabupatenKtpKeluarga.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value        = item.id;
                    opt.dataset.name = item.name;
                    opt.textContent  = toTitleCase(item.name);
                    selKabupatenKtpKeluarga.appendChild(opt);
                });
                selKabupatenKtpKeluarga.disabled = false;
            })
            .catch(() => resetSelect(selKabupatenKtpKeluarga, '-- Gagal memuat --'));
    });

    // ── Kabupaten KTP Keluarga changed ────────────────────────────────────────
    selKabupatenKtpKeluarga.addEventListener('change', function () {
        const id   = this.value;
        const name = this.options[this.selectedIndex]?.dataset?.name ?? '';

        inpKabupatenKtpKeluargaName.value = name;

        resetSelect(selKecamatanKtpKeluarga, '-- Pilih Kecamatan --');
        resetSelect(selKelurahanKtpKeluarga, '-- Pilih Kelurahan/Desa --');
        inpKecamatanKtpKeluargaName.value = '';
        inpKelurahanKtpKeluargaName.value = '';

        if (!id) return;

        selKecamatanKtpKeluarga.disabled = true;
        selKecamatanKtpKeluarga.innerHTML = '<option value="">Memuat data...</option>';

        fetch(`/apps-ecommerce/wilayah/kecamatan/${id}`)
            .then(r => r.json())
            .then(data => {
                selKecamatanKtpKeluarga.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value        = item.id;
                    opt.dataset.name = item.name;
                    opt.textContent  = toTitleCase(item.name);
                    selKecamatanKtpKeluarga.appendChild(opt);
                });
                selKecamatanKtpKeluarga.disabled = false;
            })
            .catch(() => resetSelect(selKecamatanKtpKeluarga, '-- Gagal memuat --'));
    });

    // ── Kecamatan KTP Keluarga changed ─────────────────────────────────────────
    selKecamatanKtpKeluarga.addEventListener('change', function () {
        const id   = this.value;
        const name = this.options[this.selectedIndex]?.dataset?.name ?? '';

        inpKecamatanKtpKeluargaName.value = name;

        resetSelect(selKelurahanKtpKeluarga, '-- Pilih Kelurahan/Desa --');
        inpKelurahanKtpKeluargaName.value = '';

        if (!id) return;

        selKelurahanKtpKeluarga.disabled = true;
        selKelurahanKtpKeluarga.innerHTML = '<option value="">Memuat data...</option>';

        fetch(`/apps-ecommerce/wilayah/kelurahan/${id}`)
            .then(r => r.json())
            .then(data => {
                selKelurahanKtpKeluarga.innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>';
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value        = item.id;
                    opt.dataset.name = item.name;
                    opt.textContent  = toTitleCase(item.name);
                    selKelurahanKtpKeluarga.appendChild(opt);
                });
                selKelurahanKtpKeluarga.disabled = false;
            })
            .catch(() => resetSelect(selKelurahanKtpKeluarga, '-- Gagal memuat --'));
    });

    // ── Kelurahan KTP Keluarga changed ────────────────────────────────────────
    selKelurahanKtpKeluarga.addEventListener('change', function () {
        const name = this.options[this.selectedIndex]?.dataset?.name ?? '';
        inpKelurahanKtpKeluargaName.value = name;
    });

    bindProgressWatcher();
    resumeDraftIfAny().finally(() => {
        renderStepper();
    });
})();
</script>
@endsection
@endsection
