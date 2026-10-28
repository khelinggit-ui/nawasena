@extends('layouts.master')

@section('title', 'Laporan Nominatif | NSB')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Laporan Nominatif</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">Laporan</li>
                            <li class="breadcrumb-item active">Laporan Nominatif</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-column gap-3">
                <h5 class="card-title mb-0">Debitur Aktif</h5>
                <div class="d-flex flex-wrap justify-content-end align-items-center gap-2">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="{{ route('debitur.laporan.nominatif.export') }}" class="btn btn-success">
                            <i class="ri-file-excel-2-line align-bottom me-1"></i> Export Excel
                        </a>
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="ri-printer-line align-bottom me-1"></i> Cetak
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle table-nowrap mb-0" id="nominativeTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama Debitur</th>
                                <th>No. KTP</th>
                                <th>No. Kontrak</th>
                                <th>Nomor Debitur</th>
                                <th>Cabang</th>
                                <th>Unit</th>
                                <th>Marketing</th>
                                <th class="text-end">Plafon</th>
                                <th class="text-end">Total Bunga</th>
                                <th class="text-end">Denda</th>
                                <th class="text-end">Outstanding</th>
                                <th class="text-center">Hari Telat</th>
                                <th class="text-end">Angsuran Masuk</th>
                                <th class="text-center">Tenor</th>
                                <th class="text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($debiturs as $index => $debitur)
                                @php
                                    $loan = $debitur['debitur'];
                                    $summary = $debitur['summary'];
                                    $totalBunga = (float) ($loan->nominal_pinjaman ?? 0)
                                        * ((float) ($loan->bungapersen ?? 0) / 100)
                                        * max((int) ($loan->tenor_bulan ?? 1), 1);
                                    $outstanding = max(
                                        $summary['total_utang_berjalan'] - $summary['total_paid'] + $summary['total_denda'],
                                        0
                                    );
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $loan->nama_peminjam ?? $loan->nama_debitur ?? '-' }}</td>
                                    <td>{{ $loan->nomor_ktp ?? '-' }}</td>
                                    <td>{{ $loan->no_kontrak ?? '-' }}</td>
                                    <td>{{ $loan->nomor_debitur ?? '-' }}</td>
                                    <td>{{ optional($loan->cabang)->nama_cabang ?? '-' }}</td>
                                    <td>{{ optional($loan->kantor)->nama_kantor ?? '-' }}</td>
                                    <td>{{ optional($loan->marketing)->name ?? '-' }}</td>
                                    <td class="text-end">Rp{{ number_format((float) ($loan->nominal_pinjaman ?? 0), 0, ',', '.') }}</td>
                                    <td class="text-end">Rp{{ number_format($totalBunga, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp{{ number_format($summary['total_denda'], 0, ',', '.') }}</td>
                                    <td class="text-end">Rp{{ number_format($outstanding, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ number_format($summary['total_hari_telat'], 0, ',', '.') }}</td>
                                    <td class="text-end">Rp{{ number_format($summary['total_paid'], 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $loan->tenor_bulan ?? 0 }} Bulan</td>
                                    <td class="text-center">
                                        <a href="{{ route('debitur.details', ['id' => $loan->id]) }}" class="btn btn-sm btn-soft-primary">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        #nominativeTable_wrapper .dataTables_length,
        #nominativeTable_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }

        @media print {
            .navbar-menu,
            .app-menu,
            .page-title-right,
            .card-header,
            #nominativeTable_wrapper .row:first-child,
            #nominativeTable_wrapper .row:last-child,
            .card-body td:last-child,
            .card thead th:last-child,
            .card-body > .d-flex:last-child {
                display: none !important;
            }

            .main-content,
            .page-content,
            .container-fluid,
            .card,
            .card-body {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
        }
    </style>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (!window.jQuery || !$.fn.DataTable) {
                console.error('DataTables gagal dimuat pada laporan nominatif.');
                return;
            }

            $('#nominativeTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [],
                language: {
                    search: 'Pencarian:',
                    lengthMenu: 'Tampilkan _MENU_ data per halaman',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    zeroRecords: 'Data tidak ditemukan',
                    emptyTable: 'Tidak ada debitur aktif yang ditemukan.',
                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir',
                        next: 'Berikutnya',
                        previous: 'Sebelumnya'
                    }
                }
            });
        });
    </script>
@endsection
