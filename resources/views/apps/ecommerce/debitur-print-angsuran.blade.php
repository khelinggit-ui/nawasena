<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Angsuran - {{ $debitur->nama_peminjam ?? '-' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 14mm;
        }

        body {
            font-family: Arial, sans-serif;
            color: #1f2937;
            margin: 0;
            font-size: 12px;
        }

        .print-bar {
            margin-bottom: 14px;
        }

        .print-btn {
            border: 0;
            background: #2563eb;
            color: #fff;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
        }

        .header {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .logo-wrap {
            width: 130px;
            flex-shrink: 0;
        }

        .logo-wrap img {
            width: 90px;
            height: auto;
        }

        .title-wrap {
            flex: 1;
            text-align: center;
            margin-right: 130px;
        }

        .title-wrap h1 {
            margin: 0;
            font-size: 20px;
            line-height: 1.2;
            font-weight: 700;
        }

        .title-wrap p {
            margin: 2px 0 0;
            font-size: 13px;
            font-weight: 700;
        }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 10px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 2px 0;
            vertical-align: top;
            font-size: 12px;
        }

        .meta-table td:nth-child(1) {
            width: 45%;
        }

        .meta-table td:nth-child(2) {
            width: 5%;
            text-align: center;
        }

        .meta-table td:nth-child(3) {
            width: 50%;
            font-weight: 700;
        }

        .meta-right td:nth-child(1) {
            width: 62%;
            text-align: right;
            padding-right: 8px;
        }

        .meta-right td:nth-child(2) {
            width: 38%;
            text-align: right;
            font-weight: 700;
        }

        .schedule {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 11px;
        }

        .schedule th,
        .schedule td {
            border: 1px solid #d1d5db;
            padding: 6px 5px;
        }

        .schedule thead th {
            background: #f3f4f6;
            text-align: center;
            color: #1e3a8a;
            font-weight: 700;
        }

        .num {
            text-align: right;
            white-space: nowrap;
        }

        .center {
            text-align: center;
        }

        .status {
            font-weight: 700;
        }

        .footer {
            margin-top: 6px;
            text-align: right;
            font-size: 11px;
            color: #6b7280;
        }

        @media print {
            .print-bar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-bar">
        <button type="button" class="print-btn" onclick="window.print()">Cetak Kartu Angsuran</button>
    </div>

    <div class="header">
        <div class="logo-wrap">
            <img src="{{ asset('assets/images/logo-nsb.png') }}" alt="Logo NSB">
        </div>
        <div class="title-wrap">
            <h1>Koperasi Nawasena Sanggah Bersama (NSB)</h1>
            <p>Kartu Angsuran</p>
            <p>No kredit : {{ $debitur->nomor_spk ?? '-' }}</p>
        </div>
    </div>

    <div class="two-col">
        <table class="meta-table">
            <tr>
                <td>Nasabah (Anggota)</td>
                <td>:</td>
                <td>[{{ $debitur->id }}] - {{ $debitur->nama_peminjam ?? '-' }}</td>
            </tr>
            <tr>
                <td>No Kredit</td>
                <td>:</td>
                <td>{{ $debitur->nomor_debitur ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jenis Pinjaman</td>
                <td>:</td>
                <td>{{ $debitur->jenis_pinjaman ?? 'Pinjaman Uang' }}</td>
            </tr>
            <tr>
                <td>Deskripsi Pinjaman</td>
                <td>:</td>
                <td>{{ $debitur->tujuan_pinjaman ?? 'PINJAMAN KARYAWAN' }}</td>
            </tr>
            <tr>
                <td>Keterangan Jaminan</td>
                <td>:</td>
                <td>{{ $debitur->jaminan ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tenor</td>
                <td>:</td>
                <td>{{ $summary['tenor_bulan'] }} Bulan</td>
            </tr>
            <tr>
                <td>Tanggal Pengajuan</td>
                <td>:</td>
                <td>{{ !empty($debitur->tanggal_akad_kredit) ? \Carbon\Carbon::parse($debitur->tanggal_akad_kredit)->format('d M Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal Disetujui</td>
                <td>:</td>
                <td>{{ !empty($debitur->tanggal_akad_kredit) ? \Carbon\Carbon::parse($debitur->tanggal_akad_kredit)->format('d M Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Catatan</td>
                <td>:</td>
                <td>{{ $debitur->alamat_domisili ?? $debitur->alamat_ktp ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jenis Jasa</td>
                <td>:</td>
                <td>{{ $debitur->suku_bunga ?? 'Flat' }}</td>
            </tr>
        </table>

        <table class="meta-table meta-right">
            <tr>
                <td>Jatuh Tempo Selanjutnya :</td>
                <td>{{ $summary['next_jatuh_tempo'] }}</td>
            </tr>
            <tr>
                <td>Plafon :</td>
                <td>Rp{{ number_format($summary['nominal_pinjaman'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Jasa Terhitung :</td>
                <td>Rp{{ number_format($summary['total_jasa_terhitung'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Denda :</td>
                <td>Rp{{ number_format($summary['total_denda'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Utang Berjalan :</td>
                <td>Rp{{ number_format($summary['total_utang_berjalan'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Uang Masuk :</td>
                <td>Rp{{ number_format($summary['total_paid'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Sisa Pokok Utang :</td>
                <td>Rp{{ number_format($summary['remaining_balance'], 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <table class="schedule">
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">TANGGAL TAGIHAN</th>
                <th rowspan="2">JATUH TEMPO</th>
                <th rowspan="2">TELAT (HARI)</th>
                <th colspan="3">TAGIHAN (RP)</th>
                <th colspan="3">BAYAR (RP)</th>
                <th rowspan="2">KETERANGAN</th>
                <th rowspan="2">STATUS</th>
            </tr>
            <tr>
                <th>POKOK</th>
                <th>JASA</th>
                <th>TOTAL</th>
                <th>POKOK</th>
                <th>JASA</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwal_angsuran as $item)
                <tr>
                    <td class="center">{{ $item['urutan'] }}</td>
                    <td class="center">{{ $item['tgl_tagihan'] }}</td>
                    <td class="center">{{ $item['tgl_jatuh_tempo'] }}</td>
                    <td class="center">{{ $item['hari_telat'] > 0 ? $item['hari_telat'] : '-' }}</td>
                    <td class="num">{{ number_format($item['pokok'], 2, ',', '.') }}</td>
                    <td class="num">{{ number_format($item['jasa'], 2, ',', '.') }}</td>
                    <td class="num">{{ number_format($item['total_angsuran'], 2, ',', '.') }}</td>
                    <td class="num">{{ number_format($item['pokok_dibayarkan'], 2, ',', '.') }}</td>
                    <td class="num">{{ number_format($item['jasa_dibayarkan'], 2, ',', '.') }}</td>
                    <td class="num">{{ number_format($item['nominal_bayar'], 2, ',', '.') }}</td>
                    <td>{{ $item['has_payment'] ? 'Waktu bayar: ' . $item['tgl_bayar'] : '-' }}</td>
                    <td class="center status">
                        @if($item['is_lunas'])
                            Lunas
                        @elseif($item['is_sebagian'])
                            Sebagian
                        @else
                            Belum Lunas
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">1/1</div>
</body>
</html>
