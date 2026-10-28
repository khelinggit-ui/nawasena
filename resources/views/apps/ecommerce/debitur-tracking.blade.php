@extends('layouts.master')
@section('title', 'Tracking Pengajuan | NSB')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                    <h4 class="mb-sm-0">Tracking Pengajuan Debitur</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Debitur</a></li>
                            <li class="breadcrumb-item active">Tracking</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0">
                        <h5 class="card-title mb-0">Daftar Pengajuan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0" id="trackingTable">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Nama Debitur</th>
                                        <th scope="col">No. KTP</th>
                                        <th scope="col">Tgl Pengajuan</th>
                                        <th scope="col">Nama Cabang</th>
                                        <th scope="col">Nama Perusahaan</th>
                                        <th scope="col">Marketing</th>
                                        <th scope="col">SPV</th>
                                        <th scope="col">Status Saat Ini</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Timeline -->
    <div class="modal fade" id="timelineModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tracking History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="timelineContainer" class="p-3">
                        <!-- Timeline items will be generated here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('styles')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endsection

@section('script')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
let trackingData = [];

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/\"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function showTimeline(histories, nama) {
    console.log('showTimeline called with:', { histories, nama });
    document.querySelector('#timelineModal .modal-title').textContent = 'Tracking History: ' + nama;
    const container = document.getElementById('timelineContainer');
    container.innerHTML = '';

    if(!histories || histories.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">Belum ada history.</p>';
    } else {
        let ul = document.createElement('ul');
        ul.className = 'list-group list-group-flush border-dashed';

        histories.forEach(h => {
            let date = h.created_at ? new Date(h.created_at).toLocaleString('id-ID') : '-';
            let statusTahap = (h.status_tahap || '-').replace(/_/g, ' ');
            let approvalName = (h.user?.name) ? h.user.name : '-';
            let li = document.createElement('li');
            li.className = 'list-group-item px-0';
            li.innerHTML = `
                <div class="d-flex">
                    <div class="flex-shrink-0 avatar-xs">
                        <div class="avatar-title bg-light text-primary rounded-circle fs-16">
                            <i class="ri-checkbox-circle-fill"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 text-uppercase">${statusTahap}</h6>
                        <p class="text-muted mb-2"><strong>Approval:</strong> ${escapeHtml(approvalName)}</p>
                        <p class="text-muted mb-0">${h.catatan ?? '-'}</p>
                        <small class="text-muted">${date}</small>
                    </div>
                </div>
            `;
            ul.appendChild(li);
        });
        container.appendChild(ul);
    }

    try {
        var myModal = new bootstrap.Modal(document.getElementById('timelineModal'));
        myModal.show();
        console.log('Modal shown successfully');
    } catch(e) {
        console.error('Error showing modal:', e);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('#trackingTable tbody');

    function getBadgeClass(status) {
        if (!status) return 'bg-secondary';
        if (status.startsWith('approve')) return 'bg-success';
        if (status.startsWith('reject')) return 'bg-danger';
        if (status.startsWith('revisi')) return 'bg-info';
        return 'bg-warning';
    }

    function renderRows(data) {
        tbody.innerHTML = '';

        if (!Array.isArray(data) || !data.length) {
            return;
        }

        data.forEach(item => {
            const status = item.status_tahap || 'belum_diatur';
            const statusLabel = status.replace(/_/g, ' ');
            const badgeClass = getBadgeClass(status);
            const marketingName = item.marketing ? item.marketing.name : '-';
            const spvName = item.spv ? item.spv.name : '-';
            const cabangName = item.cabang ? item.cabang.nama_cabang : '-';
            const perusahaanName = item.perusahaan || '-';
            const detailUrl = `/apps-ecommerce/debitur-details?id=${item.id}`;
            const tanggalPengajuan = item.created_at
                ? new Date(item.created_at).toLocaleDateString('id-ID')
                : '-';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${escapeHtml(item.nama_peminjam || '-')}</td>
                <td>${escapeHtml(item.nomor_ktp || '-')}</td>
                <td data-order="${escapeHtml(item.created_at || '')}">${escapeHtml(tanggalPengajuan)}</td>
                <td>${escapeHtml(cabangName)}</td>
                <td>${escapeHtml(perusahaanName)}</td>
                <td>${escapeHtml(marketingName)}</td>
                <td>${escapeHtml(spvName)}</td>
                <td><span class="badge ${badgeClass} text-uppercase">${escapeHtml(statusLabel)}</span></td>
                <td>
                    <div class="d-flex gap-2">
                        <a href="${detailUrl}" class="btn btn-sm btn-info">
                            <i class="ri-eye-line align-bottom me-1"></i> Detail
                        </a>
                        <button type="button" class="btn btn-sm btn-primary btn-show-timeline" data-id="${item.id}">
                            <i class="ri-history-line align-bottom me-1"></i> Lihat Proses
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    tbody.addEventListener('click', function (event) {
        const btn = event.target.closest('.btn-show-timeline');
        if (!btn) return;

        const id = Number(btn.getAttribute('data-id'));
        const row = trackingData.find(item => Number(item.id) === id);
        if (!row) return;

        showTimeline(row.histories || [], row.nama_peminjam || '-');
    });

    function initDataTable() {
        if (window.jQuery && $.fn.DataTable) {
            if ($.fn.DataTable.isDataTable('#trackingTable')) {
                $('#trackingTable').DataTable().destroy();
            }

            $('#trackingTable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[2, 'desc']],
                language: {
                    search: 'Pencarian:',
                    lengthMenu: 'Tampilkan _MENU_ data per halaman',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data',
                    zeroRecords: 'Data tidak ditemukan',
                    emptyTable: 'Belum ada data pengajuan yang sedang berjalan.',
                    paginate: {
                        first: 'Pertama',
                        last: 'Terakhir',
                        next: 'Berikutnya',
                        previous: 'Sebelumnya'
                    }
                }
            });
        }
    }

    fetch("{{ route('debitur.tracking.data') }}")
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal memuat data tracking.');
            }
            return response.json();
        })
        .then(data => {
            trackingData = Array.isArray(data) ? data : [];
            renderRows(trackingData);
            initDataTable();
        })
        .catch(error => {
            console.error(error);
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger py-4">Gagal memuat data tracking.</td></tr>';
        });
});
</script>
@endsection
