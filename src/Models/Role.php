<?php

namespace TimoDeWinter\FilamentAuthorization\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Spatie role extended with "system role" protection flags. A system role is one
 * whose deletion, name, or linked permissions can be locked so code logic that
 * depends on the role cannot be broken from the CMS. The lock helpers below are
 * the single source of truth for those protections and are enforced at the
 * resource layer (forms/tables/pages) and, for deletion, by the model guard
 * registered in {@see static::booted()} — which holds even for super-admins that
 * bypass the policy via Gate::before().
 *
 * @property bool $is_system
 * @property bool $is_deletable
 * @property bool $is_name_editable
 * @property bool $are_permissions_editable
 */
class Role extends SpatieRole
{
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'is_deletable' => 'boolean',
            'is_name_editable' => 'boolean',
            'are_permissions_editable' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (self $role): bool {
            return ! $role->isDeleteLocked();
        });
    }

    public function isSystemRole(): bool
    {
        return (bool) $this->is_system;
    }

    public function isDeleteLocked(): bool
    {
        return $this->isSystemRole() && ! $this->is_deletable;
    }

    public function isNameLocked(): bool
    {
        return $this->isSystemRole() && ! $this->is_name_editable;
    }

    public function arePermissionsLocked(): bool
    {
        return $this->isSystemRole() && ! $this->are_permissions_editable;
    }
}
