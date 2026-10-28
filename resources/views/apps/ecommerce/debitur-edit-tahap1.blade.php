@extends('layouts.master')
@section('title', 'Review Debitur | NSB')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Review / Action Debitur - Tahap 1 (SPV)</h4>
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

        <form id="create-debitur-form" action="{{ route('debitur.action', $debitur->id) }}" method="POST">
            @csrf

            <div class="row">
                {{-- ===== INFORMASI PRIBADI ===== --}}
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Informasi Pribadi</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div>
                                        <label class="form-label">Nama Peminjam <span class="text-danger">*</span></label>
                                        <input readonly type="text" name="nama_peminjam" class="form-control" required placeholder="Masukkan nama peminjam" value="{{ $debitur->nama_peminjam }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div>
                                        <label class="form-label">Nomor KTP <span class="text-danger">*</span></label>
                                        <input readonly type="text" name="nomor_ktp" class="form-control" required placeholder="16 Digit NIK" value="{{ $debitur->nomor_ktp }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div>
                                        <label class="form-label">Email</label>
                                        <input readonly type="email" name="email" class="form-control" placeholder="Email aktif" value="{{ $debitur->email }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div>
                                        <label class="form-label">Nomor Handphone <span class="text-danger">*</span></label>
                                        <input readonly type="text" name="nomor_handphone" class="form-control" required placeholder="Contoh: 08123456789" value="{{ $debitur->nomor_handphone }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div>
                                        <label class="form-label">Jenis Kelamin</label>
                                        <select disabled class="form-select" name="jenis_kelamin">
                                            <option value="">Pilih...</option>
                                            <option value="Laki-laki" {{ $debitur->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="Perempuan" {{ $debitur->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <label class="form-label">Nama Pasangan</label>
                                            <input readonly type="text" name="nama_pasangan" class="form-control" value="{{ $debitur->nama_pasangan }}">
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="form-label">NIK Pasangan</label>
                                            <input readonly type="text" name="nik_pasangan" class="form-control" value="{{ $debitur->nik_pasangan }}">
                                        </div>
                                        <div class="col-lg-4">
                                            <label class="form-label">No Handphone Pasangan</label>
                                            <input readonly type="text" name="nomor_hp_pasangan" class="form-control" value="{{ $debitur->nomor_hp_pasangan }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div>
                                        <label class="form-label">Tempat Lahir</label>
                                        <input readonly type="text" name="tempat_lahir" class="form-control" placeholder="Kota lahir" value="{{ $debitur->tempat_lahir }}">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div>
                                        <label class="form-label">Tanggal Lahir</label>
                                        <input readonly type="date" name="tanggal_lahir" class="form-control" value="{{ $debitur->tanggal_lahir }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div>
                                        <label class="form-label">Nama Ibu Kandung</label>
                                        <input readonly type="text" name="nama_ibu_kandung" class="form-control" placeholder="Nama ibu" value="{{ $debitur->nama_ibu_kandung }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label">Agama</label>
                                            <select disabled class="form-select" name="agama">
                                                <option value="">Pilih...</option>
                                                @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $ag)
                                                    <option value="{{ $ag }}" {{ $debitur->agama == $ag ? 'selected' : '' }}>{{ $ag }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Pendidikan</label>
                                            <select disabled class="form-select" name="pendidikan">
                                                <option value="">Pilih...</option>
                                                @foreach(['SD','SMP','SMA','D3','S1','S2'] as $pd)
                                                    <option value="{{ $pd }}" {{ $debitur->pendidikan == $pd ? 'selected' : '' }}>{{ $pd }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Status Perkawinan</label>
                                    <select disabled class="form-select" name="status_perkawinan">
                                        <option value="">Pilih...</option>
                                        @foreach(['Belum Menikah','Menikah','Cerai'] as $sp)
                                            <option value="{{ $sp }}" {{ $debitur->status_perkawinan == $sp ? 'selected' : '' }}>{{ $sp }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label">Privy ID</label>
                                            <input readonly type="text" name="privy_id" class="form-control" placeholder="Privy ID" value="{{ $debitur->privy_id }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Nomor NPWP</label>
                                            <input readonly type="text" name="nomor_npwp" class="form-control" placeholder="Nomor NPWP" value="{{ $debitur->nomor_npwp }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-6">
                                            <label class="form-label">Pengajuan Plafond</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input readonly type="number" name="pengajuan_plafond" class="form-control" value="{{ $debitur->pengajuan_plafond }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">Tenor (Bulan)</label>
                                            <input readonly type="text" name="tenor_bulan" class="form-control" value="{{ $debitur->tenor_bulan }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end col -->

                {{-- ===== INFORMASI ALAMAT ===== --}}
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Informasi Alamat (Sesuai KTP)</h5>
                        </div>
                        <div class="card-body">
                            @php
                                // Resolve names from code if name columns are empty
                                $namaProvinsi  = $debitur->provinsi_ktp
                                    ?: optional(\App\Models\Province::find($debitur->kode_provinsi))->name;

                                $namaKabupaten = $debitur->kota_kabupaten_ktp
                                    ?: optional(\App\Models\Regency::find($debitur->kode_kabupaten))->name;

                                $namaKecamatan = $debitur->kecamatan_ktp
                                    ?: optional(\App\Models\District::find($debitur->kode_kecamatan))->name;

                                $namaKelurahan = $debitur->kelurahan_ktp
                                    ?: optional(\App\Models\Village::find($debitur->kode_kelurahan))->name;
                            @endphp
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <div>
                                        <label class="form-label">Alamat KTP Lengkap <span class="text-danger">*</span></label>
                                        <textarea readonly class="form-control" name="alamat_ktp" rows="3" required placeholder="Nama Jalan, Gedung, No. Rumah">{{ $debitur->alamat_ktp }}</textarea>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <label class="form-label">RT</label>
                                    <input readonly type="text" name="rt_ktp" class="form-control" placeholder="000" value="{{ $debitur->rt_ktp }}">
                                </div>
                                <div class="col-lg-3 col-6">
                                    <label class="form-label">RW</label>
                                    <input readonly type="text" name="rw_ktp" class="form-control" placeholder="000" value="{{ $debitur->rw_ktp }}">
                                </div>

                                {{-- Provinsi --}}
                                <div class="col-lg-6">
                                    <label class="form-label">Provinsi</label>
                                    <input readonly type="text" class="form-control fw-semibold"
                                           value="{{ $namaProvinsi ? ucwords(strtolower($namaProvinsi)) : '-' }}">
                                </div>

                                {{-- Kota/Kabupaten --}}
                                <div class="col-lg-6">
                                    <label class="form-label">Kota / Kabupaten</label>
                                    <input readonly type="text" class="form-control fw-semibold"
                                           value="{{ $namaKabupaten ? ucwords(strtolower($namaKabupaten)) : '-' }}">
                                </div>

                                {{-- Kecamatan --}}
                                <div class="col-lg-6">
                                    <label class="form-label">Kecamatan</label>
                                    <input readonly type="text" class="form-control fw-semibold"
                                           value="{{ $namaKecamatan ? ucwords(strtolower($namaKecamatan)) : '-' }}">
                                </div>

                                {{-- Kelurahan/Desa --}}
                                <div class="col-lg-6">
                                    <label class="form-label">Kelurahan / Desa</label>
                                    <input readonly type="text" class="form-control fw-semibold"
                                           value="{{ $namaKelurahan ? ucwords(strtolower($namaKelurahan)) : '-' }}">
                                </div>

                                <div class="col-lg-4">
                                    <label class="form-label">Kode Pos</label>
                                    <input readonly type="text" name="kode_pos_ktp" class="form-control" value="{{ $debitur->kode_pos_ktp }}" placeholder="00000">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== INFORMASI ALAMAT DOMISILI ===== --}}
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Informasi Alamat Domisili</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $namaProvinsiDomisili  = $debitur->provinsi_domisili ?: optional(\App\Models\Province::find($debitur->kode_provinsi_domisili))->name;
                                $namaKabupatenDomisili = $debitur->kota_kabupaten_domisili ?: optional(\App\Models\Regency::find($debitur->kode_kabupaten_domisili))->name;
                                $namaKecamatanDomisili = $debitur->kecamatan_domisili ?: optional(\App\Models\District::find($debitur->kode_kecamatan_domisili))->name;
                                $namaKelurahanDomisili = $debitur->kelurahan_domisili ?: optional(\App\Models\Village::find($debitur->kode_kelurahan_domisili))->name;
                            @endphp
                            <div class="row g-3">
                                <div class="col-lg-12">
                                    <label class="form-label">Alamat Domisili Lengkap</label>
                                    <textarea readonly class="form-control" rows="3">{{ $debitur->alamat_domisili }}</textarea>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <label class="form-label">RT</label>
                                    <input readonly type="text" class="form-control" value="{{ $debitur->rt_domisili }}">
                                </div>
                                <div class="col-lg-3 col-6">
                                    <label class="form-label">RW</label>
                                    <input readonly type="text" class="form-control" value="{{ $debitur->rw_domisili }}">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Provinsi</label>
                                    <input readonly type="text" class="form-control fw-semibold" value="{{ $namaProvinsiDomisili ? ucwords(strtolower($namaProvinsiDomisili)) : '-' }}">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Kota / Kabupaten</label>
                                    <input readonly type="text" class="form-control fw-semibold" value="{{ $namaKabupatenDomisili ? ucwords(strtolower($namaKabupatenDomisili)) : '-' }}">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Kecamatan</label>
                                    <input readonly type="text" class="form-control fw-semibold" value="{{ $namaKecamatanDomisili ? ucwords(strtolower($namaKecamatanDomisili)) : '-' }}">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Kelurahan / Desa</label>
                                    <input readonly type="text" class="form-control fw-semibold" value="{{ $namaKelurahanDomisili ? ucwords(strtolower($namaKelurahanDomisili)) : '-' }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Kode Pos</label>
                                    <input readonly type="text" class="form-control" value="{{ $debitur->kode_pos_domisili }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Status Kepemilikan Rumah</label>
                                    <input readonly type="text" class="form-control" value="{{ $debitur->status_kepemilikan_rumah }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== KELUARGA DARURAT ===== --}}
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Keluarga Tidak Serumah (Darurat)</h5>
                        </div>
                        <div class="card-body">
                            @php
                                $namaProvinsiKeluarga  = $debitur->provinsi_ktp_keluarga ?: optional(\App\Models\Province::find($debitur->kode_provinsi_ktp_keluarga))->name;
                                $namaKabupatenKeluarga = $debitur->kota_kabupaten_ktp_keluarga ?: optional(\App\Models\Regency::find($debitur->kode_kabupaten_ktp_keluarga))->name;
                                $namaKecamatanKeluarga = $debitur->kecamatan_ktp_keluarga ?: optional(\App\Models\District::find($debitur->kode_kecamatan_ktp_keluarga))->name;
                                $namaKelurahanKeluarga = $debitur->kelurahan_ktp_keluarga ?: optional(\App\Models\Village::find($debitur->kode_kelurahan_ktp_keluarga))->name;
                            @endphp
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input readonly type="text" class="form-control" value="{{ $debitur->nama_keluarga_tidak_serumah }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Hubungan Keluarga</label>
                                    <input readonly type="text" class="form-control" value="{{ $debitur->hubungan_keluarga }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">No Handphone</label>
                                    <input readonly type="text" class="form-control" value="{{ $debitur->no_handphone_keluarga }}">
                                </div>
                                <div class="col-lg-12">
                                    <label class="form-label">Alamat Lengkap</label>
                                    <textarea readonly class="form-control" rows="2">{{ $debitur->alamat_ktp_keluarga }}</textarea>
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Provinsi</label>
                                    <input readonly type="text" class="form-control fw-semibold" value="{{ $namaProvinsiKeluarga ? ucwords(strtolower($namaProvinsiKeluarga)) : '-' }}">
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Kota / Kabupaten</label>
                                    <input readonly type="text" class="form-control fw-semibold" value="{{ $namaKabupatenKeluarga ? ucwords(strtolower($namaKabupatenKeluarga)) : '-' }}">
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Kecamatan</label>
                                    <input readonly type="text" class="form-control fw-semibold" value="{{ $namaKecamatanKeluarga ? ucwords(strtolower($namaKecamatanKeluarga)) : '-' }}">
                                </div>
                                <div class="col-lg-3">
                                    <label class="form-label">Kelurahan / Desa</label>
                                    <input readonly type="text" class="form-control fw-semibold" value="{{ $namaKelurahanKeluarga ? ucwords(strtolower($namaKelurahanKeluarga)) : '-' }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Kode Pos</label>
                                    <input readonly type="text" class="form-control" value="{{ $debitur->kode_pos_ktp_keluarga }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== PEKERJAAN & REKENING ===== --}}
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Pekerjaan &amp; Rekening</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Pekerjaan</label>
                                    <input readonly type="text" name="pekerjaan" class="form-control" placeholder="Pekerjaan utama" value="{{ $debitur->pekerjaan }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Jenis Pekerjaan</label>
                                    <input readonly type="text" name="jenis_pekerjaan" class="form-control" placeholder="Contoh: Karyawan Tetap" value="{{ $debitur->jenis_pekerjaan }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Bidang Pekerjaan</label>
                                    <input readonly type="text" name="bidang_pekerjaan" class="form-control" placeholder="Contoh: Manufaktur" value="{{ $debitur->bidang_pekerjaan }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Pengalaman Kerja (Tahun)</label>
                                    <input readonly type="number" name="pengalaman_kerjatahun" class="form-control" placeholder="0" value="{{ $debitur->pengalaman_kerjatahun }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Nama Perusahaan</label>
                                    <input readonly type="text" name="nama_perusahaan" class="form-control" value="{{ $debitur->nama_perusahaan }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Jabatan/Posisi</label>
                                    <input readonly type="text" name="jabatan_posisi" class="form-control" value="{{ $debitur->jabatan_posisi }}">
                                </div>
                                <div class="col-lg-12">
                                    <label class="form-label">Alamat Perusahaan</label>
                                    <textarea readonly name="alamat_perusahaan" class="form-control" rows="2">{{ $debitur->alamat_perusahaan }}</textarea>
                                </div>
                            </div>
                            <hr class="my-4">
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Nama Bank</label>
                                    <input readonly type="text" name="nama_bank" class="form-control" placeholder="BCA/Mandiri/dsb" value="{{ $debitur->nama_bank }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Nama Pemilik Rekening</label>
                                    <input readonly type="text" name="nama_pemilik_rekening" class="form-control" value="{{ $debitur->nama_pemilik_rekening }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Nomor Rekening</label>
                                    <input readonly type="text" name="nomer_rekening" class="form-control" value="{{ $debitur->nomer_rekening }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== KEUANGAN & PEKERJAAN TAMBAHAN ===== --}}
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Keuangan & Pekerjaan Tambahan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Gaji / Penghasilan Per Bulan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input readonly type="number" name="pendapatan_per_bulan" class="form-control" value="{{ $debitur->pendapatan_per_bulan }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Pengeluaran Per Bulan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input readonly type="number" name="pengeluaran_per_bulan" class="form-control" value="{{ $debitur->pengeluaran_per_bulan }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Hutang Per Bulan (Jika Ada)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input readonly type="number" name="hutang_per_bulan" class="form-control" value="{{ $debitur->hutang_per_bulan }}">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h6 class="fs-14 mb-3">Data Pekerjaan Lainnya (Jika Ada)</h6>
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Pekerjaan Lain</label>
                                    <input readonly type="text" name="pekerjaan_lain" class="form-control" value="{{ $debitur->pekerjaan_lain }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Jenis Pekerjaan Lain</label>
                                    <input readonly type="text" name="jenis_pekerjaan_lain" class="form-control" value="{{ $debitur->jenis_pekerjaan_lain }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Bidang Pekerjaan Lain</label>
                                    <input readonly type="text" name="bidang_pekerjaan_lain" class="form-control" value="{{ $debitur->bidang_pekerjaan_lain }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Pengalaman Kerja (Tahun)</label>
                                    <input readonly type="number" name="pengalaman_kerja_lain_tahun" class="form-control" value="{{ $debitur->pengalaman_kerja_lain_tahun }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Nama Perusahaan Lain</label>
                                    <input readonly type="text" name="nama_perusahaan_lain" class="form-control" value="{{ $debitur->nama_perusahaan_lain }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Jabatan/Posisi Lain</label>
                                    <input readonly type="text" name="jabatan_posisi_lain" class="form-control" value="{{ $debitur->jabatan_posisi_lain }}">
                                </div>
                                <div class="col-lg-12">
                                    <label class="form-label">Pendapatan Lain</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input readonly type="number" name="pendapatan_lain_rupiah" class="form-control" value="{{ $debitur->pendapatan_lain_rupiah }}">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <label class="form-label">Alamat Perusahaan Lain</label>
                                    <textarea readonly name="alamat_perusahaan_lain" class="form-control" rows="1">{{ $debitur->alamat_perusahaan_lain }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                    {{-- ===== DOKUMEN UPLOAD (Preview) ===== --}}
                    <div class="card mt-3">
                        <div class="card-header border-bottom-dashed d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="ri-file-upload-line me-2 text-primary"></i>Dokumen Debitur
                            </h5>
                            <a href="{{ route('debitur.download.all', $debitur->id) }}" class="btn btn-sm btn-primary">
                                <i class="ri-download-cloud-line me-1"></i>Download Semua (.zip)
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">

                                @php
                                    $docs = [
                                        'foto_ktp'     => ['label' => 'Foto KTP Debitur',      'icon' => 'ri-id-card-line',      'color' => 'primary'],
                                        'ktp_pasangan' => ['label' => 'KTP Pasangan Debitur',  'icon' => 'ri-contacts-line',     'color' => 'success'],
                                        'foto_bpjs'    => ['label' => 'Foto Kartu BPJS',       'icon' => 'ri-health-book-line',  'color' => 'warning'],
                                        'selfie_ktp'   => ['label' => 'Selfie dengan KTP',     'icon' => 'ri-camera-line',       'color' => 'danger'],
                                        'kartu_keluarga' => ['label' => 'Kartu Keluarga',      'icon' => 'ri-group-line',        'color' => 'info'],
                                        'privy_id_file'=> ['label' => 'File Privy ID',         'icon' => 'ri-shield-keyhole-line', 'color' => 'secondary'],
                                        'id_card'      => ['label' => 'ID Card Pegawai',       'icon' => 'ri-profile-line',      'color' => 'primary'],
                                        'slip_gaji'    => ['label' => 'Slip Gaji',             'icon' => 'ri-money-dollar-circle-line', 'color' => 'success'],
                                        'buku_nikah'   => ['label' => 'Buku Nikah',            'icon' => 'ri-heart-line',        'color' => 'danger'],
                                        'mutasi_bank'  => ['label' => 'Mutasi Bank',           'icon' => 'ri-bank-line',         'color' => 'info'],
                                        'dokumen_pendukung' => ['label' => 'Dokumen Pendukung', 'icon' => 'ri-file-add-line',    'color' => 'secondary', 'multiple' => true],
                                        'saldo_jmo' => ['label' => 'Saldo JMO',            'icon' => 'ri-wallet-line',       'color' => 'secondary'],
                                    ];
                                @endphp

                                @foreach($docs as $collection => $info)
                                    @php $medias = $debitur->getMedia($collection); @endphp
                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <label class="form-label fw-semibold">{{ $info['label'] }}</label>
                                        @if($medias->count() > 0)
                                            <div class="d-flex flex-column gap-3">
                                            @foreach($medias as $media)
                                                @if(str_contains($media->mime_type, 'image'))
                                                    <div>
                                                        <div class="rounded border overflow-hidden" style="height:160px;">
                                                            <img src="{{ parse_url($media->getUrl(), PHP_URL_PATH) }}" alt="{{ $info['label'] }}"
                                                                 class="w-100 h-100" style="object-fit:cover;">
                                                        </div>
                                                        <a href="{{ parse_url($media->getUrl(), PHP_URL_PATH) }}" target="_blank"
                                                           class="btn btn-sm btn-outline-{{ $info['color'] }} mt-2 w-100">
                                                            <i class="ri-eye-line me-1"></i>Lihat Full
                                                        </a>
                                                    </div>
                                                @else
                                                    <div>
                                                        <div class="rounded border d-flex align-items-center justify-content-center bg-light" style="height:160px;">
                                                            <div class="text-center p-2 text-truncate w-100">
                                                                <i class="ri-file-pdf-line fs-1 text-{{ $info['color'] }}"></i>
                                                                <p class="small mb-0 mt-1 text-muted text-truncate" title="{{ $media->file_name }}">{{ $media->file_name }}</p>
                                                            </div>
                                                        </div>
                                                        <a href="{{ parse_url($media->getUrl(), PHP_URL_PATH) }}" target="_blank"
                                                           class="btn btn-sm btn-outline-{{ $info['color'] }} mt-2 w-100">
                                                            <i class="ri-download-line me-1"></i>Download
                                                        </a>
                                                    </div>
                                                @endif
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
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                    @if(auth()->user() && auth()->user()->isSpv())
                    <div class="text-end mb-4">
                        <a href="{{ route('debitur.index') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" name="action_type" value="reject" class="btn btn-danger me-2"
                                onclick="return confirm('Tolak pengajuan ini?');">
                            <i class="ri-close-circle-line me-1"></i>Reject
                        </button>
                        <button type="button" class="btn btn-warning me-2" onclick="promptRevisi(this)">
                            <i class="ri-edit-line me-1"></i>Revisi
                        </button>
                        <button type="submit" name="action_type" value="approve" class="btn btn-success"
                                onclick="return confirm('Setujui pengajuan ini?');">
                            <i class="ri-check-double-line me-1"></i>Approve Tahap 1
                        </button>
                    </div>
                    @endif

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
</script>
@endsection





