<?php

namespace TimoDeWinter\FilamentAuthorization;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class FilamentAuthorization
{
    public array $permissions = [];

    public array $descriptions = [];

    public array $prefixTranslations = [];

    public array $prefixDescriptions = [];

    public array $tabOrder = [];

    public function registerPermission(string|array $permission, string $prefix, string $prefixTranslation, $tab = 'Default', ?string $prefixDescription = null): static
    {
        $existing = $this->permissions[$tab][$prefix] ?? [];

        foreach (Arr::wrap($permission) as $key => $value) {
            if (is_array($value)) {
                $existing[$key] = $value['label'] ?? (is_string($key) ? $key : (string) $key);

                if (isset($value['description'])) {
                    $this->descriptions[$prefix][$key] = $value['description'];
                }
            } else {
                $existing[$key] = $value;
            }
        }

        $this->permissions[$tab][$prefix] = $existing;
        $this->prefixTranslations[$prefix] = $prefixTranslation;

        if ($prefixDescription !== null) {
            $this->prefixDescriptions[$prefix] = $prefixDescription;
        }

        return $this;
    }

    public function setTabOrder(array $order): static
    {
        $this->tabOrder = $order;

        return $this;
    }

    public function getPrefixTranslation(string $prefix): ?string
    {
        return $this->prefixTranslations[$prefix] ?? null;
    }

    public function getPrefixDescription(string $prefix): ?string
    {
        return $this->prefixDescriptions[$prefix] ?? null;
    }

    public function getDescription(string $prefix, int|string $key): ?string
    {
        return $this->descriptions[$prefix][$key] ?? null;
    }

    public function getTabs(): array
    {
        $registered = array_keys($this->permissions);

        if ($this->tabOrder === []) {
            return $registered;
        }

        $ordered = array_values(array_intersect($this->tabOrder, $registered));
        $remaining = array_values(array_diff($registered, $ordered));

        return [...$ordered, ...$remaining];
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
