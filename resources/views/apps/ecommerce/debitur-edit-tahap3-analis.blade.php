@extends('layouts.master')
@section('title', 'Analis - Tahap 3 | NSB')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Input Debitur - Tahap 3 (Analis)</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('debitur.index') }}">Debitur</a></li>
                            <li class="breadcrumb-item active">Tahap 3 Analis</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        
        <form id="edit-tahap3-analis-form" action="{{ route('debitur.update.tahap3.analis', $debitur->id) }}" method="POST">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <h6 class="alert-heading mb-2">Mohon periksa kembali input berikut:</h6>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
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
                            <h5 class="card-title mb-0">Review Data Debitur, Pinjaman & Domisili</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 border-end">
                                    <h6 class="text-uppercase fw-semibold mb-3">Tahap 1 - Data Debitur</h6>
                                    @php
                                        $namaProvinsiKtp = $debitur->provinsi_ktp ?: optional(\App\Models\Province::find($debitur->kode_provinsi))->name;
                                        $namaKabupatenKtp = $debitur->kota_kabupaten_ktp ?: optional(\App\Models\Regency::find($debitur->kode_kabupaten))->name;
                                        $namaKecamatanKtp = $debitur->kecamatan_ktp ?: optional(\App\Models\District::find($debitur->kode_kecamatan))->name;
                                        $namaKelurahanKtp = $debitur->kelurahan_ktp ?: optional(\App\Models\Village::find($debitur->kode_kelurahan))->name;
                                    @endphp
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">Email</dt>
                                        <dd class="col-sm-8">{{ $debitur->email ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Nomor Handphone</dt>
                                        <dd class="col-sm-8">{{ $debitur->nomor_handphone ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Privy ID</dt>
                                        <dd class="col-sm-8">{{ $debitur->privy_id ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Jenis Kelamin</dt>
                                        <dd class="col-sm-8">{{ $debitur->jenis_kelamin ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Tempat Lahir</dt>
                                        <dd class="col-sm-8">{{ $debitur->tempat_lahir ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Tanggal Lahir</dt>
                                        <dd class="col-sm-8">{{ $debitur->tanggal_lahir ? \Carbon\Carbon::parse($debitur->tanggal_lahir)->format('d M Y') : '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Nama Ibu Kandung</dt>
                                        <dd class="col-sm-8">{{ $debitur->nama_ibu_kandung ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Agama</dt>
                                        <dd class="col-sm-8">{{ $debitur->agama ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Pendidikan</dt>
                                        <dd class="col-sm-8">{{ $debitur->pendidikan ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Status Perkawinan</dt>
                                        <dd class="col-sm-8">{{ $debitur->status_perkawinan ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Alamat KTP Lengkap</dt>
                                        <dd class="col-sm-8">{{ $debitur->alamat_ktp ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">RT</dt>
                                        <dd class="col-sm-8">{{ $debitur->rt_ktp ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">RW</dt>
                                        <dd class="col-sm-8">{{ $debitur->rw_ktp ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Provinsi</dt>
                                        <dd class="col-sm-8">{{ $namaProvinsiKtp ? ucwords(strtolower($namaProvinsiKtp)) : '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Kota/Kabupaten</dt>
                                        <dd class="col-sm-8">{{ $namaKabupatenKtp ? ucwords(strtolower($namaKabupatenKtp)) : '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Kecamatan</dt>
                                        <dd class="col-sm-8">{{ $namaKecamatanKtp ? ucwords(strtolower($namaKecamatanKtp)) : '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Kelurahan/Desa</dt>
                                        <dd class="col-sm-8">{{ $namaKelurahanKtp ? ucwords(strtolower($namaKelurahanKtp)) : '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Kode Pos</dt>
                                        <dd class="col-sm-8">{{ $debitur->kode_pos_ktp ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Nama Perusahaan</dt>
                                        <dd class="col-sm-8">{{ $debitur->nama_perusahaan ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Jabatan / Posisi</dt>
                                        <dd class="col-sm-8">{{ $debitur->jabatan_posisi ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Pengalaman Kerja</dt>
                                        <dd class="col-sm-8">{{ $debitur->pengalaman_kerjatahun ?: '-' }} tahun</dd>
                                        
                                        <dt class="col-sm-4">Alamat Perusahaan</dt>
                                        <dd class="col-sm-8">{{ $debitur->alamat_perusahaan ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Nama Bank</dt>
                                        <dd class="col-sm-8">{{ $debitur->nama_bank ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Nama Pemilik Rekening</dt>
                                        <dd class="col-sm-8">{{ $debitur->nama_pemilik_rekening ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Nomor Rekening</dt>
                                        <dd class="col-sm-8">{{ $debitur->nomer_rekening ?: '-' }}</dd>
                                    </dl>
                                </div>

                                <div class="col-lg-6 ps-lg-4">
                                    <h6 class="text-uppercase fw-semibold mb-3 mt-4 mt-lg-0">Tahap 2 - Pinjaman & Domisili</h6>
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">Batch</dt>
                                        <dd class="col-sm-8">{{ $debitur->batch ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Tipe Peminjam</dt>
                                        <dd class="col-sm-8">{{ $debitur->tipe_peminjam ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Nomor Anggota</dt>
                                        <dd class="col-sm-8">{{ $debitur->nomor_anggota ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Pengajuan Plafond</dt>
                                        <dd class="col-sm-8">Rp {{ number_format(floatval($debitur->pengajuan_plafond), 0, ',', '.') }}</dd>
                                        
                                        <dt class="col-sm-4">Alamat Domisili</dt>
                                        <dd class="col-sm-8">{{ $debitur->alamat_domisili ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">RT</dt>
                                        <dd class="col-sm-8">{{ $debitur->rt_domisili ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">RW</dt>
                                        <dd class="col-sm-8">{{ $debitur->rw_domisili ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Provinsi</dt>
                                        <dd class="col-sm-8">{{ $debitur->provinsi_domisili ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Kota/Kabupaten</dt>
                                        <dd class="col-sm-8">{{ $debitur->kota_kabupaten_domisili ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Kecamatan</dt>
                                        <dd class="col-sm-8">{{ $debitur->kecamatan_domisili ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Kelurahan/Desa</dt>
                                        <dd class="col-sm-8">{{ $debitur->kelurahan_domisili ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Kode Pos</dt>
                                        <dd class="col-sm-8">{{ $debitur->kode_pos_domisili ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Status Kepemilikan Rumah</dt>
                                        <dd class="col-sm-8">{{ $debitur->status_kepemilikan_rumah ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Nama Keluarga</dt>
                                        <dd class="col-sm-8">{{ $debitur->nama_keluarga_tidak_serumah ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Hubungan</dt>
                                        <dd class="col-sm-8">{{ $debitur->hubungan_keluarga ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">No. HP Keluarga</dt>
                                        <dd class="col-sm-8">{{ $debitur->no_handphone_keluarga ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Alamat KTP Keluarga</dt>
                                        <dd class="col-sm-8">{{ $debitur->alamat_ktp_keluarga ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Provinsi Keluarga</dt>
                                        <dd class="col-sm-8">{{ $debitur->provinsi_ktp_keluarga ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Kota/Kabupaten Keluarga</dt>
                                        <dd class="col-sm-8">{{ $debitur->kota_kabupaten_ktp_keluarga ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Kecamatan Keluarga</dt>
                                        <dd class="col-sm-8">{{ $debitur->kecamatan_ktp_keluarga ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Kelurahan/Desa Keluarga</dt>
                                        <dd class="col-sm-8">{{ $debitur->kelurahan_ktp_keluarga ?: '-' }}</dd>
                                        
                                        <dt class="col-sm-4">Kode Pos Keluarga</dt>
                                        <dd class="col-sm-8">{{ $debitur->kode_pos_ktp_keluarga ?: '-' }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Produk Analis</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-12 mb-2">
                                    <label class="form-label">Jenis Produk</label>
                                    <select name="jenis_produk" id="jenis_produk" class="form-select @error('jenis_produk') is-invalid @enderror" required>
                                        <option value="">-- Pilih Jenis Produk --</option>
                                        @foreach($parameterProduks as $produk)
                                            <option value="{{ $produk->nama_produk }}" 
                                                data-bunga="{{ $produk->bunga_persen }}"
                                                data-sukubunga="{{ $produk->suku_bunga }}"
                                                {{ old('jenis_produk', optional($analisa)->jenis_produk) == $produk->nama_produk ? 'selected' : '' }}>
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('jenis_produk') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                </div>
                                <!-- (default dari marketing, bisa diubah analis) -->
                                <div class="col-lg-4">
                                    <label class="form-label">Tenor (Bulan)</small></label>
                                    <input type="number" id="tenor_bulan" name="tenor_bulan" class="form-control kalkulasi" value="{{ old('tenor_bulan', floatval($debitur->tenor_bulan)) }}" min="1" required>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Bunga (%) per Bulan/Tahun <small class="text-muted">(otomatis)</small></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" id="bunga_persen" name="bunga_persen" class="form-control bg-light kalkulasi" value="{{ old('bunga_persen', optional($analisa)->bunga_persen ?? floatval($debitur->bunga_persen)) }}" readonly>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Suku Bunga <small class="text-muted">(otomatis)</small></label>
                                    <input type="text" id="suku_bunga" name="suku_bunga" class="form-control bg-light" value="{{ old('suku_bunga', optional($analisa)->suku_bunga ?? $debitur->suku_bunga) }}" readonly>
                                </div>
                                
                                <div class="col-lg-12 mt-3 mb-2">
                                    <hr class="border-dashed">
                                </div>

                                <div class="col-lg-6">
                                    <label class="form-label">Plafond Diajukan (Debitur)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" id="pengajuan_plafond" class="form-control bg-light text-muted" value="{{ floatval($debitur->pengajuan_plafond) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Usulan Plafond <small class="text-muted">(opsional, ubah jika perlu)</small></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" id="usulan_plafond" name="usulan_plafond" class="form-control kalkulasi text-primary fw-bold" value="{{ old('usulan_plafond', (optional($analisa)->pengajuan_plafond && optional($analisa)->pengajuan_plafond != floatval($debitur->pengajuan_plafond)) ? optional($analisa)->pengajuan_plafond : '') }}" placeholder="Isi untuk override plafond awal">
                                    </div>
                                    <small class="text-muted">Biarkan kosong untuk menggunakan Plafond Diajukan</small>
                                </div>
                                
                                <div class="col-lg-4">
                                    <label class="form-label">Jaminan</label>
                                    <input type="text" name="jaminan" class="form-control @error('jaminan') is-invalid @enderror" value="{{ old('jaminan', optional($analisa)->jaminan) }}" required>
                                    @error('jaminan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Nomor BPJS</label>
                                    <input type="text" name="kode_jaminan" class="form-control @error('kode_jaminan') is-invalid @enderror" value="{{ old('kode_jaminan', optional($analisa)->kode_jaminan) }}" required>
                                    @error('kode_jaminan') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Saldo BPJS</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" id="saldo_bpjs" name="saldo_bpjs" class="form-control kalkulasi @error('saldo_bpjs') is-invalid @enderror" value="{{ old('saldo_bpjs', optional($analisa)->saldo_bpjs) }}" required>
                                    </div>
                                    @error('saldo_bpjs') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Analisa Keuangan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-4">
                                    <label class="form-label">Nomor MPP</label>
                                    <input type="text" name="nomor_mpp" class="form-control bg-light" value="{{ old('nomor_mpp', optional($analisa)->nomor_mpp) }}" readonly>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Tanggal MPP</label>
                                    <input type="date" name="tanggal_mpp" class="form-control @error('tanggal_mpp') is-invalid @enderror" value="{{ old('tanggal_mpp', optional(optional($analisa)->tanggal_mpp)->format('Y-m-d')) }}" required>
                                    @error('tanggal_mpp') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Jenis Fasilitas</label>
                                    <select name="jenis_fasilitas" class="form-select @error('jenis_fasilitas') is-invalid @enderror" required>
                                        <option value="">-- Pilih Jenis Fasilitas --</option>
                                        <option value="MULTIGUNA" {{ old('jenis_fasilitas', optional($analisa)->jenis_fasilitas) == 'MULTIGUNA' ? 'selected' : '' }}>MULTIGUNA</option>
                                    </select>
                                    @error('jenis_fasilitas') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="col-lg-4">
                                    <label class="form-label">Cabang</label>
                                    <input type="text" class="form-control bg-light" value="{{ optional($debitur->cabang)->nama_cabang ?: '-' }}" readonly>
                                    <input type="hidden" name="cabang_id" value="{{ $debitur->cabang_id }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Marketing</label>
                                    <input type="text" class="form-control bg-light" value="{{ optional($debitur->marketing)->name ?: '-' }}" readonly>
                                    <input type="hidden" name="marketing_id" value="{{ $debitur->marketing_id }}">
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">SPV Marketing</label>
                                    <input type="text" class="form-control bg-light" value="{{ $debitur->spv ? $debitur->spv->name : '-' }}" readonly>
                                    <input type="hidden" name="spv_id" value="{{ $debitur->spv_id }}">
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h6 class="fw-bold mb-3">ANALISA KEUANGAN (per Bulan)</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-lg-4">
                                    <label class="form-label">Pendapatan Per Bulan</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" id="pendapatan_per_bulan" class="form-control kalkulasi bg-light" value="{{ floatval($debitur->pendapatan_per_bulan) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Pendapatan Lain</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" id="pendapatan_lain_rupiah" class="form-control kalkulasi bg-light" value="{{ floatval($debitur->pendapatan_lain_rupiah) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Total Penghasilan Bersih</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="total_penghasilan_bersih" id="total_penghasilan_bersih" class="form-control bg-light fw-bold" value="{{ old('total_penghasilan_bersih', optional($analisa)->total_penghasilan_bersih) }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-lg-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0 fw-bold">Hutang di Tempat Lain</label>
                                        <button type="button" class="btn btn-sm btn-soft-primary" id="btn-add-hutang"><i class="ri-add-line"></i> Tambah Hutang</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0" id="table-hutang">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nama/Jenis Hutang</th>
                                                    <th width="30%">Angsuran (Rp)</th>
                                                    <th width="10%" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $detail_hutang = old('detail_hutang', optional($analisa)->detail_hutang ?? []);
                                                @endphp
                                                @if(count($detail_hutang) > 0)
                                                    @foreach($detail_hutang as $index => $hutang)
                                                    <tr>
                                                        <td><input type="text" name="detail_hutang[{{$index}}][nama]" class="form-control form-control-sm" value="{{ $hutang['nama'] ?? '' }}" required></td>
                                                        <td><input type="number" name="detail_hutang[{{$index}}][angsuran]" class="form-control form-control-sm input-hutang" value="{{ $hutang['angsuran'] ?? 0 }}" required></td>
                                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remove-hutang"><i class="ri-delete-bin-line"></i></button></td>
                                                    </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="text-end align-middle">Total Angsuran Hutang:</th>
                                                    <th>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="number" name="total_angsuran_hutang" id="total_angsuran_hutang" class="form-control bg-light fw-bold" value="{{ old('total_angsuran_hutang', optional($analisa)->total_angsuran_hutang ?? 0) }}" readonly>
                                                        </div>
                                                    </th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-lg-6">
                                    <div class="p-3 bg-light rounded border">
                                        <label class="form-label fw-bold">Total Angsuran Pengajuan Baru</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" name="total_angsuran_pengajuan" id="total_angsuran_pengajuan" class="form-control fs-5 fw-bold text-primary" value="{{ old('total_angsuran_pengajuan', optional($analisa)->total_angsuran_pengajuan) }}" readonly>
                                        </div>
                                        <small class="text-muted mt-1 d-block">Rumus: (Plafond * 3.5%) + (Plafond / Tenor)</small>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            
                            <h6 class="fw-bold mb-3">ANALISA KEMAMPUAN MEMBAYAR ANGSURAN</h6>
                            <div class="row g-3 align-items-end">
                                <div class="col-lg-4">
                                    <label class="form-label">IIR (Installment to Income Ratio)</label>
                                    <div class="input-group">
                                        <input type="text" id="iir_display" class="form-control bg-light fw-bold" readonly>
                                        <input type="hidden" name="nilai_iir" id="nilai_iir" value="{{ old('nilai_iir', $analisa->nilai_iir) }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <small class="text-muted mt-1 d-block">(Total Hutang + Total Pengajuan) / Penghasilan Bersih</small>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">LTV (Loan To Value)</label>
                                    <div class="input-group">
                                        <input type="text" id="ltv_display" class="form-control bg-light fw-bold" readonly>
                                        <input type="hidden" name="nilai_ltv" id="nilai_ltv" value="{{ old('nilai_ltv', $analisa->nilai_ltv) }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Plafond / Saldo BPJS</small>
                                </div>
                                <div class="col-lg-4">
                                    <div class="p-3 border rounded text-center h-100 d-flex flex-column justify-content-center" id="kesimpulan_container">
                                        <span class="d-block text-muted small mb-1 fw-bold">Kesimpulan Analisa</span>
                                        <h4 class="mb-1 fw-bold" id="kesimpulan_text">-</h4>
                                        <small id="kesimpulan_alasan" class="d-block lh-sm mt-2"></small>
                                    </div>
                                </div>
                                <div class="col-lg-12 mt-3" id="force_approve_container" style="display: none;">
                                    <div class="alert alert-warning mb-0 border-warning">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="force_approve_checkbox">
                                            <label class="form-check-label fw-bold text-dark" for="force_approve_checkbox">Tetap Setujui (Force Approve)</label>
                                        </div>
                                        <div id="alasan_disetujui_wrapper" style="display: none;">
                                            <label class="form-label text-dark small fw-bold">Alasan Persetujuan Khusus <span class="text-danger">*</span></label>
                                            <textarea name="alasan_disetujui" id="alasan_disetujui" class="form-control form-control-sm" rows="2" placeholder="Masukkan alasan mengapa pengajuan ini tetap disetujui meskipun hasil analisa menolak..."></textarea>
                                        </div>
                                    </div>
                                </div>
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
                                        'kartu_keluarga'    => ['label' => 'Kartu Keluarga',        'icon' => 'ri-group-line',        'color' => 'info',      'multiple' => false],
                                        'privy_id_file'     => ['label' => 'Privy ID',              'icon' => 'ri-shield-check-line', 'color' => 'info',      'multiple' => false],
                                        'id_card'           => ['label' => 'ID Card',               'icon' => 'ri-id-card-2-line',   'color' => 'secondary', 'multiple' => false],
                                        'slip_gaji'         => ['label' => 'Slip Gaji',             'icon' => 'ri-file-text-line',   'color' => 'success',   'multiple' => false],
                                        'buku_nikah'        => ['label' => 'Buku Nikah',            'icon' => 'ri-book-line',        'color' => 'primary',   'multiple' => false],
                                        'dokumen_pendukung' => ['label' => 'Dokumen Pendukung',     'icon' => 'ri-attachment-line',  'color' => 'dark',      'multiple' => true],
                                        'saldo_jmo'         => ['label' => 'Saldo JMO',             'icon' => 'ri-wallet-line',      'color' => 'secondary', 'multiple' => false],
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
                    
                    <div class="text-end mb-4">
                        <a href="{{ route('debitur.index') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" name="action_type" value="reject" class="btn btn-danger me-2" onclick="return confirm('Tolak pengajuan ini secara permanen (Analis)?');">Reject</button>
                        <button type="button" class="btn btn-warning me-2" onclick="promptRevisi(this)">Revisi</button>
                        <button type="submit" name="action_type" value="simpan" class="btn btn-primary" id="btn-simpan-analis" onclick="return confirm('Apakah Anda yakin ingin menyimpan persetujuan Analis ini? Data akan diteruskan ke Tahap Risk.');">Simpan Lanjut Risk (Tahap 3)</button>
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

document.addEventListener('DOMContentLoaded', function() {
    const tableHutangBody = document.querySelector('#table-hutang tbody');
    const btnAddHutang = document.getElementById('btn-add-hutang');
    
    let hutangIndex = {{ isset($analisa) && isset($analisa->detail_hutang) ? count($analisa->detail_hutang) : 0 }};

    function addHutangRow() {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" name="detail_hutang[${hutangIndex}][nama]" class="form-control form-control-sm" required></td>
            <td><input type="number" name="detail_hutang[${hutangIndex}][angsuran]" class="form-control form-control-sm input-hutang" value="0" required></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-danger btn-remove-hutang"><i class="ri-delete-bin-line"></i></button></td>
        `;
        tableHutangBody.appendChild(row);
        hutangIndex++;
        calculateAll();
    }

    btnAddHutang.addEventListener('click', addHutangRow);

    tableHutangBody.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-hutang')) {
            e.target.closest('tr').remove();
            calculateAll();
        }
    });

    tableHutangBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('input-hutang')) {
            calculateAll();
        }
    });

    document.getElementById('jenis_produk').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            document.getElementById('bunga_persen').value = selectedOption.getAttribute('data-bunga');
            document.getElementById('suku_bunga').value = selectedOption.getAttribute('data-sukubunga');
        } else {
            document.getElementById('bunga_persen').value = '';
            document.getElementById('suku_bunga').value = '';
        }
        calculateAll();
    });

    document.querySelectorAll('.kalkulasi').forEach(item => {
        item.addEventListener('input', calculateAll);
    });

    function calculateAll() {
        // 1. Hitung Total Penghasilan Bersih
        const pendapatanUtama = parseFloat(document.getElementById('pendapatan_per_bulan').value) || 0;
        const pendapatanLain = parseFloat(document.getElementById('pendapatan_lain_rupiah').value) || 0;
        const totalPenghasilan = pendapatanUtama + pendapatanLain;
        document.getElementById('total_penghasilan_bersih').value = totalPenghasilan;

        // 2. Hitung Total Angsuran Hutang Tempat Lain
        let totalHutang = 0;
        document.querySelectorAll('.input-hutang').forEach(item => {
            totalHutang += parseFloat(item.value) || 0;
        });
        document.getElementById('total_angsuran_hutang').value = totalHutang;

        // 3. Hitung Total Angsuran Pengajuan Baru: (Plafond * Bunga%) + (Plafond / Tenor)
        let plafond = parseFloat(document.getElementById('usulan_plafond').value);
        if (isNaN(plafond) || plafond <= 0) {
            plafond = parseFloat(document.getElementById('pengajuan_plafond').value) || 0;
        }
        const tenor = parseFloat(document.getElementById('tenor_bulan').value) || 1;
        const bungaPersen = parseFloat(document.getElementById('bunga_persen').value) || 0;
        
        // Menggunakan bunga dari produk jika tersedia, jika tidak kembali ke 3.5%
        const bungaRate = (bungaPersen > 0) ? (bungaPersen / 100) : 0.035;
        const angsuranBaru = (plafond * bungaRate) + (plafond / tenor);
        document.getElementById('total_angsuran_pengajuan').value = Math.round(angsuranBaru);

        // 4. Hitung IIR: (Total Hutang + Total Pengajuan) / Penghasilan Bersih
        let iir = 0;
        if (totalPenghasilan > 0) {
            iir = ((totalHutang + angsuranBaru) / totalPenghasilan) * 100;
        }
        document.getElementById('iir_display').value = iir.toFixed(2);
        document.getElementById('nilai_iir').value = iir.toFixed(2);

        // 5. Hitung LTV: Plafond / Saldo BPJS
        const saldoBpjs = parseFloat(document.getElementById('saldo_bpjs').value) || 0;
        let ltv = 0;
        if (saldoBpjs > 0) {
            ltv = (plafond / saldoBpjs) * 100;
        }
        document.getElementById('ltv_display').value = ltv.toFixed(2);
        document.getElementById('nilai_ltv').value = ltv.toFixed(2);

        const kesimpulanContainer = document.getElementById('kesimpulan_container');
        const kesimpulanText = document.getElementById('kesimpulan_text');
        const kesimpulanAlasan = document.getElementById('kesimpulan_alasan');
        const forceApproveContainer = document.getElementById('force_approve_container');
        const forceApproveCheckbox = document.getElementById('force_approve_checkbox');
        const btnSimpan = document.getElementById('btn-simpan-analis');

        kesimpulanContainer.classList.remove('border-success', 'border-danger', 'bg-success-subtle', 'bg-danger-subtle');
        kesimpulanText.classList.remove('text-success', 'text-danger');

        let isDisetujui = true;
        let alasan = [];

        if (ltv <= 80) {
            // LTV Rendah
        } else {
            alasan.push("LTV Tinggi (>80%): Jaminan kurang memadai");
            isDisetujui = false;
        }

        if (iir <= 40) {
            // IIR Ideal
        } else {
            alasan.push("IIR Tinggi (>40%): Pendapatan kurang memadai untuk membayar angsuran");
            isDisetujui = false;
        }

        if (isDisetujui) {
            kesimpulanText.textContent = "DISETUJUI";
            kesimpulanText.classList.add('text-success');
            kesimpulanContainer.classList.add('border-success', 'bg-success-subtle');
            if(kesimpulanAlasan) {
                kesimpulanAlasan.className = "d-block lh-sm mt-2 text-success";
                kesimpulanAlasan.innerHTML = "LTV rendah (jaminan memadai) dan IIR rendah (pendapatan cukup).";
            }
            
            // Sembunyikan Force Approve
            forceApproveContainer.style.display = 'none';
            forceApproveCheckbox.checked = false;
            document.getElementById('alasan_disetujui_wrapper').style.display = 'none';
            document.getElementById('alasan_disetujui').required = false;
            btnSimpan.disabled = false;
            
        } else {
            kesimpulanText.textContent = "DITOLAK / PENYESUAIAN";
            kesimpulanText.classList.add('text-danger');
            kesimpulanContainer.classList.add('border-danger', 'bg-danger-subtle');
            if(kesimpulanAlasan) {
                kesimpulanAlasan.className = "d-block lh-sm mt-2 text-danger fw-medium";
                kesimpulanAlasan.innerHTML = alasan.join('<br>');
            }
            
            // Tampilkan Force Approve
            forceApproveContainer.style.display = 'block';
            if (!forceApproveCheckbox.checked) {
                btnSimpan.disabled = true;
            } else {
                btnSimpan.disabled = false;
            }
        }
    }

    document.getElementById('force_approve_checkbox').addEventListener('change', function() {
        const btnSimpan = document.getElementById('btn-simpan-analis');
        const alasanWrapper = document.getElementById('alasan_disetujui_wrapper');
        const alasanInput = document.getElementById('alasan_disetujui');
        
        if (this.checked) {
            alasanWrapper.style.display = 'block';
            alasanInput.required = true;
            btnSimpan.disabled = false;
        } else {
            alasanWrapper.style.display = 'none';
            alasanInput.required = false;
            btnSimpan.disabled = true;
        }
    });

    // Hitung saat pertama kali load
    calculateAll();
});
</script>
@endsection





