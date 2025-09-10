<?php

namespace TimoDeWinter\FilamentAuthorization;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use TimoDeWinter\FilamentAuthorization\Console\Commands\SyncPermissionsCommand;

class FilamentAuthorizationServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-authorization')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasCommand(SyncPermissionsCommand::class)
            ->hasMigration('create_filament_authorization_table');
    }

    public function packageBooted(): void
    {
        \TimoDeWinter\FilamentAuthorization\Facades\FilamentAuthorization::registerPermission([
            'view' => __('filament-authorization::labels.view'),
            'update' => __('filament-authorization::labels.update'),
            'create' => __('filament-authorization::labels.create'),
            'delete' => __('filament-authorization::labels.delete'),
        ], 'roles');
    }
}
