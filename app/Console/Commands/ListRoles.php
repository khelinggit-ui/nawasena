<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RoleManager;
use Spatie\Permission\Models\Role;

class ListRoles extends Command
{
    protected $signature = 'role:list';
    protected $description = 'List all roles with their IDs';

    public function handle()
    {
        $roles = Role::all();

        if ($roles->isEmpty()) {
            $this->info('No roles found.');
            return;
        }

        $headers = ['ID', 'Name', 'Permissions Count'];
        $rows = [];

        foreach ($roles as $role) {
            $rows[] = [
                $role->id,
                $role->name,
                $role->permissions()->count()
            ];
        }

        $this->table($headers, $rows);

        // Show role IDs mapping
        $this->info("\nRole ID Mapping:");
        $allRoleIds = RoleManager::getAllRoleIds();
        foreach ($allRoleIds as $name => $id) {
            $this->line("  {$id} => {$name}");
        }
    }
}
