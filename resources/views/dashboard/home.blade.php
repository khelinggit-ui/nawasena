@extends('layouts.master')
@section('title', 'Home Dashboard')
@section('content')
    <!-- Page-content -->
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="h-100">
                    <div class="row mb-3 pb-1">
                        <div class="col-12">
                            <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                <div class="flex-grow-1">
                                    <h4 class="fs-16 mb-1">Good Morning, {{ Auth::user()->name }}</h4>
                                    <p class="text-muted mb-0">Here's what happened on your dashboard today for branch <strong>{{ $hasCabang ? $userCabang : 'Semua Cabang' }}</strong>.</p>
                                </div>
                                <div class="mt-3 mt-lg-0">
                                    <form method="GET" action="{{ route('home') }}">
                                        <div class="row g-3 mb-0 align-items-center">
                                            @if(!$hasCabang)
                                            <div class="col-sm-auto">
                                                <select name="cabang_id" id="filterCabangGrafik" class="form-select border-0 shadow" onchange="this.form.submit()">
                                                    <option value="">Semua Cabang</option>
                                                    @foreach($listCabang as $cb)
                                                        <option value="{{ $cb->id }}" {{ (isset($filterCabang) ? $filterCabang : '') == $cb->id ? 'selected' : '' }}>{{ $cb->nama_cabang }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @endif
                                            <div class="col-sm-auto">
                                                <select name="tahun" id="filterTahunGrafik" class="form-select border-0 shadow" onchange="this.form.submit()">
                                                    @foreach($listTahun as $th)
                                                        <option value="{{ $th }}" {{ (isset($filterTahun) ? $filterTahun : now()->year) == $th ? 'selected' : '' }}>{{ $th }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-auto">
                                                <div class="input-group">
                                                    <input type="text" class="form-control border-0 minimal-border dash-filter-picker shadow" id="dash-filter-picker" data-provider="flatpickr" data-range-date="true" data-date-format="d M, Y" placeholder="Pilih Tanggal">
                                                    <div class="input-group-text bg-primary border-primary text-white">
                                                        <i class="ri-calendar-2-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- <div class="col-auto">
                                                <button type="button" class="btn btn-soft-info btn-icon waves-effect material-shadow-none waves-light layout-rightside-btn"><i class="ri-pulse-line"></i></button>
                                            </div> --}}
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- end card header -->
                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->
                    <div class="row">
                         @if(!$hasCabang)
                        <div class="col-xl-3 col-md-6">
                            <!-- card -->
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total All Plafond</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <h5 class="text-success fs-14 mb-0">
                                                <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +16.24 %
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">Rp <span class="counter-value" data-target="{{ $totalAllPlafond }}">0</span></h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                            <i class="bx bx-dollar-circle text-success"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        @endif
                        @if(!$hasCabang)
                        <div class="col-xl-3 col-md-6">
                            <!-- card -->
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total All Debitur</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <h5 class="text-success fs-14 mb-0">
                                                <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +29.08 %
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $totalAllDebitur }}">0</span></h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                            <i class="bx bx-user-circle text-warning"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        @endif
                        @if(!$hasCabang)
                        <div class="col-xl-3 col-md-6">
                            <!-- card -->
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total All Pembayaran</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <h5 class="text-danger fs-14 mb-0">
                                                <i class="ri-arrow-right-down-line fs-13 align-middle"></i> -3.57 %
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">Rp <span class="counter-value" data-target="{{ $totalAllPembayaran }}">0</span></h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                            <i class="bx bx-shopping-bag text-info"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        @endif
                        @if(!$hasCabang)
                         <div class="col-xl-3 col-md-6">
                            <!-- card -->
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">All Outstanding</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <h5 class="text-muted fs-14 mb-0">
                                                +0.00 %
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">Rp <span class="counter-value" data-target="{{ $totalAllOutstanding }}">0</span></h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                            <i class="bx bx-wallet text-primary"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        @endif
                        <!-- end col -->
                        <div class="col-xl-3 col-md-6">
                            <!-- card -->
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0"> Total Plafond berjalan</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <h5 class="text-success fs-14 mb-0">
                                                <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +16.24 %
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">Rp <span class="counter-value" data-target="{{ $totalPlafond }}">0</span></h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                            <i class="bx bx-dollar-circle text-success"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                        <div class="col-xl-3 col-md-6">
                            <!-- card -->
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Pembayaran Berjalan</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <h5 class="text-danger fs-14 mb-0">
                                                <i class="ri-arrow-right-down-line fs-13 align-middle"></i> -3.57 %
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">Rp <span class="counter-value" data-target="{{ $totalPembayaran }}">0</span></h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-info-subtle rounded fs-3">
                                            <i class="bx bx-shopping-bag text-info"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                        <div class="col-xl-3 col-md-6">
                            <!-- card -->
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total Debitur Berjalan</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <h5 class="text-success fs-14 mb-0">
                                                <i class="ri-arrow-right-up-line fs-13 align-middle"></i> +29.08 %
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="{{ $totalDebitur }}">0</span></h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                            <i class="bx bx-user-circle text-warning"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                        <div class="col-xl-3 col-md-6">
                            <!-- card -->
                            <div class="card card-animate">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Outstanding Berjalan</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <h5 class="text-muted fs-14 mb-0">
                                                +0.00 %
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mt-4">
                                        <div>
                                            <h4 class="fs-22 fw-semibold ff-secondary mb-4">Rp <span class="counter-value" data-target="{{ $outstanding }}">0</span></h4>
                                        </div>
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title bg-primary-subtle rounded fs-3">
                                            <i class="bx bx-wallet text-primary"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row-->
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header border-0 align-items-center d-flex flex-wrap gap-2">
                                    <h4 class="card-title mb-0 flex-grow-1" id="grafik-card-title">Grafik Pencapaian</h4>
                                </div>
                                <!-- end card header -->
                                <div class="card-header p-0 border-0 bg-light-subtle">
                                    <div class="row g-0 text-center">
                                        <div class="col-6 col-sm-3">
                                            <div class="p-3 border border-dashed border-start-0">
                                                <h5 class="mb-1"><span id="grafik-total-debitur">0</span></h5>
                                                <p class="text-muted mb-0">Total Debitur</p>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-6 col-sm-3">
                                            <div class="p-3 border border-dashed border-start-0">
                                                <h5 class="mb-1">Rp <span id="grafik-total-plafond">0</span></h5>
                                                <p class="text-muted mb-0">Total Plafond</p>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-6 col-sm-3">
                                            <div class="p-3 border border-dashed border-start-0">
                                                <h5 class="mb-1"><span class="counter-value" data-target="{{ $totalPembayaran }}">0</span></h5>
                                                <p class="text-muted mb-0">Total Pembayaran</p>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-6 col-sm-3">
                                            <div class="p-3 border border-dashed border-start-0 border-end-0">
                                                <h5 class="mb-1 text-success"><span id="grafik-rasio">0</span>%</h5>
                                                <p class="text-muted mb-0">Rasio Bayar</p>
                                            </div>
                                        </div>
                                        <!--end col-->
                                    </div>
                                </div>
                                <!-- end card header -->
                                <div class="card-body p-0 pb-2">
                                    <div class="w-100">
                                        <div id="grafik-pencapaian-chart" style="min-height:300px;" dir="ltr"></div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                    </div>
                    
                    <!-- end row-->
                    <div class="row">
                        <div class="col-xl-4">
                            <div class="card card-height-100">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Pencapaian Per Cabang</h4>
                                </div>
                                <!-- end card header -->
                                <div class="card-body">
                                    <div id="grafik-cabang-chart" class="apex-charts" dir="ltr"></div>
                                </div>
                            </div>
                            <!-- .card-->
                        </div>
                        <!-- .col-->
                        <div class="col-xl-8">
                            <div class="card">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Pencapaian Permarketing</h4>
                                    <div class="flex-shrink-0">
                                        <button type="button" class="btn btn-soft-info btn-sm material-shadow-none">
                                        <i class="ri-file-list-3-line align-middle"></i> Generate Report
                                        </button>
                                    </div>
                                </div>
                                <!-- end card header -->
                                <div class="card-body">
                                    <div class="table-responsive table-card">
                                        <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                                            <thead class="text-muted table-light">
                                                <tr>
                                                    <th scope="col">Nama Marketing</th>
                                                    <th scope="col">Cabang</th>
                                                    <th scope="col">Jumlah Debitur</th>
                                                    <th scope="col">Jumlah Plafond</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($pencapaianMarketing as $marketing)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-shrink-0 me-2">
                                                                <div class="avatar-xs">
                                                                    <div class="avatar-title bg-light rounded-circle text-primary material-shadow">
                                                                        {{ strtoupper(substr($marketing->marketing_name ?? '?', 0, 1)) }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="flex-grow-1">{{ $marketing->marketing_name ?? 'Tanpa Marketing' }}</div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $marketing->cabang_name ?? 'Tanpa Cabang' }}</td>
                                                    <td>
                                                        <span class="badge bg-info-subtle text-info">{{ number_format($marketing->jumlah_debitur, 0, ',', '.') }} Debitur</span>
                                                    </td>
                                                    <td>
                                                        <span class="text-success">Rp {{ number_format($marketing->total_plafond, 0, ',', '.') }}</span>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">Belum ada data pencapaian marketing</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                            <!-- end tbody -->
                                        </table>
                                        <!-- end table -->
                                    </div>
                                </div>
                            </div>
                            <!-- .card-->
                        </div>
                        <!-- .col-->
                    </div>
                    <!-- end row-->
                </div>
                <!-- end .h-100-->
            </div>
            <!-- end col -->
        </div>
    </div>
    <!-- End Page-content -->
    @section('script')
        <!-- Dashboard init -->
        <script src="{{ asset('assets/js/pages/dashboard-ecommerce.init.js') }}"></script>

        <script>
        (function() {
            var grafikChart = null;
            var apiUrl = "{{ route('home.grafik-pencapaian') }}";

            function formatRupiah(num) {
                if (num >= 1000000) return (num / 1000000).toFixed(1) + ' Jt';
                if (num >= 1000) return (num / 1000).toFixed(1) + ' Rb';
                return num;
            }

            function loadGrafik() {
                var cabangEl = document.getElementById('filterCabangGrafik');
                var tahunEl = document.getElementById('filterTahunGrafik');
                
                var queryParams = [];
                if (cabangEl && cabangEl.value) queryParams.push('cabang_id=' + encodeURIComponent(cabangEl.value));
                if (tahunEl && tahunEl.value) queryParams.push('tahun=' + encodeURIComponent(tahunEl.value));

                var url = apiUrl + (queryParams.length > 0 ? '?' + queryParams.join('&') : '');
                
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        var totalDebitur = data.totalDebitur.reduce(function(a, b) { return parseInt(a) + parseInt(b); }, 0);
                        var totalPlafond = data.totalPlafond.reduce(function(a, b) { return parseInt(a) + parseInt(b); }, 0);

                        document.getElementById('grafik-total-debitur').textContent = totalDebitur.toLocaleString('id-ID');
                        document.getElementById('grafik-total-plafond').textContent = Number(totalPlafond).toLocaleString('id-ID');
                        document.getElementById('grafik-rasio').textContent = totalDebitur > 0 ? (totalPlafond / totalDebitur / 1000000).toFixed(1) : '0';

                        var tahun = data.tahun || new Date().getFullYear();

                        // Update card title with current year
                        var cardEl = document.getElementById('grafik-pencapaian-chart');
                        if (cardEl) {
                            var titleEl = cardEl.closest('.card').querySelector('.card-title');
                            if (titleEl) titleEl.textContent = 'Grafik Pencapaian ' + tahun;
                        }

                        var options = {
                            series: [
                                { name: 'Total Plafond (Rp)', type: 'area', data: data.totalPlafond.map(Number) },
                                { name: 'Total Debitur', type: 'bar', data: data.totalDebitur.map(Number) }
                            ],
                            chart: {
                                type: 'line',
                                height: 300,
                                toolbar: { show: false },
                                animations: { enabled: true }
                            },
                            stroke: { width: [2, 0], curve: 'smooth' },
                            fill: {
                                opacity: [0.15, 1],
                                type: ['gradient', 'solid']
                            },
                            colors: ['#0ab39c', '#405189'],
                            dataLabels: { enabled: false },
                            xaxis: {
                                categories: data.labels,
                                title: {
                                    text: 'Bulan (Tahun ' + tahun + ')',
                                    style: { fontSize: '11px', fontWeight: 500 }
                                },
                                labels: {
                                    rotate: -30,
                                    style: { fontSize: '11px' }
                                }
                            },
                            yaxis: [
                                { title: { text: 'Plafond (Rp)' }, labels: { formatter: function(v) { return formatRupiah(v); } } },
                                { opposite: true, title: { text: 'Debitur' } }
                            ],
                            tooltip: {
                                y: [
                                    { formatter: function(v) { return 'Rp ' + Number(v).toLocaleString('id-ID'); } },
                                    { formatter: function(v) { return v + ' debitur'; } }
                                ]
                            },
                            legend: { position: 'top' },
                            grid: { borderColor: '#f1f1f1' }
                        };

                        if (grafikChart) {
                            grafikChart.updateOptions(options);
                        } else {
                            grafikChart = new ApexCharts(document.getElementById('grafik-pencapaian-chart'), options);
                            grafikChart.render();
                        }
                    });
            }

            var apiCabangUrl = "{{ route('home.grafik-pencapaian-cabang') }}";
            var grafikCabangChart = null;

            function loadGrafikCabang() {
                var cabangEl = document.getElementById('filterCabangGrafik');
                var tahunEl = document.getElementById('filterTahunGrafik');
                
                var queryParams = [];
                if (cabangEl && cabangEl.value) queryParams.push('cabang_id=' + encodeURIComponent(cabangEl.value));
                if (tahunEl && tahunEl.value) queryParams.push('tahun=' + encodeURIComponent(tahunEl.value));

                var url = apiCabangUrl + (queryParams.length > 0 ? '?' + queryParams.join('&') : '');
                
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if(data.series.length === 0) {
                            document.getElementById('grafik-cabang-chart').innerHTML = '<div class="text-center mt-4">Data tidak tersedia</div>';
                            return;
                        }

                        var themeColors = ["#405189", "#0ab39c", "#f7b84b", "#f06548", "#299cdb"];

                        var options = {
                            series: data.series.map(Number),
                            labels: data.labels,
                            chart: {
                                type: 'donut',
                                height: 333
                            },
                            legend: { position: 'bottom' },
                            stroke: { show: false },
                            dataLabels: { dropShadow: { enabled: false } },
                            colors: themeColors,
                            tooltip: {
                                y: {
                                    formatter: function(val) {
                                        return 'Rp ' + Number(val).toLocaleString('id-ID');
                                    }
                                }
                            }
                        };

                        if (grafikCabangChart) {
                            grafikCabangChart.updateOptions(options);
                        } else {
                            document.getElementById('grafik-cabang-chart').innerHTML = '';
                            grafikCabangChart = new ApexCharts(document.getElementById('grafik-cabang-chart'), options);
                            grafikCabangChart.render();
                        }
                    });
            }

            // Load on page ready
            loadGrafik();
            loadGrafikCabang();

            // Filter reloads page (handled by HTML form onchange submit)
        })();
        </script>
    @endsection
@endsection
