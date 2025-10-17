<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\RoleResource\Pages;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Pages\CreateRole;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Pages\EditRole;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Pages\ListRoles;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Schemas\RoleForm;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Tables\RolesTable;
use TimoDeWinter\FilamentModifiablePlugins\Concerns\CanBeModified;
use TimoDeWinter\FilamentModifiablePlugins\CustomizableTable;

class RoleResource extends Resource
{
    use CanBeModified;

    public static function getPluginId(): string
    {
        return 'filament-authorization';
    }

    public static function getModel(): string
    {
        return config('permission.models.role');
    }

    protected static ?string $slug = 'roles';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedShieldCheck;

    public static function getModelLabel(): string
    {
        return __('filament-authorization::labels.role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-authorization::labels.roles');
    }

    public static function form(Schema $schema): Schema
    {
        return self::getCustomForm($schema, function (Schema $schema) {
            return RoleForm::configure($schema);
        });
    }

    public static function table(Table $table): Table
    {
        return self::getCustomTable($table, function (CustomizableTable $table) {
            return RolesTable::configure($table);
        });
    }

    public static function getRelations(): array
    {
        return self::getCustomRelations([]);
    }

    public static function getPages(): array
    {
        return self::getCustomPages([
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ]);
    }
}
