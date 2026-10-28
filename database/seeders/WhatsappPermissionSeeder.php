<?php

namespace Database\Seeders;

use App\Constants\RoleConstants;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class WhatsappPermissionSeeder extends Seeder
{
    /**
     * Permission menu WhatsApp API beserta submenu dan aksinya.
     */
    public const PERMISSIONS = [
        // Menu utama
        'menu whatsapp api',

        // Submenu Instance WhatsApp
        'page whatsapp instance',
        'create whatsapp instance',
        'delete whatsapp instance',

        // Submenu Dashboard Bot
        'page whatsapp bot dashboard',

        // Submenu Pengaturan Bot
        'page whatsapp bot setting',
        'send whatsapp test',

        // Submenu Template Pesan
        'page whatsapp template',
        'update whatsapp template',

        // Submenu Riwayat Pengingat
        'page whatsapp log',

        // Submenu Blacklist Nomor
        'page whatsapp opt out',
        'create whatsapp opt out',
        'delete whatsapp opt out',
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Admin mendapatkan seluruh akses menu WhatsApp API.
        $roleAdmin = Role::findByName(RoleConstants::ADMIN, 'web');

        if ($roleAdmin) {
            $roleAdmin->givePermissionTo(self::PERMISSIONS);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
