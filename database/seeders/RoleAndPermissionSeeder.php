<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Constants\RoleConstants;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Daftar Permissions
        $permissions = [
            'menu dashboard',
            'page pencapaian',
            'page data debitur',
            'page debitur approve',
            'page laporan nominatif',
            'create debitur',
            'delete debitur',
            'edit debitur',
            'update debitur',
            'view debitur',
            'menu management user',
            'page users',
            'create users',
            'edit users',
            'update users',
            'view users',
            'delete users',
            'page roles',
            'create roles',
            'edit roles',
            'update roles',
            'delete roles',
            'page permission',
            'create permission',
            'edit permission',
            'update permission',
            'view permission',
            'delete permission',
            'page cabang',
            'create cabang',
            'edit cabang',
            'update cabang',
            'delete cabang',
            'view cabang',
            'page kantor',
            'create kantor',
            'edit kantor',
            'update kantor',
            'delete kantor',
            'view kantor',
            'page analis',
            'menu analis',
            'page analis detail',
            'action tahap 1',
            'manage payment',
            'download excel mitra'
        ];

        // Buat Permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Buat Roles dengan ID yang spesifik
        $roleAdmin = Role::updateOrCreate(
            ['name' => RoleConstants::ADMIN, 'guard_name' => 'web'],
            ['id' => RoleConstants::ADMIN_ID]
        );
        $roleUser = Role::updateOrCreate(
            ['name' => RoleConstants::USER, 'guard_name' => 'web'],
            ['id' => RoleConstants::USER_ID]
        );
        $roleSpv = Role::updateOrCreate(
            ['name' => RoleConstants::SPV, 'guard_name' => 'web'],
            ['id' => RoleConstants::SPV_ID]
        );
        $roleAdminKredit = Role::updateOrCreate(
            ['name' => RoleConstants::ADMINKREDIT, 'guard_name' => 'web'],
            ['id' => RoleConstants::ADMINKREDIT_ID]
        );
        $roleMarketing = Role::updateOrCreate(
            ['name' => RoleConstants::MARKETING, 'guard_name' => 'web'],
            ['id' => RoleConstants::MARKETING_ID]
        );
        $roleAnalis = Role::updateOrCreate(
            ['name' => RoleConstants::ANALIS, 'guard_name' => 'web'],
            ['id' => RoleConstants::ANALIS_ID]
        );
        $roleRisk = Role::updateOrCreate(
            ['name' => RoleConstants::RISK, 'guard_name' => 'web'],
            ['id' => RoleConstants::RISK_ID]
        );
        $roleMitra = Role::updateOrCreate(
            ['name' => RoleConstants::MITRA, 'guard_name' => 'web'],
            ['id' => RoleConstants::MITRA_ID]
        );

        // Set Permissions ke Role Admin (Full Akses)
        $roleAdmin->givePermissionTo(Permission::all());

        // Set Permission ke Role User
        $roleUser->givePermissionTo([
            'menu dashboard',
            'page laporan nominatif',
        ]);

        // Set Permission ke Role SPV
        $roleSpv->givePermissionTo([
            'menu dashboard', 
            'page data debitur', 
            'page debitur approve',
            'page laporan nominatif',
            'view debitur', 
            'action tahap 1'
        ]);

        // Set Permission ke Role Admin Kredit
        $roleAdminKredit->givePermissionTo([
            'menu dashboard',
            'page data debitur',
            'page debitur approve',
            'page laporan nominatif',
            'view debitur',
            'manage payment'
        ]);

        // Set Permission ke Role Marketing
        $roleMarketing->givePermissionTo([
            'menu dashboard',
            'page data debitur',
            'page debitur approve',
            'page laporan nominatif',
            'create debitur',
            'view debitur',
            'action tahap 1'
        ]);

        // Set Permission ke Role Analis
        $roleAnalis->givePermissionTo([
            'menu dashboard',
            'page data debitur',
            'page debitur approve',
            'page laporan nominatif',
            'view debitur',
            'page analis',
            'menu analis',
            'page analis detail',
            'action tahap 1',
            'download excel mitra'
        ]);

        // Set Permission ke Role Risk
        $roleRisk->givePermissionTo([
            'menu dashboard',
            'page data debitur',
            'page debitur approve',
            'page laporan nominatif',
            'view debitur',
            'action tahap 1',
            'download excel mitra'
        ]);

        // Set Permission ke Role Mitra
        $roleMitra->givePermissionTo([
            'menu dashboard',
            'page data debitur',
            'page debitur approve',
            'page laporan nominatif',
            'view debitur',
            'action tahap 1',
            'download excel mitra'
        ]);

        // Buat Akun User Administrator
        $adminUser = User::firstOrCreate([
            'email' => 'admin@email.com',
        ], [
            'name' => 'administrator',
            'password' => Hash::make('masterkey296'),
            'status' => 'Active'
        ]);

        // Berikan Role admin ke user administrator
        $adminUser->assignRole($roleAdmin);
    }
}
