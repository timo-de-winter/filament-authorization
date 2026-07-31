<?php

use Illuminate\Support\Facades\Facade;
use TimoDeWinter\FilamentAuthorization\FilamentAuthorization;
use TimoDeWinter\FilamentAuthorization\FilamentAuthorizationServiceProvider;

/**
 * Re-boots the package against a fresh registry so each case sees only the
 * registrations made under its own config. The facade's resolved instance has
 * to be cleared too, otherwise packageBooted() writes to the registry built
 * during the original application boot.
 */
function bootPackageWith(bool $autoRegister): FilamentAuthorization
{
    config()->set('filament-authorization.roles_permission.auto_register', $autoRegister);

    app()->forgetInstance(FilamentAuthorization::class);
    app()->singleton(FilamentAuthorization::class);
    Facade::clearResolvedInstances();

    (new FilamentAuthorizationServiceProvider(app()))->packageBooted();

    return app(FilamentAuthorization::class);
}

it('registers the roles permission by default', function () {
    expect(bootPackageWith(true)->getPermissions('Default', 'roles'))
        ->toBe([
            'view' => __('filament-authorization::labels.view'),
            'update' => __('filament-authorization::labels.update'),
            'create' => __('filament-authorization::labels.create'),
            'delete' => __('filament-authorization::labels.delete'),
        ]);
});

it('does not register the roles permission when auto registration is off', function () {
    expect(bootPackageWith(false)->getPermissions('Default', 'roles'))->toBeEmpty();
});

it('leaves no default tab at all when auto registration is off', function () {
    expect(bootPackageWith(false)->getTabs())->not->toContain('Default');
});
