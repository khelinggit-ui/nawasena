// debitur list
var debiturListAllData = [];
var debiturTableAll;
var debiturVerifikasiMap = {};
var debiturInfoMap = {};

function formatDateOnly(dateTimeStr) {
    if (!dateTimeStr || dateTimeStr === '-') return '-';

    const match = String(dateTimeStr).match(/^(\d{4})-(\d{2})-(\d{2})/);
    return match ? `${match[3]}-${match[2]}-${match[1]}` : dateTimeStr;
}

async function loadApprovedSummary() {
    if (window.debiturPageMode !== 'approved-only' || !window.debiturSummaryUrl) return;

    try {
        const response = await fetch(window.debiturSummaryUrl, { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error('Ringkasan debitur gagal dimuat.');
        const summary = await response.json();
        const numberFormat = new Intl.NumberFormat('id-ID');
        const currencyFormat = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 });
        const values = {
            'approved-summary-total': numberFormat.format(summary.jumlah_approve || 0),
            'approved-summary-active': numberFormat.format(summary.jumlah_aktif || 0),
            'approved-summary-paid': numberFormat.format(summary.jumlah_lunas || 0),
            'approved-summary-overdue': numberFormat.format(summary.jumlah_menunggak || 0),
            'approved-summary-paid-amount': 'Rp ' + currencyFormat.format(summary.nominal_lunas || 0),
            'approved-summary-payment-amount': 'Rp ' + currencyFormat.format(summary.nominal_angsuran_masuk || 0),
            'approved-summary-overdue-amount': 'Rp ' + currencyFormat.format(summary.nominal_tunggakan || 0)
        };

        Object.entries(values).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) element.textContent = value;
        });
    } catch (error) {
        console.error('Error loading approved debtor summary:', error);
    }
}

async function loadDebiturData() {
    try {
        const response = await fetch(window.debiturDataUrl || '/apps-ecommerce/debitur-data');
        const data = await response.json();
        debiturListAllData = data.map((item, index) => ({
            id: index + 1,
            model_id: item.id || '',
            nama: item.nama_peminjam,
            nomor_debitur: item.nomor_debitur || '-',
            nik: item.nomor_ktp,
            created_at: item.created_at ? formatDateOnly(item.created_at) : '-',
            updated_at: item.updated_at ? formatDateOnly(item.updated_at) : '-',
            batch: item.batch || '-',
            marketing: item.marketing || '-',
            nama_cabang: item.nama_cabang || '-',
            unit: item.unit || '-',
            email: item.email || '-',
            nomor_handphone: item.nomor_handphone || '-',
            tenor_bulan: item.tenor_bulan || '-',
            spv: item.spv || '-',
            pengajuan_plafond: 'Rp ' + Number(item.pengajuan_plafond || 0).toLocaleString('id-ID'),
            nominal_pinjaman: 'Rp ' + Number(item.nominal_pinjaman || 0).toLocaleString('id-ID'),
            status_tahap: item.status_tahap || 'pending_spv',
            status_pinjaman: item.status_pinjaman || 'aktif',
            status_verifikasi: item.status_verifikasi_data || '',
            raw_pengajuan_plafond: item.pengajuan_plafond || 0,
            raw_nominal: item.nominal_pinjaman || 0
        }));
        debiturVerifikasiMap = {};
        debiturInfoMap = {};
        debiturListAllData.forEach(function(item) {
            debiturVerifikasiMap[item.model_id] = item.status_verifikasi;
            debiturInfoMap[item.model_id] = {
                nama: item.nama,
                pengajuan_plafond: item.raw_pengajuan_plafond,
                nominal_pinjaman: item.raw_nominal
            };
        });
        initDebiturList();
        loadApprovedSummary();
    } catch (error) {
        console.error('Error loading debitur data:', error);
    }
}

function getStatusBadge(status) {
    switch (status) {
        case 'pending_spv': return '<span class="badge bg-warning-subtle text-warning">Pending SPV</span>';
        case 'pending_adminkredit': return '<span class="badge bg-info-subtle text-info">Pending AdminKredit</span>';
        case 'pending_analis': return '<span class="badge bg-primary-subtle text-primary">Pending Analis</span>';
        case 'pending_risk': return '<span class="badge bg-secondary-subtle text-secondary">Pending Risk</span>';
        case 'pending_mitra': return '<span class="badge bg-dark-subtle text-dark">Pending Mitra</span>';
        case 'pending_debitur_dsr': return '<span class="badge bg-warning-subtle text-warning">Konfirmasi DSR</span>';
        case 'pending_debitur_takeout': return '<span class="badge bg-info-subtle text-info">Konfirmasi Take Out</span>';
        case 'approve':
        case 'approved': return '<span class="badge bg-success-subtle text-success">Approved</span>';
        case 'revisi': return '<span class="badge bg-danger-subtle text-danger">Revisi</span>';
        case 'revisi_spv': return '<span class="badge bg-danger-subtle text-danger">Revisi SPV</span>';
        case 'revisi_analis': return '<span class="badge bg-danger-subtle text-danger">Revisi Analis</span>';
        case 'revisi_risk': return '<span class="badge bg-danger-subtle text-danger">Revisi Risk</span>';
        case 'revisi_mitra': return '<span class="badge bg-danger-subtle text-danger">Revisi Mitra</span>';
        case 'reject': return '<span class="badge bg-danger">Rejected</span>';
        case 'reject_dsr': return '<span class="badge bg-danger">Reject DSR</span>';
        case 'reject_fdc': return '<span class="badge bg-danger">Reject FDC</span>';
        case 'reject_takeout': return '<span class="badge bg-danger">Reject Take Out</span>';
        default: return '<span class="badge bg-light text-dark">' + status + '</span>';
    }
}

function updateBadgeCounts() {
    const countAll = debiturListAllData.length;
    const countApproved = debiturListAllData.filter(item => item.status_tahap === 'approved' || item.status_tahap === 'approve').length;
    const countPending = debiturListAllData.filter(item => item.status_tahap && item.status_tahap.startsWith('pending') && item.status_tahap !== 'pending_mitra' && item.status_tahap !== 'pending_debitur_dsr' && item.status_tahap !== 'pending_debitur_takeout').length;
    const countRevisi = debiturListAllData.filter(item => item.status_tahap && item.status_tahap.startsWith('revisi')).length;
    const countPendingMitra = debiturListAllData.filter(item => item.status_tahap === 'pending_mitra' || item.status_tahap === 'pending_debitur_dsr' || item.status_tahap === 'pending_debitur_takeout').length;
    const countReject = debiturListAllData.filter(item => item.status_tahap === 'reject').length;
    const badges = document.querySelectorAll('.nav-tabs-custom .badge');
    const countMap = {
        '#debiturnav-all': countAll,
        '#debiturnav-approved': countApproved,
        '#debiturnav-pending': countPending,
        '#debiturnav-revisi': countRevisi,
        '#debiturnav-pending-mitra': countPendingMitra,
        '#debiturnav-reject': countReject,
    };

    badges.forEach((badge) => {
        const tabLink = badge.closest('a');
        const href = tabLink ? tabLink.getAttribute('href') : null;
        if (href && Object.prototype.hasOwnProperty.call(countMap, href)) {
            badge.textContent = countMap[href];
        }
    });
}

function getActionButtons(id, status) {
    let actions = `<div class="d-flex gap-2">`;
    const canActionTahap1 = typeof window.canActionTahap1 !== 'undefined' && window.canActionTahap1;
    const isAdminKredit = typeof window.isAdminKredit !== 'undefined' && window.isAdminKredit;
    const isAnalis = typeof window.isAnalis !== 'undefined' && window.isAnalis;
    const isRisk = typeof window.isRisk !== 'undefined' && window.isRisk;
    const isMitra = typeof window.isMitra !== 'undefined' && window.isMitra;
    const isMarketing = typeof window.isMarketing !== 'undefined' && window.isMarketing;

    if (status === 'approved' || status === 'approve' || status === 'reject') {
        actions += `<a href="/apps-ecommerce/debitur-details?id=${id}" class="btn btn-sm btn-info"><i class="ri-eye-fill align-bottom"></i> Detail</a>`;
    }

    if (status === 'pending_spv' && canActionTahap1) {
        actions += `<a href="/apps-ecommerce/debitur/${id}/edit-tahap1" class="btn btn-sm btn-success"><i class="ri-check-line align-bottom"></i> Review Tahap 1</a>`;
    } else if (status === 'pending_adminkredit' && isAdminKredit) {
        actions += `<a href="/apps-ecommerce/debitur/${id}/edit-tahap1" class="btn btn-sm btn-success"><i class="ri-check-line align-bottom"></i> Review</a>`;
        actions += `<a href="/apps-ecommerce/debitur/${id}/edit-tahap2" class="btn btn-sm btn-primary"><i class="ri-pencil-fill align-bottom"></i> Input data</a>`;
    } else if (status === 'pending_analis' && isAnalis) {
        actions += `<a href="/apps-ecommerce/debitur/${id}/edit-tahap3-analis" class="btn btn-sm btn-primary"><i class="ri-pencil-fill align-bottom"></i> Input Analis</a>`;
    } else if (status === 'pending_risk' && isRisk) {
        actions += `<a href="/apps-ecommerce/debitur/${id}/edit-tahap3-risk" class="btn btn-sm btn-primary"><i class="ri-pencil-fill align-bottom"></i> Input Risk</a>`;
    } else if (status === 'pending_mitra' && isMitra) {
        actions += `<a href="/apps-ecommerce/debitur/${id}/edit-tahap-mitra" class="btn btn-sm btn-dark"><i class="ri-check-double-line align-bottom"></i> Review Final</a>`;
    } else if (status === 'pending_debitur_dsr' && (debiturVerifikasiMap[id] === 'Debt Service Ratio (DSR)') && isMarketing) {
        actions += `<button onclick="confirmDsrPenurunan(${id})" class="btn btn-sm btn-warning"><i class="ri-check-line align-bottom"></i> Konfirmasi DSR</button>`;
    } else if (status === 'pending_debitur_takeout' && !isMitra) {
        actions += `<button onclick="confirmTakeOut(${id})" class="btn btn-sm btn-info"><i class="ri-check-line align-bottom"></i> Konfirmasi Take Out</button>`;
    }

    if (typeof status === 'string' && status.startsWith('revisi') && isMarketing) {
        actions += `<a href="/apps-ecommerce/debitur/${id}/edit" class="btn btn-sm btn-warning"><i class="ri-pencil-fill align-bottom"></i> Perbaiki Data</a>`;
    }

    if (actions === `<div class="d-flex gap-2">`) {
        return gridjs.html('-');
    }

    actions += `</div>`;
    return gridjs.html(actions);
}

function getDebiturColumns(handphoneLabel = 'Handphone') {
    const columns = [
        {
            name: 'No',
            width: '80px',
            formatter: (function (cell) {
                return gridjs.html('<span class="fw-semibold">' + cell + '</span>');
            })
        },
        {
            name: 'Nama Debitur',
            width: '200px'
        },
        {
            name: 'NIK',
            width: '150px'
        },
        {
            name: 'Nomor Debitur',
            width: '160px'
        },
        {
            name: 'Tgl Dibuat',
            width: '150px'
        },
        {
            name: 'Tgl Update',
            width: '150px'
        },
        {
            name: 'Batch',
            width: '120px'
        },
        {
            name: 'Marketing',
            width: '160px'
        },
        {
            name: 'Cabang',
            width: '160px'
        },
        {
            name: 'Unit',
            width: '120px'
        },
        {
            name: 'Email',
            width: '200px'
        },
        {
            name: handphoneLabel,
            width: '150px'
        },
        {
            name: 'Tenor',
            width: '100px'
        },
        {
            name: 'SPV',
            width: '150px'
        },
        {
            name: 'RP Pengajuan',
            width: '140px'
        },
        {
            name: 'RP Disetujui',
            width: '120px'
        },
        {
            name: 'Status',
            width: '150px',
            formatter: (function (cell) {
                return gridjs.html(getStatusBadge(cell));
            })
        },
        ...(window.debiturPageMode === 'approved-only' ? [{
            name: 'Status Pinjaman',
            width: '140px',
            formatter: (function (cell) {
                const label = cell === 'lunas' ? 'Lunas' : 'Aktif';
                const badgeClass = cell === 'lunas' ? 'bg-info-subtle text-info' : 'bg-success-subtle text-success';
                return gridjs.html(`<span class="badge ${badgeClass}">${label}</span>`);
            })
        }] : []),
        {
            name: 'Action',
            width: '100px',
            formatter: (function (cell, row) {
                return getActionButtons(cell, row.cells[16].data);
            })
        }
    ];

    return columns;
}

function toDebiturRow(item) {
    const row = [
        item.id,
        item.nama,
        item.nik,
        item.nomor_debitur,
        item.created_at,
        item.updated_at,
        item.batch,
        item.marketing,
        item.nama_cabang,
        item.unit,
        item.email,
        item.nomor_handphone,
        item.tenor_bulan,
        item.spv,
        item.pengajuan_plafond,
        item.nominal_pinjaman,
        item.status_tahap
    ];

    if (window.debiturPageMode === 'approved-only') {
        row.push(item.status_pinjaman);
    }

    row.push(item.model_id);
    return row;
}

function initDebiturList() {
    if (window.debiturPageMode === 'approved-only' && document.getElementById("table-debitur-list-approved-only")) {
        const approvedTableContainer = document.getElementById("table-debitur-list-approved-only");
        debiturTableAll = new gridjs.Grid({
            columns: getDebiturColumns(),
            pagination: {
                limit: 10
            },
            sort: true,
            search: true,
            data: debiturListAllData.map(item => toDebiturRow(item))
        }).render(approvedTableContainer);

        const exportButton = document.getElementById('approved-debitur-export');
        const gridHead = approvedTableContainer.querySelector('.gridjs-head');
        const searchControl = gridHead && gridHead.querySelector('.gridjs-search');
        if (exportButton && gridHead && searchControl) {
            const toolbar = document.createElement('div');
            toolbar.className = 'approved-grid-toolbar';
            gridHead.insertBefore(toolbar, gridHead.firstChild);
            toolbar.append(searchControl, exportButton);
        }

        return;
    }

    // All Debitur Table
    debiturTableAll = new gridjs.Grid({
        columns: getDebiturColumns(),
        pagination: {
            limit: 10
        },
        sort: true,
        search: true,
        data: debiturListAllData.map(item => toDebiturRow(item))
    }).render(document.getElementById("table-debitur-list-all"));

    // Pending Debitur Table - only render if element exists (Mitra role only)
    if (document.getElementById("table-debitur-list-pending")) {
        new gridjs.Grid({
        columns: getDebiturColumns(),
        pagination: {
            limit: 10
        },
        sort: true,
        search: true,
        data: debiturListAllData
            .filter(item => item.status_tahap && item.status_tahap.startsWith('pending') && item.status_tahap !== 'pending_mitra' && item.status_tahap !== 'pending_debitur_dsr' && item.status_tahap !== 'pending_debitur_takeout')
            .map(item => toDebiturRow(item))
    }).render(document.getElementById("table-debitur-list-pending"));
    }

    // Pending Mitra Debitur Table
    new gridjs.Grid({
        columns: getDebiturColumns('No. Handphone'),
        pagination: {
            limit: 10
        },
        sort: true,
        search: true,
        data: debiturListAllData
            .filter(item => item.status_tahap === 'pending_mitra' || item.status_tahap === 'pending_debitur_dsr' || item.status_tahap === 'pending_debitur_takeout')
            .map(item => toDebiturRow(item))
    }).render(document.getElementById("table-debitur-list-pending-mitra"));

    // Revisi Debitur Table - only render if element exists (Mitra role only)
    if (document.getElementById("table-debitur-list-revisi")) {
        new gridjs.Grid({
        columns: getDebiturColumns(),
        pagination: {
            limit: 10
        },
        sort: true,
        search: true,
        data: debiturListAllData
            .filter(item => item.status_tahap && item.status_tahap.startsWith('revisi'))
            .map(item => toDebiturRow(item))
    }).render(document.getElementById("table-debitur-list-revisi"));
    }

    // Reject Debitur Table - only render if element exists (Mitra role only)
    if (document.getElementById("table-debitur-list-reject")) {
        new gridjs.Grid({
        columns: getDebiturColumns(),
        pagination: {
            limit: 10
        },
        sort: true,
        search: true,
        data: debiturListAllData
            .filter(item => item.status_tahap === 'reject')
            .map(item => toDebiturRow(item))
    }).render(document.getElementById("table-debitur-list-reject"));
    }

    // Custom Search Functionality
    var searchDebiturList = document.getElementById("searchDebiturList");
    if (searchDebiturList) {
        searchDebiturList.addEventListener("keyup", function () {
            var keyword = searchDebiturList.value.toLowerCase();
            var filteredData = debiturListAllData.filter(function (item) {
                var searchPattern = keyword;
                return (
                    (item.nama && item.nama.toLowerCase().includes(searchPattern)) ||
                    (item.nomor_debitur && item.nomor_debitur.toLowerCase().includes(searchPattern)) ||
                    (item.nik && item.nik.toLowerCase().includes(searchPattern)) ||
                    (item.batch && item.batch.toLowerCase().includes(searchPattern)) ||
                    (item.marketing && item.marketing.toLowerCase().includes(searchPattern)) ||
                    (item.nama_cabang && item.nama_cabang.toLowerCase().includes(searchPattern)) ||
                    (item.unit && item.unit.toLowerCase().includes(searchPattern)) ||
                    (item.email && item.email.toLowerCase().includes(searchPattern)) ||
                    (item.nomor_handphone && item.nomor_handphone.toLowerCase().includes(searchPattern)) ||
                    (item.tenor_bulan && item.tenor_bulan.toString().includes(searchPattern)) ||
                    (item.spv && item.spv.toLowerCase().includes(searchPattern))
                );
            });
            debiturTableAll.updateConfig({
                data: filteredData.map(item => toDebiturRow(item))
            }).forceRender();
        });
    }

    // Update badge counts
    updateBadgeCounts();
}

// Load data on page load
document.addEventListener('DOMContentLoaded', loadDebiturData);

function postConfirm(url, data, modal) {
    var csrfToken = window.csrfToken || (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    data['_token'] = csrfToken;
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify(data)
    })
    .then(function(res) { return res.json(); })
    .then(function(result) {
        modal.hide();
        if (result.success) {
            Swal && Swal.fire ? Swal.fire('Berhasil!', result.message, 'success').then(function() { location.reload(); }) : (alert(result.message), location.reload());
        } else {
            alert(result.message || 'Terjadi kesalahan.');
        }
    })
    .catch(function(err) {
        modal.hide();
        alert('Terjadi kesalahan koneksi.');
        console.error(err);
    });
}

function confirmDsrPenurunan(debiturId) {
    var info = debiturInfoMap[debiturId] || {};
    var nama = info.nama || '-';
    var pengajuanAwal = info.pengajuan_plafond ? 'Rp ' + parseInt(info.pengajuan_plafond).toLocaleString('id-ID') : '-';
    var nominalBaru = info.nominal_pinjaman ? 'Rp ' + parseInt(info.nominal_pinjaman).toLocaleString('id-ID') : '-';

    var existing = document.getElementById('modalKonfirmasiDsr');
    if (existing) existing.remove();

    document.body.insertAdjacentHTML('beforeend', [
        '<div class="modal fade" id="modalKonfirmasiDsr" tabindex="-1" aria-hidden="true">',
        '  <div class="modal-dialog modal-dialog-centered">',
        '    <div class="modal-content">',
        '      <div class="modal-header">',
        '        <h5 class="modal-title"><i class="ri-percent-line me-2 text-warning"></i>Konfirmasi DSR - Penurunan Plafond</h5>',
        '        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>',
        '      </div>',
        '      <div class="modal-body">',
        '        <p>Hasil verifikasi debitur atas nama <strong>' + nama + '</strong>.</p>',
        '        <p>Pengajuan awal sebesar <strong>' + pengajuanAwal + '</strong>. Dengan adanya penurunan DSR, plafond yang disetujui sebesar <strong class="text-warning">' + nominalBaru + '</strong>.</p>',
        '        <p class="text-muted small mb-0">Apakah debitur menyetujui penurunan plafond ini?</p>',
        '      </div>',
        '      <div class="modal-footer">',
        '        <button type="button" class="btn btn-danger" id="btnDsrTolak"><i class="ri-close-line me-1"></i>Tidak Setuju</button>',
        '        <button type="button" class="btn btn-success" id="btnDsrSetuju"><i class="ri-check-line me-1"></i>Setuju</button>',
        '      </div>',
        '    </div>',
        '  </div>',
        '</div>'
    ].join(''));

    var modalEl = document.getElementById('modalKonfirmasiDsr');
    var modal = new bootstrap.Modal(modalEl);

    document.getElementById('btnDsrSetuju').addEventListener('click', function() {
        postConfirm('/apps-ecommerce/debitur/' + debiturId + '/confirm-dsr-penurunan', { confirm_dsr: '1' }, modal);
    });
    document.getElementById('btnDsrTolak').addEventListener('click', function() {
        postConfirm('/apps-ecommerce/debitur/' + debiturId + '/confirm-dsr-penurunan', { confirm_dsr: '0' }, modal);
    });

    modal.show();
}

function confirmTakeOut(debiturId) {
    var info = debiturInfoMap[debiturId] || {};
    var nama = info.nama || '-';

    var existing = document.getElementById('modalKonfirmasiTakeOut');
    if (existing) existing.remove();

    document.body.insertAdjacentHTML('beforeend', [
        '<div class="modal fade" id="modalKonfirmasiTakeOut" tabindex="-1" aria-hidden="true">',
        '  <div class="modal-dialog modal-dialog-centered">',
        '    <div class="modal-content">',
        '      <div class="modal-header">',
        '        <h5 class="modal-title"><i class="ri-restart-line me-2 text-info"></i>Konfirmasi Take Out</h5>',
        '        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>',
        '      </div>',
        '      <div class="modal-body">',
        '        <p>Apakah pinjaman atas nama <strong>' + nama + '</strong> yakin diajukan kembali?</p>',
        '      </div>',
        '      <div class="modal-footer">',
        '        <button type="button" class="btn btn-secondary" id="btnTakeOutTolak"><i class="ri-close-line me-1"></i>Tidak</button>',
        '        <button type="button" class="btn btn-info text-white" id="btnTakeOutSetuju"><i class="ri-check-line me-1"></i>Ya, Ajukan Kembali</button>',
        '      </div>',
        '    </div>',
        '  </div>',
        '</div>'
    ].join(''));

    var modalEl = document.getElementById('modalKonfirmasiTakeOut');
    var modal = new bootstrap.Modal(modalEl);

    document.getElementById('btnTakeOutSetuju').addEventListener('click', function() {
        postConfirm('/apps-ecommerce/debitur/' + debiturId + '/confirm-takeout', { confirm_takeout: '1' }, modal);
    });
    document.getElementById('btnTakeOutTolak').addEventListener('click', function() {
        postConfirm('/apps-ecommerce/debitur/' + debiturId + '/confirm-takeout', { confirm_takeout: '0' }, modal);
    });

    modal.show();
}

function getStatusText(status) {
    switch (status) {
        case 'pending_spv': return 'Pending SPV';
        case 'pending_adminkredit': return 'Pending AdminKredit';
        case 'pending_analis': return 'Pending Analis';
        case 'pending_risk': return 'Pending Risk';
        case 'pending_mitra': return 'Pending Mitra';
        case 'pending_debitur_dsr': return 'Konfirmasi DSR';
        case 'pending_debitur_takeout': return 'Konfirmasi Take Out';
        case 'approve':
        case 'approved': return 'Approved';
        case 'revisi': return 'Revisi';
        case 'revisi_spv': return 'Revisi SPV';
        case 'revisi_analis': return 'Revisi Analis';
        case 'revisi_risk': return 'Revisi Risk';
        case 'revisi_mitra': return 'Revisi Mitra';
        case 'reject': return 'Rejected';
        case 'reject_dsr': return 'Reject DSR';
        case 'reject_fdc': return 'Reject FDC';
        case 'reject_takeout': return 'Reject Take Out';
        default: return status || '';
    }
}

function escapeCSVValue(value) {
    if (value === null || value === undefined) {
        return '';
    }
    let stringValue = value.toString();
    if (stringValue.includes('"') || stringValue.includes(',') || stringValue.includes(';') || stringValue.includes('\n') || stringValue.includes('\r')) {
        stringValue = '"' + stringValue.replace(/"/g, '""') + '"';
    }
    return stringValue;
}

function exportDebitur(mode) {
    if (!debiturListAllData || debiturListAllData.length === 0) {
        alert('Tidak ada data untuk diexport.');
        return;
    }

    let dataToExport = [...debiturListAllData];

    // If mode is 'current', filter by active tab constraints and search keywords
    if (mode === 'current') {
        // 1. Get active tab
        const activeTabLink = document.querySelector('.nav-tabs-custom .nav-link.active');
        const activeTabHref = activeTabLink ? activeTabLink.getAttribute('href') : '#debiturnav-all';

        // 2. Filter by tab
        if (activeTabHref === '#debiturnav-pending') {
            dataToExport = dataToExport.filter(item => item.status_tahap && item.status_tahap.startsWith('pending') && item.status_tahap !== 'pending_mitra' && item.status_tahap !== 'pending_debitur_dsr' && item.status_tahap !== 'pending_debitur_takeout');
        } else if (activeTabHref === '#debiturnav-pending-mitra') {
            dataToExport = dataToExport.filter(item => item.status_tahap === 'pending_mitra' || item.status_tahap === 'pending_debitur_dsr' || item.status_tahap === 'pending_debitur_takeout');
        } else if (activeTabHref === '#debiturnav-revisi') {
            dataToExport = dataToExport.filter(item => item.status_tahap && item.status_tahap.startsWith('revisi'));
        } else if (activeTabHref === '#debiturnav-reject') {
            dataToExport = dataToExport.filter(item => item.status_tahap === 'reject');
        }

        // 3. Filter by search input
        const searchInput = document.getElementById("searchDebiturList");
        if (searchInput && searchInput.value) {
            const keyword = searchInput.value.toLowerCase();
            dataToExport = dataToExport.filter(function (item) {
                return (
                    (item.nama && item.nama.toLowerCase().includes(keyword)) ||
                    (item.nik && item.nik.toLowerCase().includes(keyword)) ||
                    (item.batch && item.batch.toLowerCase().includes(keyword)) ||
                    (item.marketing && item.marketing.toLowerCase().includes(keyword)) ||
                    (item.nama_cabang && item.nama_cabang.toLowerCase().includes(keyword)) ||
                    (item.unit && item.unit.toLowerCase().includes(keyword)) ||
                    (item.email && item.email.toLowerCase().includes(keyword)) ||
                    (item.nomor_handphone && item.nomor_handphone.toLowerCase().includes(keyword)) ||
                    (item.tenor_bulan && item.tenor_bulan.toString().includes(keyword)) ||
                    (item.spv && item.spv.toLowerCase().includes(keyword)) ||
                    (item.pengajuan_plafond && item.pengajuan_plafond.toString().includes(keyword)) ||
                    (item.nominal_pinjaman && item.nominal_pinjaman.toLowerCase().includes(keyword))
                );
            });
        }
    }

    if (dataToExport.length === 0) {
        alert('Tidak ada data yang cocok untuk diexport.');
        return;
    }

    // Build CSV Content
    const headers = [
        'No',
        'Nama Debitur',
        'NIK',
        'Tgl Dibuat',
        'Tgl Update',
        'Batch',
        'Marketing',
        'Cabang',
        'Unit',
        'Email',
        'Handphone',
        'Tenor (Bulan)',
        'SPV',
        'Rp Pengajuan',
        'Rp Disetujui',
        'Status'
    ];

    let csvContent = headers.map(escapeCSVValue).join(',') + '\r\n';

    dataToExport.forEach((item, index) => {
        const row = [
            index + 1,
            item.nama || '',
            // Prepend single quote or format NIK as string so Excel doesn't truncate or convert it to scientific notation
            item.nik ? "'" + item.nik : '',
            item.created_at || '',
            item.updated_at || '',
            item.batch || '',
            item.marketing || '',
            item.nama_cabang || '',
            item.unit || '',
            item.email || '',
            item.nomor_handphone ? "'" + item.nomor_handphone : '',
            item.tenor_bulan || '',
            item.spv || '',
            item.raw_pengajuan_plafond || '',
            item.raw_nominal || '',
            getStatusText(item.status_tahap)
        ];
        csvContent += row.map(escapeCSVValue).join(',') + '\r\n';
    });

    // Create Download Link
    const blob = new Blob(["\ufeff" + csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement("a");
    link.setAttribute("href", url);

    // Get current date string for file name
    const now = new Date();
    const dateString = now.getFullYear() + '-' + 
                       String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                       String(now.getDate()).padStart(2, '0') + '_' + 
                       String(now.getHours()).padStart(2, '0') + '-' + 
                       String(now.getMinutes()).padStart(2, '0');
    
    const filename = mode === 'current' ? `Debitur_Filtered_${dateString}.csv` : `Debitur_All_${dateString}.csv`;
    
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}