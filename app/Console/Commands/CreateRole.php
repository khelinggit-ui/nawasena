<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RoleManager;

class CreateRole extends Command
{
    protected $signature = 'role:create {name} {permissions?*}';
    protected $description = 'Create a new role with optional permissions';

    public function handle()
    {
        $roleName = $this->argument('name');
        $permissions = $this->argument('permissions') ?? [];

        try {
            if ($permissions) {
                $role = RoleManager::assignPermissionsToRole($roleName, $permissions);
                $this->info("✓ Role '{$roleName}' created with permissions: " . implode(', ', $permissions));
            } else {
                $role = RoleManager::createOrGetRole($roleName);
                $this->info("✓ Role '{$roleName}' created successfully");
            }

            $this->info("Role ID: " . $role->id);
        } catch (\Exception $e) {
            $this->error("✗ Error: " . $e->getMessage());
        }
    }
}
