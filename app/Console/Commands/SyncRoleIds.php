<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Constants\RoleConstants;

class SyncRoleIds extends Command
{
    protected $signature = 'sync:role-ids';
    protected $description = 'Synchronize role IDs with RoleConstants';

    public function handle()
    {
        $this->info('Syncing role IDs with RoleConstants...');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            [RoleConstants::ADMIN_ID, RoleConstants::ADMIN],
            [RoleConstants::USER_ID, RoleConstants::USER],
            [RoleConstants::SPV_ID, RoleConstants::SPV],
            [RoleConstants::ADMINKREDIT_ID, RoleConstants::ADMINKREDIT],
            [RoleConstants::MARKETING_ID, RoleConstants::MARKETING],
            [RoleConstants::ANALIS_ID, RoleConstants::ANALIS],
            [RoleConstants::RISK_ID, RoleConstants::RISK],
            [RoleConstants::MITRA_ID, RoleConstants::MITRA],
        ];

        foreach ($roles as [$id, $name]) {
            $role = Role::find($id);
            if ($role) {
                $this->info("✓ Role ID {$id} ({$name}) already exists");
            } else {
                Role::create(['id' => $id, 'name' => $name, 'guard_name' => 'web']);
                $this->info("✓ Created Role ID {$id} ({$name})");
            }
        }

        $this->info('Role synchronization completed!');
    }
}
