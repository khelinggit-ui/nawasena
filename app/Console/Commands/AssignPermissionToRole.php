<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RoleManager;

class AssignPermissionToRole extends Command
{
    protected $signature = 'role:assign-permission {role} {permissions*}';
    protected $description = 'Assign permissions to a role';

    public function handle()
    {
        $roleName = $this->argument('role');
        $permissions = $this->argument('permissions');

        try {
            $role = RoleManager::assignPermissionsToRole($roleName, $permissions);
            $this->info("✓ Permissions assigned to role '{$roleName}':");
            foreach ($permissions as $permission) {
                $this->line("  - {$permission}");
            }
        } catch (\Exception $e) {
            $this->error("✗ Error: " . $e->getMessage());
        }
    }
}
