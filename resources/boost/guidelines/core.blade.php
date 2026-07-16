{{-- Boost guideline for timo-de-winter/filament-authorization. Update when the public API changes. --}}
# Filament Authorization (roles & permissions)

- Wraps spatie/laravel-permission: a Filament role resource plus a code-first permission registry — packages register permissions in code and a command syncs them to the database.

## Structure

- `TimoDeWinter\FilamentAuthorization\FilamentAuthorizationPlugin` — panel plugin exposing `TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\RoleResource`.
- `TimoDeWinter\FilamentAuthorization\Facades\FilamentAuthorization` — singleton registry of injected permissions plus the system-role authorization gate.
- `TimoDeWinter\FilamentAuthorization\Models\Role` — spatie Role with system-role lock flags; its `deleting` hook blocks delete-locked roles even for super-admins.
- `RolePolicy` gates role CRUD behind `roles::view/create/update/delete`.

## Setup & registration

- Panel: `FilamentAuthorizationPlugin::make()`. Migration (publish tag `filament-authorization-migrations`) adds `is_system`, `is_deletable`, `is_name_editable`, `are_permissions_editable` booleans to spatie's roles table.
- Config `filament-authorization.guard.modifiable` shows/hides the role-form guard selector; when `false` saves force `filament-authorization.guard.default`.
- The resource resolves its model from spatie's `permission.models.role`; custom role models must extend this package's `Role` or system-role locks stop applying.

## Using it

- Register permissions in any service provider (grouped by `prefix`, optional resource `tab`); stored as `prefix::permission`, checked via `$user->can('articles::view')`.

@verbatim
<code-snippet name="Registering permissions" lang="php">
FilamentAuthorization::registerPermission(
    permission: ['view' => __('pkg::labels.view')],
    prefix: 'articles',
    prefixTranslation: __('pkg::labels.articles'),
    tab: 'Content',
);
</code-snippet>
@endverbatim

- Run {{ $assist->artisanCommand('authorization:sync-permissions') }} on every deploy: creates missing permissions, deletes unregistered ones, clears spatie's cache.
- `FilamentAuthorization::authorizeSystemRoleManagementUsing(fn ($user): bool => ...)` decides who may set system-role flags; unset = nobody (fails closed).

## Customizing this plugin

This plugin is modifiable (timo-de-winter/filament-modifiable-plugins): customize its resources from your panel provider instead of subclassing, e.g. `FilamentAuthorizationPlugin::make()->label('...', RoleResource::class)->navigationGroup('...', RoleResource::class)`. Labels, icons, navigation, schemas, tables, and clusters are all overridable this way; every modifier takes the target resource class as its final argument. **Never copy or extend the plugin's resource classes to change labels, navigation, forms, or tables.**

## Pitfalls

- **Never create or delete permission rows by hand** — register in code and sync; sync deletes unregistered permissions for the guard.
- **Never pass another namespace's translations to `registerPermission`** — labels are resolved strings evaluated at boot, not closures.
