@extends('layouts.master')
@section('title', 'Risk - Tahap 3 | NSB')
@section('content')
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Input Debitur - Tahap 3 (Risk)</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('debitur.index') }}">Debitur</a></li>
                            <li class="breadcrumb-item active">Tahap 3 Risk</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        
        @if($errors->any())
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div>
                                <h5 class="alert-heading">Validasi Error</h5>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <div class="d-flex">
                            <div>
                                <h5 class="alert-heading">Peringatan</h5>
                                <p class="mb-0">{{ session('error') }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif
        
        <form id="edit-tahap3-risk-form" action="{{ route('debitur.update.tahap3.risk', $debitur->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-3">
                        <div class="card-body bg-light-subtle">
                            <h6 class="mb-1 text-uppercase text-muted">Data Peminjam & Pinjaman</h6>
                            <p class="mb-0 text-dark fw-medium">Nama: {{ $debitur->nama_peminjam }} | Nominal: Rp{{ number_format((float)$debitur->nominal_pinjaman, 0, ',', '.') }} | Tenor: {{ $debitur->tenor }} Bulan</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom-dashed">
                            <h5 class="card-title mb-0">Penilaian Risiko & Agunan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label class="form-label">Nomor Batch <span class="text-danger">*</span></label>
                                    <input type="text" name="batch" class="form-control" value="{{ $debitur->batch }}" required placeholder="Contoh: BATCH-01">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Nomor SPK</label>
                                    <div class="input-group">
                                        <input type="text" id="nomor_spk" name="nomor_spk" class="form-control" value="{{ $debitur->nomor_spk }}">
                                        <button class="btn btn-outline-secondary" type="button" onclick="refreshSpk()" title="Generate ulang SPK otomatis"><i class="ri-refresh-line"></i></button>
                                    </div>
                                    <small class="text-muted">Nomor SPK tidak boleh sama. Klik refresh untuk memandu pembuatan format, lalu edit jika perlu.</small>
                                </div>
                                
                                <div class="col-lg-4">
                                    <label class="form-label">Tipe Peminjam <span class="text-danger">*</span></label>
                                    <select class="form-select" name="tipe_peminjam" required>
                                        <option value="">-- Pilih Tipe Peminjam --</option>
                                        <option value="Perorangan" {{ $debitur->tipe_peminjam == 'Perorangan' ? 'selected' : '' }}>Perorangan</option>
                                        <option value="Badan Usaha" {{ $debitur->tipe_peminjam == 'Badan Usaha' ? 'selected' : '' }}>Badan Usaha</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Jenis Pinjaman <span class="text-danger">*</span></label>
                                    <select class="form-select" name="jenis_pinjaman" required>
                                        <option value="">-- Pilih Jenis Pinjaman --</option>
                                        <option value="PRODUKTIF" {{ $debitur->jenis_pinjaman == 'PRODUKTIF' ? 'selected' : '' }}>PRODUKTIF</option>
                                        <option value="MULTIGUNA" {{ $debitur->jenis_pinjaman == 'MULTIGUNA' ? 'selected' : '' }}>MULTIGUNA</option>
                                    </select>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Tujuan Pinjaman <span class="text-danger">*</span></label>
                                    <select class="form-select" name="tujuan_pinjaman" required>
                                        <option value="">-- Pilih Tujuan --</option>
                                        <optgroup label="Produktif">
                                            <option value="Modal Usaha / Business Capital" {{ $debitur->tujuan_pinjaman == 'Modal Usaha / Business Capital' ? 'selected' : '' }}>Modal Usaha / Business Capital</option>
                                            <option value="Alat & Perlengkapan Usaha" {{ $debitur->tujuan_pinjaman == 'Alat & Perlengkapan Usaha' ? 'selected' : '' }}>Alat & Perlengkapan Usaha</option>
                                            <option value="Bahan Baku / Stok" {{ $debitur->tujuan_pinjaman == 'Bahan Baku / Stok' ? 'selected' : '' }}>Bahan Baku / Stok</option>
                                            <option value="Jasa & Layanan" {{ $debitur->tujuan_pinjaman == 'Jasa & Layanan' ? 'selected' : '' }}>Jasa & Layanan</option>
                                            <option value="Pertanian / Peternakan" {{ $debitur->tujuan_pinjaman == 'Pertanian / Peternakan' ? 'selected' : '' }}>Pertanian / Peternakan</option>
                                            <option value="Transportasi Usaha" {{ $debitur->tujuan_pinjaman == 'Transportasi Usaha' ? 'selected' : '' }}>Transportasi Usaha</option>
                                            <option value="Biaya Operasional" {{ $debitur->tujuan_pinjaman == 'Biaya Operasional' ? 'selected' : '' }}>Biaya Operasional</option>
                                            <option value="Lain-lain" {{ $debitur->tujuan_pinjaman == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                                        </optgroup>
                                        <optgroup label="Konsumtif / Multiguna">
                                            <option value="Kebutuhan Rumah Tangga" {{ $debitur->tujuan_pinjaman == 'Kebutuhan Rumah Tangga' ? 'selected' : '' }}>Kebutuhan Rumah Tangga</option>
                                            <option value="Pendidikan" {{ $debitur->tujuan_pinjaman == 'Pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                                            <option value="Kesehatan" {{ $debitur->tujuan_pinjaman == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                                            <option value="Acara / Sosial" {{ $debitur->tujuan_pinjaman == 'Acara / Sosial' ? 'selected' : '' }}>Acara / Sosial</option>
                                            <option value="Lain-lain / Pribadi" {{ $debitur->tujuan_pinjaman == 'Lain-lain / Pribadi' ? 'selected' : '' }}>Lain-lain / Pribadi</option>
                                        </optgroup>
                                    </select>
                                </div>
                                
                                <div class="col-lg-12 mt-4">
                                    <h6 class="fw-semibold font-size-12 mb-3 text-uppercase">Jadwal Pinjaman</h6>
                                </div>

                                <div class="col-lg-4">
                                    <label class="form-label">Tanggal Akad Kredit <span class="text-danger">*</span></label>
                                    <input type="date" id="tanggal_akad_kredit" name="tanggal_akad_kredit" class="form-control" value="{{ $debitur->tanggal_akad_kredit ? \Carbon\Carbon::parse($debitur->tanggal_akad_kredit)->format('Y-m-d') : '' }}" required>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Tgl. Angsuran Pertama <small class="text-muted">(+1 Bulan)</small></label>
                                    <input type="date" id="tanggal_angsuran_pertama_dibayar" name="tanggal_angsuran_pertama_dibayar" class="form-control bg-light" value="{{ $debitur->tanggal_angsuran_pertama_dibayar ? \Carbon\Carbon::parse($debitur->tanggal_angsuran_pertama_dibayar)->format('Y-m-d') : '' }}" readonly>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Tanggal Jatuh Tempo <small class="text-muted">(+{{ $debitur->tenor_bulan ?? $debitur->tenor }} Bulan)</small></label>
                                    <input type="date" id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo" class="form-control bg-light" value="{{ $debitur->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($debitur->tanggal_jatuh_tempo)->format('Y-m-d') : '' }}" readonly>
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
                                <i class="ri-download-cloud-2-line me-1"></i>Download Semua Berkas
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
                                        'saldo_jmo'     => ['label' => 'Saldo JMO',            'icon' => 'ri-wallet-line',      'color' => 'secondary'],
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
                                                        <a href="{{ parse_url($media->getUrl(), PHP_URL_PATH) }}" download
                                                           class="btn btn-sm btn-{{ $info['color'] }} mt-1 w-100">
                                                            <i class="ri-download-line me-1"></i>Download
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
                                                            <i class="ri-eye-line me-1"></i>Lihat
                                                        </a>
                                                        <a href="{{ parse_url($media->getUrl(), PHP_URL_PATH) }}" download
                                                           class="btn btn-sm btn-{{ $info['color'] }} mt-1 w-100">
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
                    
                    <div class="text-end mb-4">
                        <a href="{{ route('debitur.index') }}" class="btn btn-light me-2">Batal</a>
                        <button type="submit" name="action_type" value="reject" class="btn btn-danger me-2" onclick="return confirm('Tolak pengajuan ini secara permanen (Risk)?');">Reject</button>
                        <button type="button" class="btn btn-warning me-2" onclick="promptRevisi(this)">Revisi</button>
                        <button type="submit" name="action_type" value="simpan" class="btn btn-primary">Simpan Lanjut Approval Mitra (Akhir)</button>
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

// Helper function to convert number to Roman numerals
function toRoman(num) {
    const map = {M:1000, CM:900, D:500, CD:400, C:100, XC:90, L:50, XL:40, X:10, IX:9, V:5, IV:4, I:1};
    let roman = '';
    for (let i in map) {
        while (num >= map[i]) {
            roman += i;
            num -= map[i];
        }
    }
    return roman;
}

// Function to generate and update dates and SPK
function generateDatesAndSpk() {
    const tglAkadInput = document.getElementById('tanggal_akad_kredit');
    const tglAngsuran1Input = document.getElementById('tanggal_angsuran_pertama_dibayar');
    const tglJatuhTempoInput = document.getElementById('tanggal_jatuh_tempo');
    const spkInput = document.getElementById('nomor_spk');
    
    if (!tglAkadInput.value) {
        tglAngsuran1Input.value = '';
        tglJatuhTempoInput.value = '';
        spkInput.value = '';
        return;
    }

    const dateAkad = new Date(tglAkadInput.value);
    if (isNaN(dateAkad.getTime())) return;

    const tenorBulan = parseInt('{{ $debitur->tenor_bulan ?? $debitur->tenor }}') || 0;
    const debiturIdPadded = '{{ str_pad($debitur->id, 4, "0", STR_PAD_LEFT) }}';
    const cabangId = '{{ $debitur->cabang_id }}';

    // Tgl Angsuran Pertama (+1 bulan)
    const dateAngsuran1 = new Date(dateAkad);
    dateAngsuran1.setMonth(dateAngsuran1.getMonth() + 1);
    tglAngsuran1Input.value = dateAngsuran1.toISOString().split('T')[0];

    // Tgl Jatuh Tempo (+tenor)
    const dateJatuhTempo = new Date(dateAkad);
    dateJatuhTempo.setMonth(dateJatuhTempo.getMonth() + tenorBulan);
    tglJatuhTempoInput.value = dateJatuhTempo.toISOString().split('T')[0];

    // Generate SPK
    const mmRoman = toRoman(dateAkad.getMonth() + 1);
    const yyyy = dateAkad.getFullYear();
    
    const generatedSpk = `${debiturIdPadded}/PK-NSB/0${cabangId}/${mmRoman}/${yyyy}`;
    spkInput.value = generatedSpk;
}

document.addEventListener('DOMContentLoaded', function() {
    const tglAkadInput = document.getElementById('tanggal_akad_kredit');
    
    tglAkadInput.addEventListener('change', function() {
        generateDatesAndSpk();
    });
});

function refreshSpk() {
    const tglAkadInput = document.getElementById('tanggal_akad_kredit');
    if (tglAkadInput.value) {
        generateDatesAndSpk();
        alert('SPK berhasil di-refresh!');
    } else {
        alert('Silakan isi Tanggal Akad Kredit terlebih dahulu untuk men-generate SPK.');
    }
}
</script>
@endsection





