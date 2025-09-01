<?php

namespace TimoDeWinter\FilamentAuthorization;

use Illuminate\Support\Arr;

class FilamentAuthorization
{
    public array $permissions = [];

    public function registerPermission(string|array $permission, $prefix = 'default', $tab = 'Default'): static
    {
        $this->permissions[$tab][$prefix] = [
            ...($this->permissions[$tab][$prefix] ?? []),
            ...Arr::wrap($permission),
        ];

        return $this;
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
}
