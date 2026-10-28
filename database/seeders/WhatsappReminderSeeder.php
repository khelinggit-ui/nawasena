<?php

namespace Database\Seeders;

use App\Models\WhatsappBotSetting;
use App\Models\WhatsappTemplate;
use Illuminate\Database\Seeder;

class WhatsappReminderSeeder extends Seeder
{
    public function run(): void
    {
        WhatsappTemplate::query()->firstOrCreate(
            ['key' => 'due_reminder'],
            [
                'name' => 'Pengingat jatuh tempo',
                'body' => "Halo {{nama}},\n\nPengingat: angsuran ke-{{angsuran_ke}} sebesar {{nominal_angsuran}} akan jatuh tempo pada {{tanggal_jatuh_tempo}}.\n\nTotal tunggakan saat ini: {{total_tunggakan}}.\n\nTerima kasih.",
                'is_active' => true,
            ]
        );

        WhatsappTemplate::query()->firstOrCreate(
            ['key' => 'overdue_reminder'],
            [
                'name' => 'Pengingat tunggakan',
                'body' => "Halo {{nama}},\n\nAngsuran ke-{{angsuran_ke}} sebesar {{nominal_angsuran}} telah terlambat {{hari_terlambat}} hari (jatuh tempo {{tanggal_jatuh_tempo}}).\n\nTotal tunggakan: {{total_tunggakan}}.\nMohon segera lakukan pembayaran.\n\nTerima kasih.",
                'is_active' => true,
            ]
        );

        if (WhatsappBotSetting::current() === null) {
            WhatsappBotSetting::query()->create([
                'instance_name' => null,
                'is_active' => false,
                'timezone' => config('whatsapp.timezone', 'Asia/Jakarta'),
                'send_window_start' => '09:00',
                'send_window_end' => '16:00',
                'max_per_day' => (int) config('whatsapp.reminders.max_per_day', 50),
                'delay_min_seconds' => 20,
                'delay_max_seconds' => 30,
                'active_days' => [1, 2, 3, 4, 5, 6],
                'reminder_rules' => config('whatsapp.reminders.rules', []),
                'branch_ids' => null,
                'opt_out_enabled' => false,
            ]);
        }
    }
}
