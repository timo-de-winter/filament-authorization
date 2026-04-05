# Filament Authorization

[![Latest Version on Packagist](https://img.shields.io/packagist/v/timo-de-winter/filament-authorization.svg?style=flat-square)](https://packagist.org/packages/timo-de-winter/filament-authorization)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/timo-de-winter/filament-authorization/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/timo-de-winter/filament-authorization/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/timo-de-winter/filament-authorization/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/timo-de-winter/filament-authorization/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/timo-de-winter/filament-authorization.svg?style=flat-square)](https://packagist.org/packages/timo-de-winter/filament-authorization)

Easy authorization system for filament, with advanced features to inject permissions from different places.
## Installation

You can install the package via composer:
```bash
composer require timo-de-winter/filament-authorization
```

### Migrations
This package makes use of `spatie/laravel-permission`, so if you have not done so already, publish the migrations for this plugin:

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### Prepare your model
Follow [these](https://spatie.be/docs/laravel-permission/v6/prerequisites) instructions to prepare your model to work with roles.

### Configuration
You can optionally publish the config file with:
```bash
php artisan vendor:publish --tag="filament-authorization-config"
```

This is the contents of the published config file:
```php
return [
    'guard' => [
        'modifiable' => false,
        'default' => 'web',
    ],
];
```

Optionally, you can publish the views using
```bash
php artisan vendor:publish --tag="filament-authorization-views"
```

## Usage
```php
$panel
    ->plugin(
        \TimoDeWinter\FilamentAuthorization\FilamentAuthorizationPlugin::make(),
    );
```

### Configurable Resource
The RoleResource supports Filament's [configurable resources](https://filamentphp.com/docs/5.x/plugins/configurable-resources-and-pages) feature. You can pass custom configurations to the plugin:

```php
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\RoleResource;

$panel
    ->plugin(
        \TimoDeWinter\FilamentAuthorization\FilamentAuthorizationPlugin::make()
            ->roleResource([
                RoleResource::make('custom')->slug('custom-roles'),
            ]),
    );
```

### Providing/injecting permissions
The philosophy of this package is that permissions are defined by your application and not by the permissions as stated in your database.
This is useful when you're working with a package-first strategy. My problem was that I want my packages to work independently from each other, but most of the time a lot of them are combined to create a bigger product.
Within those applications I want a single resource for role management that allows for any permissions provided by any of the packages to be attached. Since the packages work standalone and can also choose to disable their permission system it is important that there was one modular approach to inject permissions from any of the packages.

Injecting permissions is very easy and can be done in any service provider.
The structure is as follows:
- Tabs (optional)
- Prefix
- Permission

Tabs are used to group permissions together in a tab in the resource to give more clarity to the user. You might have a tab named "Default" and one named "Advanced". If there is only 1 tab in total, we do not show tabs at all.
Prefixes are mainly used to group permissions together under a given prefix to prevent overlap.
And then of course there are permissions.

```php
// This is the most simple way to do it
\TimoDeWinter\FilamentAuthorization\Facades\FilamentAuthorization::registerPermission(
    permission: [
        'view' => __('filament-authorization::labels.view'),
        'update' => __('filament-authorization::labels.update'),
        'create' => __('filament-authorization::labels.create'),
        'delete' => __('filament-authorization::labels.delete'),
    ],
    prefix: 'roles', 
    prefixTranslation: __('filament-authorization::labels.roles'),
    tab: 'Authorization', // Optional (defaults to "Default")
);
```

### Synchronisation command
The package comes with a command to synchronize all permissions to the database. In most use cases it would be smart to add this command to your deployment script:
```bash
php artisan authorization:sync-permissions
```

### Admin role and user command
The package comes with a command to easily create an admin role and assign it to a user from the console.
```bash
php artisan authorization:create-admin-role
```

## Testing
```bash
composer test
```

## Security Vulnerabilities
Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits
- [Timo de Winter](https://github.com/timo-de-winter)
- [All Contributors](../../contributors)

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
