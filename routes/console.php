<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Pengingat WhatsApp
|--------------------------------------------------------------------------
|
| Scheduler ini aman diaktifkan sekarang: command `reminders:send --send`
| tetap menolak jalan selama setting bot `is_active = false`.
|
*/

Schedule::command('reminders:send --send')
    ->dailyAt('09:00')
    ->timezone(config('whatsapp.timezone', 'Asia/Jakarta'))
    ->withoutOverlapping()
    ->description('Kirim pengingat WhatsApp harian');

// Fallback pemrosesan antrean tanpa worker permanen (cocok untuk Laragon/Windows).
// Akan dilewati jika tidak ada job. Jangan jalankan bersamaan dengan
// `php artisan queue:work` permanen.
Schedule::command('queue:work --stop-when-empty --max-time=50')
    ->everyMinute()
    ->withoutOverlapping()
    ->description('Proses antrean pengingat WhatsApp');
