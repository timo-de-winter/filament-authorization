<?php

namespace TimoDeWinter\FilamentAuthorization;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentAuthorizationServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('filament-authorization')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_filament_authorization_table');
    }
}
