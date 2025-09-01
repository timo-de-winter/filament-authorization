<?php

namespace TimoDeWinter\FilamentAuthorization;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentAuthorizationServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-authorization')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_filament_authorization_table');
    }
}
