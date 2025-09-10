<?php

namespace TimoDeWinter\FilamentAuthorization;

use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Permission\Models\Role;
use TimoDeWinter\FilamentAuthorization\Console\Commands\SyncPermissionsCommand;
use TimoDeWinter\FilamentAuthorization\Policies\RolePolicy;

class FilamentAuthorizationServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-authorization')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasCommand(SyncPermissionsCommand::class);
    }

    public function packageBooted(): void
    {
        \TimoDeWinter\FilamentAuthorization\Facades\FilamentAuthorization::registerPermission([
            'view' => __('filament-authorization::labels.view'),
            'update' => __('filament-authorization::labels.update'),
            'create' => __('filament-authorization::labels.create'),
            'delete' => __('filament-authorization::labels.delete'),
        ], 'roles');

        Gate::policy(Role::class, RolePolicy::class);
    }
}
