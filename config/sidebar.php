<?php

return [
    'menu' => [
        [
            'title'  => 'Dashboards',
            'can'    => 'menu dashboard',
            'icon'   => 'ri-dashboard-2-line',
            'id'     => 'sidebarDashboards',
            'key'    => 't-dashboards',
            'active' => ['home', 'dashboard*','summary*','summary.spv*'], 
            'submenu' => [
                ['route' => 'home', 'label' => 'Pencapaian', 'key' => 't-pencapaian', 'can' => 'page pencapaian'],
                ['route' => 'summary', 'label' => 'Summary Marketing', 'key' => 't-summary', 'can' => 'page summary'],
                ['route' => 'summary.spv', 'label' => 'Summary SPV', 'key' => 't-summary-spv', 'can' => 'page summary spv'],
            ]
        ],
        [
            'title'  => 'Apps',
            'can'    => 'page data debitur',
            'icon'   => 'ri-apps-2-line',
            'id'     => 'sidebarApps',
            'key'    => 't-apps',
            'active' => ['apps-ecommerce*', 'apps-debitur*', 'debitur-approve*'],
            'submenu' => [
                ['route' => 'debitur.index', 'label' => 'Pengajuan Debitur','key' => 't-debitur', 'can' => 'page data debitur'],
                ['route' => 'debitur.approved', 'label' => 'Debitur Approve','key' => 't-debitur-approved', 'can' => 'page debitur approve'],
                ['route' => 'debitur.tracking', 'label' => 'Tracking Pengajuan', 'key' => 't-debitur-tracking', 'can' => 'page data debitur'],
            ]
        ],
        [
            'title'  => 'Analis Kredit',
            'can'    => 'menu analis',
            'icon'   => 'ri-file-chart-line',
            'id'     => 'sidebarDataAnalis',
            'key'    => 't-data-analis',
            'active' => ['analisis-kredit*'],
            'submenu' => [
                ['route' => 'analisis-kredit.index', 'label' => 'Data Analis', 'key' => 't-data-analis', 'can' => 'page analis'],
            ]
        ],
        // [
        //     'title'  => 'Laporan',
        //     'can'    => 'page analis kredit',
        //     'icon'   => 'ri-file-text-line',
        //     'id'     => 'sidebarLaporan',
        //     'key'    => 't-laporan',
        //     'active' => ['analisis-kredit*'],
        //     'submenu' => [
        //         ['route' => 'analisis-kredit.index', 'label' => 'Form Pembiayaan', 'key' => 't-form-pembiayaan', 'can' => 'page analis kredit'],
        //     ]
        //         ],
        [
            'title'  => 'Laporan',
            'icon'   => 'ri-file-list-3-line',
            'id'     => 'sidebarLaporan',
            'key'    => 't-laporan',
            'active' => ['apps-ecommerce/debitur-laporan-nominatif'],
            'submenu' => [
                ['route' => 'debitur.laporan.nominatif', 'label' => 'Laporan Nominatif', 'key' => 't-laporan-nominatif', 'can' => 'page laporan nominatif'],
            ]
        ],
        [
            'title'  => 'Master Data',
            'can'    => 'page cabang',
            'icon'   => 'ri-database-2-line',
            'id'     => 'sidebarMasterData',
            'key'    => 't-master-data',
            'active' => ['cabangs*', 'perusahaan*', 'jabatans*', 'parameter-produk*', 'kantors*'],
            'submenu' => [
                ['route' => 'cabangs.index', 'label' => 'Data Cabang', 'key' => 't-cabang', 'can' => 'page cabang'],
                ['route' => 'perusahaan.index', 'label' => 'Data Perusahaan', 'key' => 't-perusahaan', 'can' => 'page cabang'], // Using page cabang so it shares Master Data permission for visibility
                ['route' => 'jabatans.index', 'label' => 'Data Jabatan', 'key' => 't-jabatan', 'can' => 'page cabang'], // Uses same strategy
                ['route' => 'parameter-produk.index', 'label' => 'Parameter Produk', 'key' => 't-parameter-produk', 'can' => 'page parameter produk'],
                ['route' => 'kantors.index', 'label' => 'Data Kantor', 'key' => 't-kantor', 'can' => 'page kantor'],
            ]
        ],
        [
            'title'  => 'WhatsApp API',
            'can'    => 'menu whatsapp api',
            'icon'   => 'ri-whatsapp-line',
            'id'     => 'sidebarWhatsapp',
            'key'    => 't-whatsapp',
            'active' => ['whatsapp*'],
            'submenu' => [
                ['route' => 'whatsapp.index', 'label' => 'Instance WhatsApp', 'key' => 't-whatsapp-instance', 'can' => 'page whatsapp instance'],
                ['route' => 'whatsapp.bot.dashboard', 'label' => 'Dashboard Bot', 'key' => 't-whatsapp-dashboard', 'can' => 'page whatsapp bot dashboard'],
                ['route' => 'whatsapp.bot.index', 'label' => 'Pengaturan Bot', 'key' => 't-whatsapp-bot', 'can' => 'page whatsapp bot setting'],
                ['route' => 'whatsapp.bot.templates', 'label' => 'Template Pesan', 'key' => 't-whatsapp-template', 'can' => 'page whatsapp template'],
                ['route' => 'whatsapp.bot.logs', 'label' => 'Riwayat Pengingat', 'key' => 't-whatsapp-log', 'can' => 'page whatsapp log'],
                ['route' => 'whatsapp.bot.opt-outs', 'label' => 'Blacklist Nomor', 'key' => 't-whatsapp-optout', 'can' => 'page whatsapp opt out'],
            ]
        ],
        [
            'title'  => 'User Management',
            'can'    => 'menu management user',
            'icon'   => 'ri-shield-user-line',
            'id'     => 'sidebarUserManagement',
            'key'    => 't-user-management',
            'active' => ['users*', 'roles*', 'permissions*'],
            'submenu' => [
                ['route' => 'users.index', 'label' => 'Users', 'key' => 't-users', 'can' => 'page users'],
                ['route' => 'roles.index', 'label' => 'Roles', 'key' => 't-roles', 'can' => 'page roles'],
                ['route' => 'permissions.index', 'label' => 'Permissions', 'key' => 't-permissions', 'can' => 'page permission'],
            ]
        ]
    ]
];