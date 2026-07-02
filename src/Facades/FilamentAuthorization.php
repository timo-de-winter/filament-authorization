<?php

namespace TimoDeWinter\FilamentAuthorization\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \TimoDeWinter\FilamentAuthorization\FilamentAuthorization
 *
 * @method static \TimoDeWinter\FilamentAuthorization\FilamentAuthorization authorizeSystemRoleManagementUsing(\Closure $callback)
 * @method static bool canManageSystemRoles(\Illuminate\Contracts\Auth\Authenticatable $user)
 * @method static array systemRoleFlagColumns()
 * @method static array withoutUnmanageableSystemRoleFlags(array $data, bool $canManageSystemRoles)
 */
class FilamentAuthorization extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \TimoDeWinter\FilamentAuthorization\FilamentAuthorization::class;
    }
}
