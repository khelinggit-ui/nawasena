@extends('layouts.master')
@section('title', 'Debitur | NSB')
@section('content')
    <!-- Page-content -->
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Debitur</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Debitur</a></li>
                            <li class="breadcrumb-item active">Debitur</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-12 col-lg-8">
                <div>
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="row g-4">
                                <div class="col-sm-auto">
                                    <div class="d-flex gap-2">
                                        @if(auth()->user() && auth()->user()->isMarketing())
                                        <a href="{{ route('debitur.create') }}" class="btn btn-success"><i class="ri-add-line align-bottom me-1"></i> Add Debitur</a>
                                        @endif
                                        
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="ri-file-download-line align-bottom me-1"></i> Export
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="javascript:void(0);" onclick="exportDebitur('current')"><i class="ri-file-text-line align-bottom me-1 text-muted"></i> Export Current Tab (CSV)</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);" onclick="exportDebitur('all')"><i class="ri-file-excel-line align-bottom me-1 text-muted"></i> Export All Data (CSV)</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="d-flex justify-content-sm-end">
                                        <div class="search-box ms-2">
                                            <input type="text" class="form-control" id="searchDebiturList" placeholder="Search Debitur...">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col">
                                    <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#debiturnav-all" role="tab">
                                                All <span class="badge bg-danger-subtle text-danger align-middle rounded-pill ms-1"></span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#debiturnav-pending" role="tab">
                                                Pending <span class="badge bg-danger-subtle text-danger align-middle rounded-pill ms-1"></span>
                                            </a>
                                        </li>
                                        @if(!(auth()->user() && auth()->user()->isMitra()))
                                        <li class="nav-item">
                                            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#debiturnav-revisi" role="tab">
                                                Revisi <span class="badge bg-danger-subtle text-danger align-middle rounded-pill ms-1"></span>
                                            </a>
                                        </li>
                                        @endif
                                        <li class="nav-item">
                                            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#debiturnav-pending-mitra" role="tab">
                                                Pending Mitra <span class="badge bg-dark-subtle text-dark align-middle rounded-pill ms-1"></span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#debiturnav-reject" role="tab">
                                                Reject <span class="badge bg-danger-subtle text-danger align-middle rounded-pill ms-1"></span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-auto">
                                    <div id="selection-element">
                                        <div class="my-n1 d-flex align-items-center text-muted">
                                            Select <div id="select-content" class="text-body fw-semibold px-1"></div> Result 
                                            @can('delete debitur')
                                            <button type="button" class="btn btn-link link-danger p-0 ms-3 material-shadow-none" data-bs-toggle="modal" data-bs-target="#removeItemModal">Remove</button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">

                            <div class="tab-content text-muted">
                                <div class="tab-pane active" id="debiturnav-all" role="tabpanel">
                                    <div id="table-debitur-list-all" class="table-card gridjs-border-none"></div>
                                </div>
                                <!-- end tab pane -->
                                <div class="tab-pane" id="debiturnav-pending" role="tabpanel">
                                    <div id="table-debitur-list-pending" class="table-card gridjs-border-none"></div>
                                </div>
                                <!-- end tab pane -->

                                @if(!(auth()->user() && auth()->user()->isMitra()))
                                <div class="tab-pane" id="debiturnav-revisi" role="tabpanel">
                                    <div id="table-debitur-list-revisi" class="table-card gridjs-border-none"></div>
                                </div>
                                <!-- end tab pane -->
                                @endif

                                <div class="tab-pane" id="debiturnav-pending-mitra" role="tabpanel">
                                    <div class="mb-3 d-flex gap-2">
                                        @can('download excel mitra')
                                        <a href="{{ route('debitur.export.mitra') }}" class="btn btn-success" id="btn-download-mitra">
                                            <i class="ri-download-2-line align-bottom me-1"></i> Download Excel
                                        </a>
                                        @endcan
                                    </div>
                                    <div id="table-debitur-list-pending-mitra" class="table-card gridjs-border-none"></div>
                                </div>
                                <!-- end tab pane -->

                                <div class="tab-pane" id="debiturnav-reject" role="tabpanel">
                                    <div id="table-debitur-list-reject" class="table-card gridjs-border-none"></div>
                                </div>
                                <!-- end tab pane -->
                            </div>
                            <!-- end tab content -->

                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->

        <!-- removeItemModal -->
        <div id="removeItemModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mt-2 text-center">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                            <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                <h4>Are you sure ?</h4>
                                <p class="text-muted mx-4 mb-0">Are you sure you want to remove this product ?</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                            <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn w-sm btn-danger " id="delete-debitur">Yes, Delete It!</button>
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
        
    </div>
    <!-- End Page-content -->
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
        </script>
        <!-- nouisliderribute js -->
        <script src="{{ asset('assets/libs/nouislider/nouislider.min.js') }}"></script>
        <script src="{{ asset('assets/libs/wnumb/wNumb.min.js') }}"></script>
        <!-- gridjs js -->
        <script src="{{ asset('assets/libs/gridjs/gridjs.umd.js') }}"></script>
        <script src="https://unpkg.com/gridjs/plugins/selection/dist/selection.umd.js"></script>
        <!-- ecommerce debitur list -->
        <script src="{{ asset('assets/js/pages/ecommerce-debitur-list.init.js') }}"></script>

    @endsection
@endsection
