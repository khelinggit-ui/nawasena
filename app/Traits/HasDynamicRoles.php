<?php

namespace App\Traits;

use App\Services\RoleManager;

trait HasDynamicRoles
{
    /**
     * Check if user has role by name (dynamic, no need to change code)
     */
    public function hasRoleByName(string|array $roleNames): bool
    {
        $roleNames = is_array($roleNames) ? $roleNames : [$roleNames];
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    /**
     * Check if user has any of the given role names
     */
    public function hasAnyRoleByName(array $roleNames): bool
    {
        return $this->hasRoleByName($roleNames);
    }

    /**
     * Get user's role IDs (cached)
     */
    public function getDynamicRoleIds(): array
    {
        return $this->roles()->pluck('id')->toArray();
    }

    /**
     * Get user's role names (alias untuk Spatie getRoleNames dengan prefix)
     */
    public function getDynamicRoleNames(): array
    {
        return $this->roles()->pluck('name')->toArray();
    }

    /**
     * Get primary (first) role name
     */
    public function getPrimaryRoleName(): ?string
    {
        return $this->roles()->first()?->name;
    }

    /**
     * Get primary (first) role ID
     */
    public function getPrimaryRoleId(): ?int
    {
        return $this->roles()->first()?->id;
    }

    /**
     * Check if user has permission by name (supports nested)
     */
    public function hasPermissionByName(string $permissionName): bool
    {
        return $this->can($permissionName);
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRoleByName('admin');
    }
}
