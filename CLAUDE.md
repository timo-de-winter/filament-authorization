# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel package that provides an authorization system for Filament admin panels. It integrates with `spatie/laravel-permission` to manage roles and permissions, and uses `timo-de-winter/filament-modifiable-plugins` for customizable resources.

## Development Commands

### Testing
```bash
composer test                    # Run all tests using Pest
vendor/bin/pest                  # Direct Pest execution
vendor/bin/pest --coverage      # Run tests with coverage
```

### Code Quality
```bash
composer format                  # Format code using Laravel Pint
vendor/bin/pint                  # Direct Pint execution
composer analyse                 # Run PHPStan analysis
vendor/bin/phpstan analyse       # Direct PHPStan execution
```

### Package Development
```bash
composer prepare                 # Discover packages (runs automatically after autoload-dump)
```

## Architecture

### Core Components

- **FilamentAuthorizationPlugin**: Main plugin class that registers resources with Filament panels
- **FilamentAuthorizationServiceProvider**: Laravel service provider for package configuration
- **RoleResource**: Filament resource for managing roles with customizable forms and tables

### Key Dependencies

- **spatie/laravel-permission**: Handles the underlying role/permission system
- **timo-de-winter/filament-modifiable-plugins**: Provides customization capabilities via `CanModifyResources` trait

### Resource Architecture

Resources use the modifiable plugin pattern:
- `CanBeModified` trait allows runtime customization
- `CustomizableTable` provides flexible table configurations
- Resources integrate with Spatie's permission models via `config('permission.models.role')`

### Configuration

Guard settings are configurable in `config/filament-authorization.php`:
- `guard.modifiable`: Whether guard selection is editable in forms
- `guard.default`: Default guard when not modifiable

### Internationalization

The package supports multiple locales with both standard (e.g., `en`, `de`) and regional (e.g., `en_US`, `de_DE`) language files in `resources/lang/`.

### Testing Framework

Uses Pest PHP with Orchestra Testbench for Laravel package testing. Test configuration includes strict settings for warnings, risky tests, and output during tests.