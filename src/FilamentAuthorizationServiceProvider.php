<?php

namespace TimoDeWinter\FilamentAuthorization;

use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Permission\Models\Role;
use TimoDeWinter\FilamentAuthorization\Console\Commands\CreateAdminRoleCommand;
use TimoDeWinter\FilamentAuthorization\Console\Commands\SyncPermissionsCommand;
use TimoDeWinter\FilamentAuthorization\Facades\FilamentAuthorization;
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
            ->hasMigration('add_system_role_columns_to_roles_table')
            ->hasCommands(
                SyncPermissionsCommand::class,
                CreateAdminRoleCommand::class,
            );
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(\TimoDeWinter\FilamentAuthorization\FilamentAuthorization::class);
    }

    public function packageBooted(): void
    {
        FilamentAuthorization::registerPermission([
            'view' => __('filament-authorization::labels.view'),
            'update' => __('filament-authorization::labels.update'),
            'create' => __('filament-authorization::labels.create'),
            'delete' => __('filament-authorization::labels.delete'),
        ], 'roles', __('filament-authorization::labels.roles'));

        Gate::policy(Role::class, RolePolicy::class);
    }
}
