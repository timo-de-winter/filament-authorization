<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Pages\CreateRole;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Pages\EditRole;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Pages\ListRoles;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Schemas\RoleForm;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Tables\RolesTable;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $configurationClass = RoleResourceConfiguration::class;

    public static function getModel(): string
    {
        return config('permission.models.role');
    }

    protected static ?string $slug = 'roles';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    public static function getModelLabel(): string
    {
        return __('filament-authorization::labels.role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-authorization::labels.roles');
    }

    public static function getCluster(): ?string
    {
        if ($configuration = static::getConfiguration()) {
            if ($cluster = $configuration->getCluster()) {
                return $cluster;
            }
        }

        return parent::getCluster();
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        if ($configuration = static::getConfiguration()) {
            if ($group = $configuration->getNavigationGroup()) {
                return $group;
            }
        }

        return parent::getNavigationGroup();
    }

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
