<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;

class RoleManager
{
    const CACHE_KEY = 'app_role_ids';
    const CACHE_TTL = 86400; // 24 hours

    /**
     * Get role ID by name (cached)
     */
    public static function getRoleId(string $roleName): ?int
    {
        $roleIds = self::getAllRoleIds();
        return $roleIds[$roleName] ?? null;
    }

    /**
     * Get role name by ID (cached)
     */
    public static function getRoleName(int $roleId): ?string
    {
        $roleIds = self::getAllRoleIds();
        return array_search($roleId, $roleIds) ?: null;
    }

    /**
     * Get all role ID mappings (with caching)
     */
    public static function getAllRoleIds(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $roles = Role::pluck('id', 'name')->toArray();
            return $roles ?: [];
        });
    }

    /**
     * Create or get role with ID
     */
    public static function createOrGetRole(string $roleName, ?int $specificId = null): Role
    {
        $role = Role::firstOrCreate(
            ['name' => $roleName, 'guard_name' => 'web'],
            ['id' => $specificId]
        );

        // Clear cache when role is created/updated
        Cache::forget(self::CACHE_KEY);

        return $role;
    }

    /**
     * Get or create multiple roles
     */
    public static function createOrGetRoles(array $roleNames): array
    {
        $roles = [];
        foreach ($roleNames as $roleName) {
            $roles[$roleName] = self::createOrGetRole($roleName);
        }
        return $roles;
    }

    /**
     * Assign permissions to role
     */
    public static function assignPermissionsToRole(string $roleName, array $permissionNames): Role
    {
        $role = self::createOrGetRole($roleName);
        
        // Create permissions if not exist
        foreach ($permissionNames as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $role->givePermissionTo($permissionNames);
        Cache::forget(self::CACHE_KEY);

        return $role;
    }

    /**
     * Revoke all permissions from role
     */
    public static function revokeAllPermissionsFromRole(string $roleName): Role
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $role->permissions()->detach();
        }
        return $role;
    }

    /**
     * Flush cache
     */
    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get role model by name
     */
    public static function getRole(string $roleName): ?Role
    {
        return Role::where('name', $roleName)->first();
    }

    /**
     * Delete role by name
     */
    public static function deleteRole(string $roleName): bool
    {
        $role = self::getRole($roleName);
        if ($role) {
            $role->delete();
            Cache::forget(self::CACHE_KEY);
            return true;
        }
        return false;
    }
}
