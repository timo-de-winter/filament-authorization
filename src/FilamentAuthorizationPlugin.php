<?php

namespace TimoDeWinter\FilamentAuthorization;

use Filament\Contracts\Plugin;
use Filament\Panel;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\RoleResource;
use TimoDeWinter\FilamentModifiablePlugins\Concerns\CanModifyResources;

class FilamentAuthorizationPlugin implements Plugin
{
    use CanModifyResources;

    public function getId(): string
    {
        return 'filament-authorization';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                RoleResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {

    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }
}
