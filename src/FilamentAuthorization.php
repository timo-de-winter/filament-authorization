<?php

namespace TimoDeWinter\FilamentAuthorization;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class FilamentAuthorization
{
    public array $permissions = [];

    public array $prefixTranslations = [];

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
