@extends('layouts.master')
@section('title', 'Debitur Approve | NSB')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Debitur Approve</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Debitur</a></li>
                            <li class="breadcrumb-item active">Debitur Approve</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card mb-0"><div class="card-body"><p class="text-muted mb-1">Jumlah Debitur Approve</p><h5 id="approved-summary-total" class="mb-0">-</h5></div></div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card mb-0"><div class="card-body"><p class="text-muted mb-1">Jumlah Debitur Aktif</p><h5 id="approved-summary-active" class="mb-0">-</h5></div></div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card mb-0"><div class="card-body"><p class="text-muted mb-1">Jumlah Debitur Lunas</p><h5 id="approved-summary-paid" class="mb-0">-</h5></div></div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card mb-0"><div class="card-body"><p class="text-muted mb-1">Jumlah Debitur Menunggak</p><h5 id="approved-summary-overdue" class="mb-0">-</h5></div></div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card mb-0"><div class="card-body"><p class="text-muted mb-1">Nominal Lunas</p><h5 id="approved-summary-paid-amount" class="mb-0">-</h5></div></div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card mb-0"><div class="card-body"><p class="text-muted mb-1">Nominal Angsuran Masuk</p><h5 id="approved-summary-payment-amount" class="mb-0">-</h5></div></div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="card mb-0"><div class="card-body"><p class="text-muted mb-1">Nominal Tunggakan</p><h5 id="approved-summary-overdue-amount" class="mb-0">-</h5></div></div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <a id="approved-debitur-export" href="{{ route('debitur.approved.export') }}" class="btn btn-success">
                            <i class="ri-file-excel-2-line align-bottom me-1"></i> Export Excel
                        </a>
                        <div id="table-debitur-list-approved-only" class="table-card gridjs-border-none approved-debitur-table"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('script')
        @php
            $canActionLanjutan = false;
            $isMarketing = false;
            $isAnalis = false;
            $user = auth()->user();
            if ($user) {
                $jabatan = $user->jabatan ? strtolower($user->jabatan->nama_jabatan) : '';
                $isMarketingStr = strtolower($user->level) === 'marketing'
                    || strpos($jabatan, 'marketing') !== false
                    || $user->isMarketing();
                $isMarketing = $isMarketingStr;

                $isMarketingOrSpv = in_array(strtolower($user->level), ['marketing', 'spv'])
                    || strpos($jabatan, 'marketing') !== false
                    || strpos($jabatan, 'spv') !== false
                    || $user->hasAnyRoleById([\App\Constants\RoleConstants::MARKETING_ID, \App\Constants\RoleConstants::SPV_ID]);
                $canActionLanjutan = !$isMarketingOrSpv;

                $isAnalisStr = strtolower($user->level) === 'analis'
                    || strpos($jabatan, 'analis') !== false
                    || $user->isAnalis();
                $isAnalis = $isAnalisStr;

                $isRiskStr = strtolower($user->level) === 'risk'
                    || strpos($jabatan, 'risk') !== false
                    || $user->isRisk();
                $isRisk = $isRiskStr;

                $isMitraStr = strtolower($user->level) === 'mitra'
                    || strpos($jabatan, 'mitra') !== false
                    || $user->isMitra();
                $isMitra = $isMitraStr;

                $normalizedLevel = str_replace(' ', '', strtolower($user->level));
                $normalizedJabatan = str_replace(' ', '', strtolower($jabatan));
                $isAdminKreditStr = in_array($normalizedLevel, ['admin', 'adminkredit', 'admin_kredit'])
                    || strpos($normalizedJabatan, 'adminkredit') !== false
                    || $user->hasAnyRoleById([\App\Constants\RoleConstants::ADMIN_ID, \App\Constants\RoleConstants::ADMINKREDIT_ID]);
                $isAdminKredit = $isAdminKreditStr;
            }
        @endphp
        <script>
            window.ASSET_URL = "{{ asset('') }}";
            window.canActionTahap1 = @json(auth()->user() ? auth()->user()->isSpv() : false);
            window.canActionLanjutan = @json($canActionLanjutan);
            window.isMarketing = @json($isMarketing);
            window.isAnalis = @json($isAnalis);
            window.isRisk = @json($isRisk);
            window.isMitra = @json($isMitra);
            window.isAdminKredit = @json($isAdminKredit);
            window.csrfToken = "{{ csrf_token() }}";
            window.debiturDataUrl = "{{ route('debitur.data', ['scope' => 'approved']) }}";
            window.debiturSummaryUrl = "{{ route('debitur.approved.summary') }}";
            window.debiturPageMode = 'approved-only';
        </script>
        <script src="{{ asset('assets/libs/nouislider/nouislider.min.js') }}"></script>
        <script src="{{ asset('assets/libs/wnumb/wNumb.min.js') }}"></script>
        <script src="{{ asset('assets/libs/gridjs/gridjs.umd.js') }}"></script>
        <script src="https://unpkg.com/gridjs/plugins/selection/dist/selection.umd.js"></script>
        <script src="{{ asset('assets/js/pages/ecommerce-debitur-list.init.js') }}"></script>
    @endsection
@endsection

@section('styles')
    <style>
        .approved-debitur-table .approved-grid-toolbar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            margin-bottom: 1rem;
        }

        .approved-debitur-table .approved-grid-toolbar .gridjs-search {
            float: none;
            flex: 1 1 280px;
            max-width: 360px;
            margin: 0;
        }

        .approved-debitur-table .approved-grid-toolbar .gridjs-search input {
            width: 100%;
        }

        .approved-debitur-table .approved-grid-toolbar > .btn {
            flex: 0 0 auto;
            margin-left: auto;
        }

        @media (max-width: 575.98px) {
            .approved-debitur-table .approved-grid-toolbar {
                align-items: flex-start;
            }

            .approved-debitur-table .approved-grid-toolbar .gridjs-search {
                flex-basis: 100%;
                max-width: none;
            }

            .approved-debitur-table .approved-grid-toolbar > .btn {
                margin-left: auto;
            }
        }
    </style>
@endsection