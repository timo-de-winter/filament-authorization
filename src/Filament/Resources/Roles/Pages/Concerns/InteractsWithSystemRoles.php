<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Pages\Concerns;

use Filament\Facades\Filament;
use TimoDeWinter\FilamentAuthorization\Facades\FilamentAuthorization;

trait InteractsWithSystemRoles
{
    /**
     * Whether the current actor may create system roles and set their protection
     * flags. Used to strip those flags from submitted data for everyone else.
     */
    protected function canManageSystemRoles(): bool
    {
        $user = Filament::auth()->user();

        return $user !== null && FilamentAuthorization::canManageSystemRoles($user);
    }
}
