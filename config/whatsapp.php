<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Timezone pengiriman
    |--------------------------------------------------------------------------
    |
    | Semua perhitungan jadwal dan jendela pengiriman pengingat memakai
    | timezone ini, terlepas dari timezone aplikasi (config/app.php).
    |
    */

    'timezone' => env('WHATSAPP_TIMEZONE', 'Asia/Jakarta'),

    /*
    |--------------------------------------------------------------------------
    | Pengaturan pengingat
    |--------------------------------------------------------------------------
    */

    'reminders' => [
        'enabled' => (bool) env('WHATSAPP_REMINDERS_ENABLED', false),

        // Jendela pengiriman yang aman.
        'send_window' => [
            'start' => env('WHATSAPP_SEND_WINDOW_START', '09:00'),
            'end' => env('WHATSAPP_SEND_WINDOW_END', '16:00'),
        ],

        // Batas volume per nomor/instance.
        'max_per_day' => (int) env('WHATSAPP_REMINDER_MAX_PER_DAY', 50),
        'max_per_day_hard_limit' => 200,

        // Jeda antar pesan dalam detik (acak di antara nilai ini).
        'delay_seconds' => [
            'min' => 20,
            'max' => 30,
        ],

        // Aturan pengingat default. Offset negatif = sebelum jatuh tempo.
        'rules' => [
            'H-3' => ['offset_days' => -3, 'label' => 'Pengingat sebelum jatuh tempo'],
            'H-1' => ['offset_days' => -1, 'label' => 'Pengingat terakhir'],
            'H' => ['offset_days' => 0, 'label' => 'Jatuh tempo hari ini'],
            'D+3' => ['offset_days' => 3, 'label' => 'Terlambat 3 hari'],
            'D+7' => ['offset_days' => 7, 'label' => 'Terlambat 7 hari'],
            'D+14' => ['offset_days' => 14, 'label' => 'Terlambat 14 hari'],
            'D+30' => ['offset_days' => 30, 'label' => 'Terlambat 30 hari'],
        ],
    ],

];
