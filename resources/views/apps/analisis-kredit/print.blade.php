<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memo Persetujuan Pembiayaan - {{ $analisa->debitur->nama_peminjam ?? '-' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px 8px;
            vertical-align: top;
        }
        .header-table td {
            width: 25%;
        }
        .label-cell {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 13px;
        }
        .approval-table td {
            height: 80px;
            width: 50%;
        }
        .approval-role {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .checkbox-fake {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 5px;
            vertical-align: middle;
            text-align: center;
            line-height: 12px;
            font-size: 10px;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
        .print-button {
            display: block;
            width: 150px;
            margin: 0 auto 20px auto;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            border: none;
        }
        .doc-header {
            position: relative;
            margin-bottom: 20px;
            text-align: center;
        }
        .doc-header .logo {
            position: absolute;
            top: -40px;
            right: 0;
            margin-bottom: 10px;
            width: 70px;
            height: auto;
        }
        .doc-header h2 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .doc-header p {
            margin: 2px 0 0 0;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <button class="no-print print-button" onclick="window.print()">Cetak Dokumen</button>

    <div class="doc-header">
        <img src="{{ asset('assets/images/logo-nsb.png') }}" alt="Logo NSB" class="logo">
        <h2>Memo Persetujuan Pembiayaan</h2>
        <p>Koperasi Nawasena Sanggah Bersama</p>
    </div>

    <table class="header-table">
        <tr>
            <td class="label-cell">Nama Debitur</td>
            <td>{{ $analisa->debitur->nama_peminjam ?? '-' }}</td>
            <td class="label-cell">No. MPP</td>
            <td>{{ $analisa->nomor_mpp ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Jenis Usaha / Produk</td>
            <td>{{ $analisa->jenis_produk ?? '-' }}</td>
            <td class="label-cell">Tanggal Laporan</td>
            <td>{{ $analisa->tanggal_mpp ? $analisa->tanggal_mpp->format('d M Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Alamat Domisili</td>
            <td colspan="3">{{ $analisa->debitur->alamat_domisili ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Nama Marketing</td>
            <td>{{ $analisa->debitur->marketing->name ?? '-' }}</td>
            <td class="label-cell">Nama SPV</td>
            <td>{{ $analisa->debitur->spv->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Nama Cabang</td>
            <td>{{ $analisa->debitur->cabang->nama_cabang ?? '-' }}</td>
            <td class="label-cell">IIR (MAX 40%)</td>
            <td>{{ $analisa->nilai_iir ? number_format($analisa->nilai_iir, 2) . '%' : '-' }}</td>
        </tr>
    </table>

    <div class="section-title">Fasilitas Pembiayaan</div>
    <table>
        <thead>
            <tr class="label-cell text-center">
                <th>Jenis Fasilitas</th>
                <th>Plafon</th>
                <th>Jasa Per Bulan (%)</th>
                <th>Jangka Waktu</th>
                <th>Angsuran Per bulan</th>
            </tr>
        </thead>
        <tbody>
            <tr class="text-center">
                <td>{{ $analisa->jenis_fasilitas ?? '-' }}</td>
                <td>{{ number_format($analisa->pengajuan_plafond ?? 0, 0, ',', '.') }}</td>
                <td>{{ $analisa->bunga_persen ?? 0 }}%</td>
                <td>{{ $analisa->debitur->tenor_bulan ?? '-' }} Bulan</td>
                <td>{{ number_format($analisa->total_angsuran_pengajuan ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @php
        $usulanPlafond = $analisa->usulan_plafond ?: $analisa->pengajuan_plafond;
        $saldoBpjs = $analisa->saldo_bpjs ?: 0;
        $nilaiCoverage = ($usulanPlafond > 0 && $saldoBpjs > 0) ? ($saldoBpjs / $usulanPlafond) * 100 : 0;
    @endphp

    <div class="section-title">Jaminan Fasilitas</div>
    <table>
        <thead>
            <tr class="label-cell text-center">
                <th>Jaminan</th>
                <th>Kode Jaminan</th>
                <th>Saldo BPJS</th>
                <th>Nilai LTV / Likuidasi</th>
                <th>Nilai Coverage (Min 100%)</th>
            </tr>
        </thead>
        <tbody>
            <tr class="text-center">
                <td>{{ $analisa->jaminan ?? '-' }}</td>
                <td>{{ $analisa->kode_jaminan ?? '-' }}</td>
                <td>{{ number_format($saldoBpjs, 0, ',', '.') }}</td>
                <td>{{ $analisa->nilai_ltv ? number_format($analisa->nilai_ltv, 2) . '%' : '-' }}</td>
                <td>{{ number_format($nilaiCoverage, 2) }}%</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Rekomendasi & Persetujuan</div>
    <table class="approval-table">
        <!-- Marketing & SPV -->
        <tr>
            <td>
                <span class="approval-role">Lending Officer (Marketing)</span>
                <div>
                    <span class="checkbox-fake">{{ $analisa->marketing_status == 'Setuju' ? 'X' : '' }}</span> Setuju
                    &nbsp;&nbsp;
                    <span class="checkbox-fake">{{ $analisa->marketing_status == 'Tidak Setuju' ? 'X' : '' }}</span> Tidak Setuju
                </div>
                <div style="margin-top: 10px;">
                    <strong>Catatan:</strong><br>
                    {{ $analisa->marketing_note ?? '-' }}
                </div>
                <div style="margin-top: 20px; border-top: 1px solid #000; padding-top: 5px; text-align: center;">
                    <br><br><br>
                    <span>( {{ $analisa->marketing_approved_by ?? '........................' }} )</span>
                </div>
            </td>
            <td>
                <span class="approval-role">SPV</span>
                <div>
                    <span class="checkbox-fake">{{ $analisa->spv_status == 'Setuju' ? 'X' : '' }}</span> Setuju
                    &nbsp;&nbsp;
                    <span class="checkbox-fake">{{ $analisa->spv_status == 'Tidak Setuju' ? 'X' : '' }}</span> Tidak Setuju
                </div>
                <div style="margin-top: 10px;">
                    <strong>Catatan:</strong><br>
                    {{ $analisa->spv_note ?? '-' }}
                </div>
                <div style="margin-top: 20px; border-top: 1px solid #000; padding-top: 5px; text-align: center;">
                    <br><br><br>
                    <span>( {{ $analisa->spv_approved_by ?? '........................' }} )</span>
                </div>
            </td>
        </tr>
        <!-- Analis & KadivBisnis -->
        <tr>
            <td>
                <span class="approval-role">Area Credit Analis</span>
                <div>
                    <span class="checkbox-fake">{{ $analisa->analis_status == 'Setuju' ? 'X' : '' }}</span> Setuju
                    &nbsp;&nbsp;
                    <span class="checkbox-fake">{{ $analisa->analis_status == 'Tidak Setuju' ? 'X' : '' }}</span> Tidak Setuju
                </div>
                <div style="margin-top: 10px;">
                    <strong>Catatan:</strong><br>
                    {{ $analisa->analis_note ?? '-' }}
                </div>
                <div style="margin-top: 20px; border-top: 1px solid #000; padding-top: 5px; text-align: center;">
                    <br><br><br>
                    <span>( {{ $analisa->analis_approved_by ?? '........................' }} )</span>
                </div>
            </td>
            <td>
                <span class="approval-role">Kadiv Bisnis</span>
                <div>
                    <span class="checkbox-fake">{{ $analisa->kadivbisnis_status == 'Setuju' ? 'X' : '' }}</span> Setuju
                    &nbsp;&nbsp;
                    <span class="checkbox-fake">{{ $analisa->kadivbisnis_status == 'Tidak Setuju' ? 'X' : '' }}</span> Tidak Setuju
                </div>
                <div style="margin-top: 10px;">
                    <strong>Catatan:</strong><br>
                    {{ $analisa->kadivbisnis_note ?? '-' }}
                </div>
                <div style="margin-top: 20px; border-top: 1px solid #000; padding-top: 5px; text-align: center;">
                    <br><br><br>
                    <span>( {{ $analisa->kadivbisnis_approved_by ?? '........................' }} )</span>
                </div>
            </td>
        </tr>
        <!-- Pengelola -->
        <tr>
            <td>
                <span class="approval-role">Pengelola</span>
                <div>
                    <span class="checkbox-fake">{{ $analisa->pengelola_status == 'Setuju' ? 'X' : '' }}</span> Setuju
                    &nbsp;&nbsp;
                    <span class="checkbox-fake">{{ $analisa->pengelola_status == 'Tidak Setuju' ? 'X' : '' }}</span> Tidak Setuju
                </div>
                <div style="margin-top: 10px;">
                    <strong>Catatan:</strong><br>
                    {{ $analisa->pengelola_note ?? '-' }}
                </div>
                <div style="margin-top: 20px; border-top: 1px solid #000; padding-top: 5px; text-align: center;">
                    <br><br><br>
                    <span>( {{ $analisa->pengelola_approved_by ?? '........................' }} )</span>
                </div>
            </td>
            <td>
                &nbsp;
            </td>
        </tr>
    </table>

    <script>
        // Auto print on load
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
