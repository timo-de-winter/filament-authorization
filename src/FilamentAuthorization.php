<?php

namespace TimoDeWinter\FilamentAuthorization;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class FilamentAuthorization
{
    public array $permissions = [];

    public array $prefixTranslations = [];

    /**
     * The gate the host application plugs a "may manage system roles" decision
     * into (e.g. the CMS wires this to a super-admin check).
     *
     * @var (Closure(Authenticatable): bool)|null
     */
    protected ?Closure $authorizeSystemRoleManagementCallback = null;

    /**
     * Register who may create system roles and edit their protection flags
     * (is_system, is_deletable, is_name_editable, are_permissions_editable).
     *
     * @param  Closure(Authenticatable): bool  $callback
     */
    public function authorizeSystemRoleManagementUsing(Closure $callback): static
    {
        $this->authorizeSystemRoleManagementCallback = $callback;

        return $this;
    }

    /**
     * Whether the actor may manage the "system" aspects of roles. Fails closed:
     * without a registered callback nobody may manage system roles, so a host
     * that forgets to wire authorizeSystemRoleManagementUsing() does not silently
     * let every user flag roles as protected.
     */
    public function canManageSystemRoles(Authenticatable $user): bool
    {
        if ($this->authorizeSystemRoleManagementCallback === null) {
            return false;
        }

        return (bool) ($this->authorizeSystemRoleManagementCallback)($user);
    }

    /**
     * The role columns only system-role managers may set.
     *
     * @return array<int, string>
     */
    public function systemRoleFlagColumns(): array
    {
        return ['is_system', 'is_deletable', 'is_name_editable', 'are_permissions_editable'];
    }

    /**
     * Strip the system-role flag columns from submitted data unless the actor may
     * manage them, so a non-manager cannot flag a role as a system role or alter
     * its protections by tampering with the request payload.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function withoutUnmanageableSystemRoleFlags(array $data, bool $canManageSystemRoles): array
    {
        if ($canManageSystemRoles) {
            return $data;
        }

        return Arr::except($data, $this->systemRoleFlagColumns());
    }

    public function registerPermission(string|array $permission, string $prefix, string $prefixTranslation, $tab = 'Default'): static
    {
        $this->permissions[$tab][$prefix] = [
            ...($this->permissions[$tab][$prefix] ?? []),
            ...Arr::wrap($permission),
        ];

        $this->prefixTranslations[$prefix] = $prefixTranslation;

        return $this;
    }

    public function getPrefixTranslation(string $prefix): ?string
    {
        return $this->prefixTranslations[$prefix] ?? null;
    }

    public function getTabs(): array
    {
        return array_keys($this->permissions);
    }

    public function getPrefixGroups(string $tab): array
    {
        return array_keys($this->permissions[$tab] ?? []);
    }

    public function getPermissions(string $tab, string $prefix): array
    {
        return $this->permissions[$tab][$prefix] ?? [];
    }

    public function getAllPermissions(): array
    {
        return $this->permissions;
    }

    public function formatPermissionsForDatabase(array $permissions, bool $filterOnEnabled = true): array
    {
        $formattedPermissions = [];

        foreach ($permissions as $group => $permissionList) {
            foreach ($permissionList as $permission => $enabled) {
                if ($enabled || ! $filterOnEnabled) {
                    $formattedPermissions[] = $group.'::'.$permission;
                }
            }
        }

        return $formattedPermissions;
    }

    public function formatPermissionsFromDatabase(Collection $permissions): array
    {
        $output = [];

        foreach ($permissions as $permission) {
            [$group, $permission] = explode('::', $permission->name);

            $output[$group][$permission] = true;
        }

        return $output;
    }
}
