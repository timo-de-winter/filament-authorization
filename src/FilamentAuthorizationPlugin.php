<?php

namespace TimoDeWinter\FilamentAuthorization;

use Filament\Contracts\Plugin;
use Filament\Panel;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\RoleResource;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\RoleResourceConfiguration;

class FilamentAuthorizationPlugin implements Plugin
{
    /** @var array<RoleResourceConfiguration> */
    protected array $roleResourceConfigurations = [];

    public function getId(): string
    {
        return 'filament-authorization';
    }

    /**
     * @param  array<RoleResourceConfiguration>  $configurations
     */
    public function roleResource(array $configurations): static
    {
        $this->roleResourceConfigurations = $configurations;

        return $this;
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                ...empty($this->roleResourceConfigurations)
                    ? [RoleResource::class]
                    : $this->roleResourceConfigurations,
            ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        return filament(app(static::class)->getId());
    }
}
