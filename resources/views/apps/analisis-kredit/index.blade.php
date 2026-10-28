@extends('layouts.master')
@section('title', 'Data Analis Kredit | NSB')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Data Analis Kredit</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Data Analis</a></li>
                        <li class="breadcrumb-item active">Analisis Kredit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Daftar Analisis Kredit</h4>
                </div>
                <div class="card-body">
                    <div id="table-analisis-kredit" class="table-card gridjs-border-none"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/libs/gridjs/gridjs.umd.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @php
            $user = auth()->user();
            $jabatan = $user->jabatan ? strtolower($user->jabatan->nama_jabatan) : '';
            $isAnalis = strtolower($user->level) === 'analis' || strpos($jabatan, 'analis') !== false || (method_exists($user, 'isAnalis') && $user->isAnalis());
            
            $controller = new \App\Http\Controllers\AnalisisKreditController();
            $roleType = $controller->getUserRoleTypePublic();
        @endphp
        const isAnalis = @json($isAnalis);
        const userRoleType = @json($roleType);
        const userCanApprove = @json(auth()->user()->can('page debitur approve'));
        const userCanViewAnalisa = @json(auth()->user()->can('page analis detail'));

        fetch('{{ route("analisis-kredit.data") }}')
            .then(res => res.json())
            .then(data => {
                const rows = data.map((item, index) => {
                    const debiturApproved = !!item.is_debitur_approved;
                    const debiturStatus = item.debitur_status || 'Pending';
                    const debiturStatusLower = String(debiturStatus).toLowerCase();
                    let debiturStatusBadgeClass = 'bg-warning-subtle text-warning';
                    if (debiturStatusLower.startsWith('approve')) {
                        debiturStatusBadgeClass = 'bg-success-subtle text-success';
                    } else if (debiturStatusLower.startsWith('reject')) {
                        debiturStatusBadgeClass = 'bg-danger-subtle text-danger';
                    } else if (debiturStatusLower.startsWith('revisi')) {
                        debiturStatusBadgeClass = 'bg-info-subtle text-info';
                    }

                    let isAllApproved = item.marketing_status === 'Setuju' && 
                                        item.spv_status === 'Setuju' && 
                                        item.analis_status === 'Setuju' && 
                                        item.kadivbisnis_status === 'Setuju' && 
                                        item.pengelola_status === 'Setuju';

                    // Check if current user's role has already approved
                    let currentRoleStatus = null;
                    if (userRoleType === 'marketing') currentRoleStatus = item.marketing_status;
                    else if (userRoleType === 'spv') currentRoleStatus = item.spv_status;
                    else if (userRoleType === 'analis') currentRoleStatus = item.analis_status;
                    else if (userRoleType === 'kadivbisnis') currentRoleStatus = item.kadivbisnis_status;
                    else if (userRoleType === 'pengelola') currentRoleStatus = item.pengelola_status;
                    let currentRoleApproved = currentRoleStatus === 'Setuju';

                    let statusHtml = `<div>Marketing: ${item.marketing_status || 'Pending'}</div>`
                        + `<div>SPV: ${item.spv_status || 'Pending'}</div>`
                        + `<div>Analis: ${item.analis_status || 'Pending'}</div>`
                        + `<div>Kadiv: ${item.kadivbisnis_status || 'Pending'}</div>`
                        + `<div>Pengelola: ${item.pengelola_status || 'Pending'}</div>`
                        + `<div class="mt-2">Status Debitur: <span class="badge ${debiturStatusBadgeClass} text-uppercase">${debiturStatus.replace(/_/g, ' ')}</span></div>`;

                    let actions = `<div class="d-flex gap-2">`;
                    if (isAnalis) {
                        actions += `<a href="/apps-ecommerce/analisis-kredit/${item.id}/edit" class="btn btn-sm btn-info"><i class="ri-pencil-fill align-bottom"></i> Edit Data</a>`;
                    }
                    if (userCanViewAnalisa) {
                        actions += `<a href="/apps-ecommerce/analisis-kredit/${item.id}/view" class="btn btn-sm btn-primary"><i class="ri-eye-fill align-bottom"></i> View</a>`;
                    }
                    if (isAllApproved) {
                        actions += `<a href="/apps-ecommerce/analisis-kredit/${item.id}/print" class="btn btn-sm btn-secondary" target="_blank"><i class="ri-printer-fill align-bottom"></i> Cetak Form</a>`;
                    }
                    if (debiturApproved && userRoleType && userCanApprove && !currentRoleApproved) {
                        actions += `<a href="/apps-ecommerce/analisis-kredit/${item.id}/persetujuan" class="btn btn-sm btn-success"><i class="ri-check-double-line align-bottom"></i> Persetujuan</a>`;
                    } else if (debiturApproved && userRoleType && userCanApprove && currentRoleApproved) {
                        actions += `<a href="/apps-ecommerce/analisis-kredit/${item.id}/persetujuan" class="btn btn-sm btn-warning"><i class="ri-pencil-fill align-bottom"></i> Edit Persetujuan</a>`;
                    }
                    actions += `</div>`;

                    return [index + 1, item.nama_debitur, item.nomor_mpp || '-', item.tanggal_mpp, statusHtml, actions];
                });

                new gridjs.Grid({
                    columns: [
                        { name: 'No', width: '60px' },
                        { name: 'Nama Debitur', width: '200px' },
                        { name: 'Nomor MPP', width: '150px' },
                        { name: 'Tanggal MPP', width: '140px' },
                        { name: 'Status Persetujuan', width: '220px', formatter: (cell) => gridjs.html(cell) },
                        { name: 'Action', width: '250px', formatter: (cell) => gridjs.html(cell) }
                    ],
                    data: rows,
                    search: true,
                    pagination: { limit: 10 },
                    sort: true,
                    language: {
                        search: { placeholder: 'Cari...' },
                        pagination: { previous: 'Sebelumnya', next: 'Selanjutnya', showing: 'Menampilkan', results: 'data' }
                    }
                }).render(document.getElementById('table-analisis-kredit'));
            });
    });
</script>
@endsection
