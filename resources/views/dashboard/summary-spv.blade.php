@extends('layouts.master')
@section('title', 'Summary Pencapaian SPV')

@section('content')
<div class="container-fluid">
    <div class="row mb-3 pb-1">
        <div class="col-12">
            <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                <div class="flex-grow-1">
                    <h4 class="fs-16 mb-1">Summary Pencapaian SPV</h4>
                    <p class="text-muted mb-0">Rincian pencapaian SPV beserta tim marketing di bawahnya, dipisahkan berdasarkan cabang.</p>
                </div>
            </div>
        </div>
    </div>

    @forelse($summaryData as $cabang => $dataCabang)
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header align-items-center d-flex bg-white border-bottom-dashed">
                    <h4 class="card-title fw-semibold mb-0 flex-grow-1 fs-15">
                        <i class="ri-store-2-line text-primary me-2 fs-18 align-middle"></i> Cabang: <span class="text-primary">{{ $cabang }}</span>
                    </h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive table-card">
                        <table class="table align-middle table-nowrap mb-0">
                            <thead class="text-muted table-light text-uppercase fs-12 fw-medium">
                                <tr>
                                    <th scope="col" style="width: 25%">Nama SPV</th>
                                    <th scope="col" style="width: 25%">Tim Marketing</th>
                                    <th scope="col" style="width: 20%">Jumlah Debitur</th>
                                    <th scope="col" style="width: 30%">Total Pencapaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalDebiturCabang = 0;
                                    $totalPencapaianCabang = 0;
                                @endphp
                                @foreach($dataCabang->groupBy('spv') as $spvName => $dataSpv)
                                    @php
                                        $spvNameDisplay = !empty($spvName) ? $spvName : 'Tanpa SPV';
                                        $totalDebiturSpv = 0;
                                        $totalPencapaianSpv = 0;
                                        $rowspan = $dataSpv->count();
                                    @endphp
                                    @foreach($dataSpv->values() as $index => $row)
                                        @php
                                            $totalDebiturSpv += $row->jumlah_debitur;
                                            $totalPencapaianSpv += $row->jumlah_pencapaian;
                                            $totalDebiturCabang += $row->jumlah_debitur;
                                            $totalPencapaianCabang += $row->jumlah_pencapaian;
                                        @endphp
                                        <tr>
                                            @if($index === 0)
                                            <td rowspan="{{ $rowspan }}" class="align-top border-end border-bottom bg-light bg-opacity-25">
                                                <div class="d-flex align-items-center mt-1">
                                                    <div class="avatar-xs me-2">
                                                        <div class="avatar-title rounded-circle bg-success-subtle text-success fw-bold text-uppercase fs-13">
                                                            {{ substr($spvNameDisplay, 0, 1) }}
                                                        </div>
                                                    </div>
                                                    <h6 class="fs-14 fw-semibold mb-0 text-dark">{{ $spvNameDisplay }}</h6>
                                                </div>
                                            </td>
                                            @endif
                                            <td>
                                                <span class="text-muted fw-medium fs-13"><i class="ri-user-2-fill text-muted align-middle me-1"></i> {{ !empty($row->marketing_name) ? $row->marketing_name : 'Tanpa Marketing' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info fs-12 border border-info border-opacity-25 px-2 py-1">{{ number_format($row->jumlah_debitur, 0, ',', '.') }} Debitur</span>
                                            </td>
                                            <td>
                                                <span class="text-success fw-bold fs-13">Rp {{ number_format($row->jumlah_pencapaian, 0, ',', '.') }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-light bg-opacity-50">
                                        <td colspan="2" class="text-end fw-bold text-dark border-bottom"><i class="ri-corner-down-right-line align-middle text-muted me-1 fs-16"></i> Total {{ $spvNameDisplay }}</td>
                                        <td class="fw-bold text-dark border-bottom">{{ number_format($totalDebiturSpv, 0, ',', '.') }} Debitur</td>
                                        <td class="text-primary fw-bold border-bottom">Rp {{ number_format($totalPencapaianSpv, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold text-dark text-uppercase fs-13">
                                <tr>
                                    <td colspan="2" class="text-end">Total Keseluruhan Cabang {{ $cabang }}</td>
                                    <td>{{ number_format($totalDebiturCabang, 0, ',', '.') }} Debitur</td>
                                    <td class="text-primary fs-14">Rp {{ number_format($totalPencapaianCabang, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info text-center">
                Belum ada data pencapaian SPV untuk ditampilkan.
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
