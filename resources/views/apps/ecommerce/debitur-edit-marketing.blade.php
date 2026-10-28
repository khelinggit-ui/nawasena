@extends('layouts.master')
@section('title', 'Edit Debitur | NSB')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Perbaiki Data Debitur (Revisi)</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('debitur.index') }}">Debitur</a></li>
                            <li class="breadcrumb-item active">Tahap Revisi</li>
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

        <form id="create-debitur-form" action="{{ route('debitur.update', $debitur->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- CARD: Informasi Pribadi -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Informasi Pribadi</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label class="form-label">Nama Peminjam <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_peminjam" class="form-control" required placeholder="Masukkan nama peminjam" value="{{ $debitur->nama_peminjam }}">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Nomor KTP <span class="text-danger">*</span></label>
                                    <input type="text" name="nomor_ktp" class="form-control @error('nomor_ktp') is-invalid @enderror" required placeholder="16 Digit NIK" value="{{ $debitur->nomor_ktp }}" maxlength="16">
                                    @error('nomor_ktp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ $debitur->email }}" required placeholder="Email aktif">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Nomor Handphone <span class="text-danger">*</span></label>
                                    <input type="text" name="nomor_handphone" class="form-control @error('nomor_handphone') is-invalid @enderror" value="{{ $debitur->nomor_handphone }}" required placeholder="Contoh: 08123456789">
                                    @error('nomor_handphone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select class="form-select" name="jenis_kelamin">
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="Laki-laki" {{ $debitur->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ $debitur->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" class="form-control" value="{{ $debitur->tempat_lahir }}" placeholder="Kota lahir">
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ $debitur->tanggal_lahir }}">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Nama Ibu Kandung <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_ibu_kandung" class="form-control @error('nama_ibu_kandung') is-invalid @enderror" value="{{ $debitur->nama_ibu_kandung }}" required placeholder="Nama ibu">
                                    @error('nama_ibu_kandung')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Agama</label>
                                    <select class="form-select" name="agama">
                                        <option value="">Pilih Agama</option>
                                        <option value="Islam" {{ $debitur->agama == 'Islam' ? 'selected' : '' }}>Islam</option>
                                        <option value="Kristen" {{ $debitur->agama == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                        <option value="Katolik" {{ $debitur->agama == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                        <option value="Hindu" {{ $debitur->agama == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                        <option value="Buddha" {{ $debitur->agama == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                        <option value="Konghucu" {{ $debitur->agama == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Pendidikan</label>
                                    <select class="form-select" name="pendidikan">
                                        <option value="">Pilih Pendidikan</option>
                                        <option value="SD" {{ $debitur->pendidikan == 'SD' ? 'selected' : '' }}>SD</option>
                                        <option value="SMP" {{ $debitur->pendidikan == 'SMP' ? 'selected' : '' }}>SMP</option>
                                        <option value="SMA" {{ $debitur->pendidikan == 'SMA' ? 'selected' : '' }}>SMA</option>
                                        <option value="D3" {{ $debitur->pendidikan == 'D3' ? 'selected' : '' }}>D3</option>
                                        <option value="S1" {{ $debitur->pendidikan == 'S1' ? 'selected' : '' }}>S1</option>
                                        <option value="S2" {{ $debitur->pendidikan == 'S2' ? 'selected' : '' }}>S2</option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Status Perkawinan <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status_perkawinan') is-invalid @enderror" name="status_perkawinan" id="select-status-perkawinan" required>
                                        <option value="">Pilih Status</option>
                                        <option value="Belum Menikah" {{ $debitur->status_perkawinan == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                                        <option value="Menikah" {{ $debitur->status_perkawinan == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                        <option value="Cerai" {{ $debitur->status_perkawinan == 'Cerai' ? 'selected' : '' }}>Cerai</option>
                                    </select>
                                    @error('status_perkawinan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Spouse Section (shown only if Menikah) -->
                                <div id="spouse-section" class="w-100 d-none">
                                    <div class="row g-3">
                                        <div class="col-lg-4">
                                            <label class="form-label">Nama Pasangan <span class="text-danger">*</span></label>
                                            <input type="text" name="nama_pasangan" id="input-nama-pasangan" class="form-control @error('nama_pasangan') is-invalid @enderror" value="{{ $debitur->nama_pasangan }}" placeholder="Nama pasangan">
                                            @error('nama_pasangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="form-label">NIK Pasangan <span class="text-danger">*</span></label>
                                            <input type="text" name="nik_pasangan" id="input-nik-pasangan" class="form-control @error('nik_pasangan') is-invalid @enderror" value="{{ $debitur->nik_pasangan }}" placeholder="NIK pasangan">
                                            @error('nik_pasangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="form-label">No Handphone Pasangan <span class="text-danger">*</span></label>
                                            <input type="text" name="nomor_hp_pasangan" id="input-hp-pasangan" class="form-control @error('nomor_hp_pasangan') is-invalid @enderror" value="{{ $debitur->nomor_hp_pasangan }}" placeholder="No HP pasangan">
                                            @error('nomor_hp_pasangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Privy ID</label>
                                    <input type="text" name="privy_id" class="form-control" value="{{ $debitur->privy_id }}" placeholder="Privy ID">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Nomor NPWP</label>
                                    <input type="text" name="nomor_npwp" class="form-control @error('nomor_npwp') is-invalid @enderror" value="{{ $debitur->nomor_npwp }}" placeholder="Nomor NPWP">
                                    @error('nomor_npwp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Pengajuan Plafond <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="pengajuan_plafond" class="form-control @error('pengajuan_plafond') is-invalid @enderror" required value="{{ $debitur->pengajuan_plafond }}">
                                        @error('pengajuan_plafond')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Tenor (Bulan) <span class="text-danger">*</span></label>
                                    <select class="form-select @error('tenor_bulan') is-invalid @enderror" name="tenor_bulan" required>
                                        <option value="">Pilih Tenor</option>
                                        <option value="6" {{ $debitur->tenor_bulan == '6' ? 'selected' : '' }}>6 Bulan</option>
                                        <option value="10" {{ $debitur->tenor_bulan == '10' ? 'selected' : '' }}>10 Bulan</option>
                                        <option value="12" {{ $debitur->tenor_bulan == '12' ? 'selected' : '' }}>12 Bulan</option>
                                        <option value="24" {{ $debitur->tenor_bulan == '24' ? 'selected' : '' }}>24 Bulan</option>
                                    </select>
                                    @error('tenor_bulan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">SPV</label>
                                    <input type="text" class="form-control bg-light" value="{{ optional($debitur->spv)->name ?: '-' }}" readonly>
                                    <input type="hidden" name="spv_id" value="{{ $debitur->spv_id }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD: Informasi Alamat (Sesuai KTP) -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Informasi Alamat (Sesuai KTP)</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <label class="form-label">Alamat KTP Lengkap <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="alamat_ktp" rows="3" required placeholder="Nama Jalan, Gedung, No. Rumah">{{ $debitur->alamat_ktp }}</textarea>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <label class="form-label">RT</label>
                                    <input type="text" name="rt_ktp" class="form-control" placeholder="000" value="{{ $debitur->rt_ktp }}">
                                </div>
                                <div class="col-lg-3 col-6">
                                    <label class="form-label">RW</label>
                                    <input type="text" name="rw_ktp" class="form-control" placeholder="000" value="{{ $debitur->rw_ktp }}">
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                                    <select id="select-provinsi" name="kode_provinsi" class="form-select" required>
                                        <option value="">-- Pilih Provinsi --</option>
                                        @foreach(\App\Models\Province::orderBy('name')->get() as $prov)
                                            <option value="{{ $prov->id }}" data-name="{{ $prov->name }}" {{ $debitur->kode_provinsi == $prov->id ? 'selected' : '' }}>{{ ucwords(strtolower($prov->name)) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="provinsi_ktp" id="input-provinsi-name" value="{{ $debitur->provinsi_ktp }}">
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Kota / Kabupaten <span class="text-danger">*</span></label>
                                    <select id="select-kabupaten" name="kode_kabupaten" class="form-select" required>
                                        <option value="">-- Pilih Kota/Kabupaten --</option>
                                        @if($debitur->kode_provinsi)
                                            @foreach(\App\Models\Regency::where('province_id', $debitur->kode_provinsi)->orderBy('name')->get() as $kab)
                                                <option value="{{ $kab->id }}" data-name="{{ $kab->name }}" {{ $debitur->kode_kabupaten == $kab->id ? 'selected' : '' }}>{{ ucwords(strtolower($kab->name)) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <input type="hidden" name="kota_kabupaten_ktp" id="input-kabupaten-name" value="{{ $debitur->kota_kabupaten_ktp }}">
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                    <select id="select-kecamatan" name="kode_kecamatan" class="form-select" required>
                                        <option value="">-- Pilih Kecamatan --</option>
                                        @if($debitur->kode_kabupaten)
                                            @foreach(\App\Models\District::where('regency_id', $debitur->kode_kabupaten)->orderBy('name')->get() as $kec)
                                                <option value="{{ $kec->id }}" data-name="{{ $kec->name }}" {{ $debitur->kode_kecamatan == $kec->id ? 'selected' : '' }}>{{ ucwords(strtolower($kec->name)) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <input type="hidden" name="kecamatan_ktp" id="input-kecamatan-name" value="{{ $debitur->kecamatan_ktp }}">
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Kelurahan / Desa <span class="text-danger">*</span></label>
                                    <select id="select-kelurahan" name="kode_kelurahan" class="form-select" required>
                                        <option value="">-- Pilih Kelurahan/Desa --</option>
                                        @if($debitur->kode_kecamatan)
                                            @foreach(\App\Models\Village::where('district_id', $debitur->kode_kecamatan)->orderBy('name')->get() as $kel)
                                                <option value="{{ $kel->id }}" data-name="{{ $kel->name }}" {{ $debitur->kode_kelurahan == $kel->id ? 'selected' : '' }}>{{ ucwords(strtolower($kel->name)) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <input type="hidden" name="kelurahan_ktp" id="input-kelurahan-name" value="{{ $debitur->kelurahan_ktp }}">
                                </div>

                                <div class="col-lg-4">
                                    <label class="form-label">Kode Pos</label>
                                    <input type="text" name="kode_pos_ktp" class="form-control" placeholder="00000" value="{{ $debitur->kode_pos_ktp }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD: Data Pekerjaan -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Data Pekerjaan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Pekerjaan</label>
                                    <select class="form-select" name="pekerjaan">
                                        <option value="">Pilih</option>
                                        <option value="Karyawan swasta" {{ $debitur->pekerjaan == 'Karyawan swasta' ? 'selected' : '' }}>Karyawan swasta</option>
                                        <option value="Wiraswasta" {{ $debitur->pekerjaan == 'Wiraswasta' ? 'selected' : '' }}>Wiraswasta</option>
                                        <option value="PNS" {{ $debitur->pekerjaan == 'PNS' ? 'selected' : '' }}>PNS</option>
                                        <option value="Lainnya" {{ $debitur->pekerjaan == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Jenis Pekerjaan</label>
                                    <select class="form-select" name="jenis_pekerjaan">
                                        <option value="">Pilih</option>
                                        <option value="Offline" {{ $debitur->jenis_pekerjaan == 'Offline' ? 'selected' : '' }}>Offline</option>
                                        <option value="Online" {{ $debitur->jenis_pekerjaan == 'Online' ? 'selected' : '' }}>Online</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Bidang Pekerjaan</label>
                                    <select class="form-select" name="bidang_pekerjaan">
                                        <option value="">Pilih</option>
                                        <option value="Industri Pengolahan" {{ $debitur->bidang_pekerjaan == 'Industri Pengolahan' ? 'selected' : '' }}>Industri Pengolahan</option>
                                        <option value="Manufaktur" {{ $debitur->bidang_pekerjaan == 'Manufaktur' ? 'selected' : '' }}>Manufaktur</option>
                                        <option value="Lainnya" {{ $debitur->bidang_pekerjaan == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Pengalaman Kerja (Tahun)</label>
                                    <input type="number" name="pengalaman_kerjatahun" class="form-control" placeholder="0" value="{{ $debitur->pengalaman_kerjatahun }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                                    <select id="select-perusahaan" name="nama_perusahaan" class="form-select @error('nama_perusahaan') is-invalid @enderror" required>
                                        <option value="">-- Pilih Perusahaan --</option>
                                        @foreach($perusahaans as $p)
                                            <option value="{{ $p->nama_perusahaan }}"
                                                data-id="{{ $p->id }}"
                                                data-alamat="{{ $p->alamat_perusahaan }}"
                                                {{ ($debitur->nama_perusahaan ?? $debitur->perusahaan) == $p->nama_perusahaan ? 'selected' : '' }}>
                                                {{ $p->nama_perusahaan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="perusahaan_id" id="input-perusahaan-id" value="{{ $debitur->perusahaan_id ?? $debitur->perusahaan }}">
                                    @error('nama_perusahaan')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Jabatan/Posisi</label>
                                    <input type="text" name="jabatan_posisi" class="form-control" value="{{ $debitur->jabatan_posisi }}">
                                </div>
                                <div class="col-lg-12">
                                    <label class="form-label">Alamat Perusahaan</label>
                                    <textarea id="textarea-alamat-perusahaan" name="alamat_perusahaan" class="form-control" rows="2">{{ $debitur->alamat_perusahaan }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD: Data Rekening -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Data Rekening</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Nama Bank <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_bank" class="form-control @error('nama_bank') is-invalid @enderror" value="{{ $debitur->nama_bank }}" required placeholder="BCA/Mandiri/dsb">
                                    @error('nama_bank')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Nama Pemilik Rekening</label>
                                    <input type="text" name="nama_pemilik_rekening" class="form-control" value="{{ $debitur->nama_pemilik_rekening }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Nomor Rekening</label>
                                    <input type="text" name="nomer_rekening" class="form-control @error('nomer_rekening') is-invalid @enderror" value="{{ $debitur->nomer_rekening }}">
                                    @error('nomer_rekening')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD: Informasi Peminjaman & Domisili -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Informasi Peminjaman & Domisili</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
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
                                    <label class="form-label">Provinsi Domisili <span class="text-danger">*</span></label>
                                    <select id="select-provinsi-domisili" name="kode_provinsi_domisili" class="form-select" required>
                                        <option value="">-- Pilih Provinsi --</option>
                                        @foreach(\App\Models\Province::orderBy('name')->get() as $prov)
                                            <option value="{{ $prov->id }}" data-name="{{ $prov->name }}" {{ $debitur->kode_provinsi_domisili == $prov->id ? 'selected' : '' }}>{{ ucwords(strtolower($prov->name)) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="provinsi_domisili" id="input-provinsi-domisili-name" value="{{ $debitur->provinsi_domisili }}">
                                </div>

                                <div class="col-lg-4">
                                    <label class="form-label">Kota / Kabupaten Domisili <span class="text-danger">*</span></label>
                                    <select id="select-kabupaten-domisili" name="kode_kabupaten_domisili" class="form-select" required>
                                        <option value="">-- Pilih Kota/Kabupaten --</option>
                                        @if($debitur->kode_provinsi_domisili)
                                            @foreach(\App\Models\Regency::where('province_id', $debitur->kode_provinsi_domisili)->orderBy('name')->get() as $kab)
                                                <option value="{{ $kab->id }}" data-name="{{ $kab->name }}" {{ $debitur->kode_kabupaten_domisili == $kab->id ? 'selected' : '' }}>{{ ucwords(strtolower($kab->name)) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <input type="hidden" name="kota_kabupaten_domisili" id="input-kabupaten-domisili-name" value="{{ $debitur->kota_kabupaten_domisili }}">
                                </div>

                                <div class="col-lg-4">
                                    <label class="form-label">Kecamatan Domisili <span class="text-danger">*</span></label>
                                    <select id="select-kecamatan-domisili" name="kode_kecamatan_domisili" class="form-select" required>
                                        <option value="">-- Pilih Kecamatan --</option>
                                        @if($debitur->kode_kabupaten_domisili)
                                            @foreach(\App\Models\District::where('regency_id', $debitur->kode_kabupaten_domisili)->orderBy('name')->get() as $kec)
                                                <option value="{{ $kec->id }}" data-name="{{ $kec->name }}" {{ $debitur->kode_kecamatan_domisili == $kec->id ? 'selected' : '' }}>{{ ucwords(strtolower($kec->name)) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <input type="hidden" name="kecamatan_domisili" id="input-kecamatan-domisili-name" value="{{ $debitur->kecamatan_domisili }}">
                                </div>

                                <div class="col-lg-4">
                                    <label class="form-label">Kelurahan / Desa Domisili <span class="text-danger">*</span></label>
                                    <select id="select-kelurahan-domisili" name="kode_kelurahan_domisili" class="form-select" required>
                                        <option value="">-- Pilih Kelurahan/Desa --</option>
                                        @if($debitur->kode_kecamatan_domisili)
                                            @foreach(\App\Models\Village::where('district_id', $debitur->kode_kecamatan_domisili)->orderBy('name')->get() as $kel)
                                                <option value="{{ $kel->id }}" data-name="{{ $kel->name }}" {{ $debitur->kode_kelurahan_domisili == $kel->id ? 'selected' : '' }}>{{ ucwords(strtolower($kel->name)) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <input type="hidden" name="kelurahan_domisili" id="input-kelurahan-domisili-name" value="{{ $debitur->kelurahan_domisili }}">
                                </div>

                                <div class="col-lg-4">
                                    <label class="form-label">Kode Pos Domisili</label>
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

                <!-- CARD: Kontak Darurat / Keluarga -->
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
                                <div class="col-lg-4">
                                    <label class="form-label">Provinsi KTP Keluarga</label>
                                    <select id="select-provinsi-ktp-keluarga" name="kode_provinsi_ktp_keluarga" class="form-select">
                                        <option value="">-- Pilih Provinsi --</option>
                                        @foreach(\App\Models\Province::orderBy('name')->get() as $prov)
                                            <option value="{{ $prov->id }}" data-name="{{ $prov->name }}" {{ $debitur->kode_provinsi_ktp_keluarga == $prov->id ? 'selected' : '' }}>{{ ucwords(strtolower($prov->name)) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="provinsi_ktp_keluarga" id="input-provinsi-ktp-keluarga-name" value="{{ $debitur->provinsi_ktp_keluarga }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Kota/Kabupaten KTP Keluarga</label>
                                    <select id="select-kabupaten-ktp-keluarga" name="kode_kabupaten_ktp_keluarga" class="form-select">
                                        <option value="">-- Pilih Kota/Kabupaten --</option>
                                        @if($debitur->kode_provinsi_ktp_keluarga)
                                            @foreach(\App\Models\Regency::where('province_id', $debitur->kode_provinsi_ktp_keluarga)->orderBy('name')->get() as $kab)
                                                <option value="{{ $kab->id }}" data-name="{{ $kab->name }}" {{ $debitur->kode_kabupaten_ktp_keluarga == $kab->id ? 'selected' : '' }}>{{ ucwords(strtolower($kab->name)) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <input type="hidden" name="kota_kabupaten_ktp_keluarga" id="input-kabupaten-ktp-keluarga-name" value="{{ $debitur->kota_kabupaten_ktp_keluarga }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Kecamatan KTP Keluarga</label>
                                    <select id="select-kecamatan-ktp-keluarga" name="kode_kecamatan_ktp_keluarga" class="form-select">
                                        <option value="">-- Pilih Kecamatan --</option>
                                        @if($debitur->kode_kabupaten_ktp_keluarga)
                                            @foreach(\App\Models\District::where('regency_id', $debitur->kode_kabupaten_ktp_keluarga)->orderBy('name')->get() as $kec)
                                                <option value="{{ $kec->id }}" data-name="{{ $kec->name }}" {{ $debitur->kode_kecamatan_ktp_keluarga == $kec->id ? 'selected' : '' }}>{{ ucwords(strtolower($kec->name)) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <input type="hidden" name="kecamatan_ktp_keluarga" id="input-kecamatan-ktp-keluarga-name" value="{{ $debitur->kecamatan_ktp_keluarga }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Kelurahan / Desa KTP Keluarga</label>
                                    <select id="select-kelurahan-ktp-keluarga" name="kode_kelurahan_ktp_keluarga" class="form-select">
                                        <option value="">-- Pilih Kelurahan/Desa --</option>
                                        @if($debitur->kode_kecamatan_ktp_keluarga)
                                            @foreach(\App\Models\Village::where('district_id', $debitur->kode_kecamatan_ktp_keluarga)->orderBy('name')->get() as $kel)
                                                <option value="{{ $kel->id }}" data-name="{{ $kel->name }}" {{ $debitur->kode_kelurahan_ktp_keluarga == $kel->id ? 'selected' : '' }}>{{ ucwords(strtolower($kel->name)) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <input type="hidden" name="kelurahan_ktp_keluarga" id="input-kelurahan-ktp-keluarga-name" value="{{ $debitur->kelurahan_ktp_keluarga }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Kode Pos KTP Keluarga</label>
                                    <input type="text" name="kode_pos_ktp_keluarga" class="form-control" value="{{ $debitur->kode_pos_ktp_keluarga }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD: Keuangan & Pekerjaan Tambahan -->
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
                                    <label class="form-label">Gaji / Penghasilan Per Bulan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="pendapatan_per_bulan" class="form-control" value="{{ $debitur->pendapatan_per_bulan }}" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Pengeluaran Per Bulan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="pengeluaran_per_bulan" class="form-control" value="{{ $debitur->pengeluaran_per_bulan }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Hutang Per Bulan (Cicilan Lain)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="hutang_per_bulan" class="form-control" value="{{ $debitur->hutang_per_bulan }}">
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
                                        <input type="number" name="pendapatan_lain_rupiah" class="form-control" value="{{ $debitur->pendapatan_lain_rupiah }}">
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <label class="form-label">Alamat Perusahaan Lain</label>
                                    <textarea name="alamat_perusahaan_lain" class="form-control" rows="1">{{ $debitur->alamat_perusahaan_lain }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD: Upload Dokumen -->
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">
                                <i class="ri-file-upload-line me-2 text-primary"></i>Upload Dokumen (Ganti file jika perlu)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                @php
                                    $docs = [
                                        'foto_ktp'     => ['label' => 'Foto KTP Debitur',      'icon' => 'ri-id-card-line',      'color' => 'primary'],
                                        'ktp_pasangan' => ['label' => 'KTP Pasangan Debitur',  'icon' => 'ri-contacts-line',     'color' => 'success'],
                                        'foto_bpjs'    => ['label' => 'Foto Kartu BPJS',       'icon' => 'ri-health-book-line',  'color' => 'warning'],
                                        'selfie_ktp'   => ['label' => 'Selfie dengan KTP',     'icon' => 'ri-camera-line',       'color' => 'danger'],
                                        'kartu_keluarga' => ['label' => 'Kartu Keluarga (KK)', 'icon' => 'ri-file-list-line',    'color' => 'info'],
                                        'privy_id_file' => ['label' => 'Privy ID',             'icon' => 'ri-shield-user-line',  'color' => 'primary'],
                                        'id_card'      => ['label' => 'ID Card',               'icon' => 'ri-id-card-line',      'color' => 'secondary'],
                                        'slip_gaji'    => ['label' => 'Slip Gaji',             'icon' => 'ri-money-dollar-circle-line', 'color' => 'success'],
                                        'buku_nikah'   => ['label' => 'Buku Nikah',            'icon' => 'ri-heart-line',        'color' => 'danger'],
                                        'mutasi_bank'  => ['label' => 'Mutasi Bank',           'icon' => 'ri-bank-line',         'color' => 'info'],
                                        'dokumen_pendukung' => ['label' => 'Dokumen Pendukung','icon' => 'ri-file-text-line',    'color' => 'warning', 'multiple' => true],
                                        'saldo_jmo'    => ['label' => 'Saldo JMO',            'icon' => 'ri-wallet-line',       'color' => 'secondary'],
                                    ];
                                @endphp

                                @foreach($docs as $collection => $info)
                                    @php 
                                        $isMultipleCollection = in_array($collection, ['mutasi_bank', 'dokumen_pendukung']);
                                        $medias = $isMultipleCollection
                                            ? $debitur->getMedia($collection)
                                            : collect([$debitur->getFirstMedia($collection)])->filter();
                                        $media = $medias->first();
                                        $hasMedia = $medias->isNotEmpty();
                                        $isImage = $media && str_contains($media->mime_type, 'image');
                                        $fileUrl = $media ? parse_url($media->getUrl(), PHP_URL_PATH) : '';
                                        $accept = $collection === 'selfie_ktp' ? 'image/*' : 'image/*,application/pdf';
                                        $multiple = $isMultipleCollection ? 'multiple' : '';
                                        $listId = $isMultipleCollection ? str_replace('_', '-', $collection) . '-preview-list-edit' : '';
                                    @endphp
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label fw-semibold">{{ $info['label'] }}</label>
                                        <div class="upload-area {{ $hasMedia ? 'has-file' : '' }}" id="area-{{ str_replace('_', '-', $collection) }}" onclick="document.getElementById('file-{{ str_replace('_', '-', $collection) }}').click()">
                                            <div class="upload-placeholder" id="ph-{{ str_replace('_', '-', $collection) }}" style="{{ ($isImage && $collection !== 'mutasi_bank') ? 'opacity: 0;' : '' }}">
                                                <i class="{{ $info['icon'] }} fs-1 text-{{ $info['color'] }} opacity-75"></i>
                                                <p class="mb-0 mt-2 text-muted small">Klik untuk ubah file</p>
                                                @if($media && !$isImage && $collection !== 'mutasi_bank')
                                                <p class="mb-0 text-{{ $info['color'] }} small text-truncate px-2">{{ $media->file_name }}</p>
                                                @endif
                                            </div>
                                            <img id="prev-{{ str_replace('_', '-', $collection) }}" src="{{ ($isImage && $collection !== 'mutasi_bank') ? $fileUrl : '' }}" class="upload-preview {{ ($isImage && $collection !== 'mutasi_bank') ? '' : 'd-none' }}" alt="Preview">
                                        </div>
                                        <input type="file" id="file-{{ str_replace('_', '-', $collection) }}" name="{{ $collection }}{{ $multiple ? '[]' : '' }}" class="d-none" accept="{{ $accept }}" {{ $multiple }} onchange="handleFilePreview(this,'prev-{{ str_replace('_', '-', $collection) }}','ph-{{ str_replace('_', '-', $collection) }}','name-{{ str_replace('_', '-', $collection) }}','{{ $listId }}')">
                                        <div class="mt-2 d-flex justify-content-between align-items-center">
                                            <small id="name-{{ str_replace('_', '-', $collection) }}" class="text-muted text-truncate" style="max-width: 65%;">{{ $collection === 'mutasi_bank' && $hasMedia ? $medias->count() . ' file tersimpan' : '' }}</small>
                                            @if($media && $collection !== 'mutasi_bank')
                                            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-soft-{{ $info['color'] }}">
                                                <i class="ri-eye-line"></i> Dokumen Asli
                                            </a>
                                            @endif
                                        </div>
                                        @if($isMultipleCollection)
                                            <div id="{{ str_replace('_', '-', $collection) }}-preview-list-edit" class="multi-file-preview mt-2 {{ $hasMedia ? '' : 'd-none' }}">
                                                @foreach($medias as $mediaItem)
                                                    @php
                                                        $itemIsImage = str_contains($mediaItem->mime_type, 'image');
                                                        $itemUrl = parse_url($mediaItem->getUrl(), PHP_URL_PATH);
                                                    @endphp
                                                    <div class="multi-file-item">
                                                        @if($itemIsImage)
                                                            <img class="multi-file-thumb" src="{{ $itemUrl }}" alt="{{ $mediaItem->file_name }}">
                                                        @else
                                                            <div class="multi-file-pdf">PDF</div>
                                                        @endif
                                                        <div class="multi-file-name" title="{{ $mediaItem->file_name }}">{{ $mediaItem->file_name }}</div>
                                                        <a href="{{ $itemUrl }}" target="_blank" class="btn btn-sm btn-soft-info w-100 mt-1">
                                                            <i class="ri-eye-line"></i> Lihat
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD: Activities -->
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

                <!-- Submit Buttons -->
                <div class="col-lg-12">
                    <div class="text-end mb-4">
                        <a href="{{ route('debitur.index') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" id="submit-btn" class="btn btn-primary">
                            <span id="btn-text">Update Data (Kirim Kembali)</span>
                            <span id="btn-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('styles')
<style>
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

    function syncInputFiles(input, files) {
        if (typeof DataTransfer === 'undefined') return;
        const dt = new DataTransfer();
        files.forEach(f => dt.items.add(f));
        input.files = dt.files;
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

        if (!file) return;

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

    if (selPerusahaan) {
        selPerusahaan.addEventListener('change', function () {
            const opt   = this.options[this.selectedIndex];
            const id    = opt?.dataset?.id    ?? '';
            const alamat= opt?.dataset?.alamat ?? '';

            if (inpPerusahaanId) inpPerusahaanId.value = id;
            if (txtAlamatPerusahaan) txtAlamatPerusahaan.value = alamat;
        });
    }

    // ── Helper: Load cascading data ────────────────────────────────────────────
    function toTitleCase(str) {
        return str.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
    }

    function setupCascade(parentId, childId, hiddenId, childPlaceholder, url) {
        const parent = document.getElementById(parentId);
        const child = document.getElementById(childId);
        const hidden = document.getElementById(hiddenId);

        if (!parent || !child) return;

        let isLoading = false;

        parent.addEventListener('change', function () {
            const val = this.value;
            
            // Update hidden field when parent changes
            if (hidden) {
                hidden.value = this.options[this.selectedIndex]?.dataset?.name ?? '';
            }

            // If no value selected, reset child
            if (!val) {
                child.innerHTML = `<option value="">${childPlaceholder}</option>`;
                child.disabled = true;
                if (hidden) hidden.value = '';
                return;
            }

            // Prevent multiple concurrent requests
            if (isLoading) return;
            
            // Save current child value before overwriting
            const savedChildValue = child.value;

            isLoading = true;
            child.disabled = true;
            child.innerHTML = '<option value="">Memuat...</option>';

            // Add timeout to prevent infinite loading
            const timeoutId = setTimeout(() => {
                console.warn(`Cascade load timeout for ${parentId} -> ${childId}`);
                child.innerHTML = `<option value="">Timeout - Refresh halaman</option>`;
                child.disabled = true;
                isLoading = false;
            }, 5000);

            fetch(url(val), { signal: AbortSignal.timeout(5000) })
                .then(r => {
                    if (!r.ok) throw new Error(`HTTP ${r.status}`);
                    return r.json();
                })
                .then(data => {
                    clearTimeout(timeoutId);
                    if (!Array.isArray(data)) throw new Error('Invalid data format');
                    
                    child.innerHTML = `<option value="">${childPlaceholder}</option>`;
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.dataset.name = item.name;
                        opt.textContent = toTitleCase(item.name);
                        // Re-select previously saved value
                        if (String(item.id) === String(savedChildValue)) {
                            opt.selected = true;
                        }
                        child.appendChild(opt);
                    });
                    child.disabled = false;
                    isLoading = false;
                    
                    // Update hidden with re-selected value
                    if (hidden && child.value) {
                        hidden.value = child.options[child.selectedIndex]?.dataset?.name ?? '';
                    }
                })
                .catch(err => {
                    clearTimeout(timeoutId);
                    console.error(`Cascade error [${parentId}->${childId}]:`, err.message);
                    child.innerHTML = `<option value="">Error - Coba pilih lagi</option>`;
                    child.disabled = true;
                    isLoading = false;
                });
        });

        child.addEventListener('change', function () {
            if (hidden) {
                hidden.value = this.options[this.selectedIndex]?.dataset?.name ?? '';
            }
        });
    }

    // ── Setup cascading: KTP ──────────────────────────────────────────────────
    setupCascade('select-provinsi', 'select-kabupaten', 'input-kabupaten-name', 
        '-- Pilih Kota/Kabupaten --', 
        id => `/apps-ecommerce/wilayah/kabupaten/${id}`);

    setupCascade('select-kabupaten', 'select-kecamatan', 'input-kecamatan-name',
        '-- Pilih Kecamatan --',
        id => `/apps-ecommerce/wilayah/kecamatan/${id}`);

    setupCascade('select-kecamatan', 'select-kelurahan', 'input-kelurahan-name',
        '-- Pilih Kelurahan/Desa --',
        id => `/apps-ecommerce/wilayah/kelurahan/${id}`);

    // Set initial provinsi name
    const selProvinsi = document.getElementById('select-provinsi');
    if (selProvinsi) {
        const inpProvinsiName = document.getElementById('input-provinsi-name');
        selProvinsi.addEventListener('change', function () {
            inpProvinsiName.value = this.options[this.selectedIndex]?.dataset?.name ?? '';
        });
    }

    // ── Setup cascading: DOMISILI ─────────────────────────────────────────────
    setupCascade('select-provinsi-domisili', 'select-kabupaten-domisili', 'input-kabupaten-domisili-name',
        '-- Pilih Kota/Kabupaten --',
        id => `/apps-ecommerce/wilayah/kabupaten/${id}`);

    setupCascade('select-kabupaten-domisili', 'select-kecamatan-domisili', 'input-kecamatan-domisili-name',
        '-- Pilih Kecamatan --',
        id => `/apps-ecommerce/wilayah/kecamatan/${id}`);

    setupCascade('select-kecamatan-domisili', 'select-kelurahan-domisili', 'input-kelurahan-domisili-name',
        '-- Pilih Kelurahan/Desa --',
        id => `/apps-ecommerce/wilayah/kelurahan/${id}`);

    const selProvinsiDomisili = document.getElementById('select-provinsi-domisili');
    if (selProvinsiDomisili) {
        const inpProvinsiDomisiliName = document.getElementById('input-provinsi-domisili-name');
        selProvinsiDomisili.addEventListener('change', function () {
            inpProvinsiDomisiliName.value = this.options[this.selectedIndex]?.dataset?.name ?? '';
        });
    }

    // ── Setup cascading: KTP KELUARGA ──────────────────────────────────────────
    setupCascade('select-provinsi-ktp-keluarga', 'select-kabupaten-ktp-keluarga', 'input-kabupaten-ktp-keluarga-name',
        '-- Pilih Kota/Kabupaten --',
        id => `/apps-ecommerce/wilayah/kabupaten/${id}`);

    setupCascade('select-kabupaten-ktp-keluarga', 'select-kecamatan-ktp-keluarga', 'input-kecamatan-ktp-keluarga-name',
        '-- Pilih Kecamatan --',
        id => `/apps-ecommerce/wilayah/kecamatan/${id}`);

    setupCascade('select-kecamatan-ktp-keluarga', 'select-kelurahan-ktp-keluarga', 'input-kelurahan-ktp-keluarga-name',
        '-- Pilih Kelurahan/Desa --',
        id => `/apps-ecommerce/wilayah/kelurahan/${id}`);

    const selProvinsiKtpKeluarga = document.getElementById('select-provinsi-ktp-keluarga');
    if (selProvinsiKtpKeluarga) {
        const inpProvinsiKtpKeluargaName = document.getElementById('input-provinsi-ktp-keluarga-name');
        selProvinsiKtpKeluarga.addEventListener('change', function () {
            inpProvinsiKtpKeluargaName.value = this.options[this.selectedIndex]?.dataset?.name ?? '';
        });
    }

    // ── Status Perkawinan toggle spouse fields ───────────────────────────────
    const selStatusPerkawinan = document.getElementById('select-status-perkawinan');
    const spouseSection = document.getElementById('spouse-section');
    const spouseRequiredInputs = [
        document.getElementById('input-nama-pasangan'),
        document.getElementById('input-nik-pasangan'),
        document.getElementById('input-hp-pasangan')
    ];

    function toggleSpouseFields() {
        const married = selStatusPerkawinan.value === 'Menikah';
        spouseSection.classList.toggle('d-none', !married);

        spouseRequiredInputs.forEach(element => {
            if (!element) return;
            element.required = married;
        });
    }

    if (selStatusPerkawinan) {
        selStatusPerkawinan.addEventListener('change', toggleSpouseFields);
        toggleSpouseFields(); // Init on page load
    }

    // ── Loading indicator on form submit ───────────────────────────────────────
    const form = document.getElementById('create-debitur-form');
    const submitBtn = document.getElementById('submit-btn');
    const btnText = document.getElementById('btn-text');
    const btnSpinner = document.getElementById('btn-spinner');

    if (form) {
        form.addEventListener('submit', function () {
            if (submitBtn) {
                submitBtn.disabled = true;
                if (btnText) btnText.classList.add('d-none');
                if (btnSpinner) btnSpinner.classList.remove('d-none');
            }
        });
    }

    // ── No need to trigger cascading on page load ─────────────────────────────
    // Data is already pre-populated server-side by Blade.
    // Cascading only triggers when user CHANGES the parent dropdown.
})();
</script>
@endsection
