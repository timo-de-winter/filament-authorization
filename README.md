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

[//]: # (todo: BELOW SHOULD BE UPDATED STILL)




You can publish and run the migrations with:
```bash
php artisan vendor:publish --tag="filament-authorization-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --tag="filament-authorization-config"
```

This is the contents of the published config file:
```php
return [
];
```

Optionally, you can publish the views using
```bash
php artisan vendor:publish --tag="filament-authorization-views"
```

## Usage
```php
$filamentAuthorization = new TimoDeWinter\FilamentAuthorization();
echo $filamentAuthorization->echoPhrase('Hello, TimoDeWinter!');
```

## Testing
```bash
composer test
```

## Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities
Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits
- [Timo de Winter](https://github.com/timo-de-winter)
- [All Contributors](../../contributors)

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
