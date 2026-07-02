<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use TimoDeWinter\FilamentAuthorization\Facades\FilamentAuthorization;
use TimoDeWinter\FilamentAuthorization\Filament\Schemas\Components\PermissionsSelect;
use TimoDeWinter\FilamentAuthorization\Models\Role;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-authorization::labels.name'))
                            ->maxLength(255)
                            ->required()
                            ->disabled(fn (?Model $record): bool => $record instanceof Role && $record->isNameLocked())
                            ->unique(
                                table: config('permission.table_names.roles'),
                                column: 'name',
                                ignoreRecord: true
                            ),

                        Select::make('guard_name')
                            ->label(__('filament-authorization::labels.guard_name'))
                            ->required()
                            ->visible(fn () => config('filament-authorization.guard.modifiable'))
                            ->options(function () {
                                return collect(array_keys(config('auth.guards')))
                                    ->mapWithKeys(fn (string $key) => [$key => $key])
                                    ->toArray();
                            }),
                    ]),

                Section::make(__('filament-authorization::labels.system_role_section'))
                    ->description(__('filament-authorization::labels.system_role_section_description'))
                    ->columns()
                    ->visible(fn (): bool => self::canManageSystemRoles())
                    ->schema([
                        Toggle::make('is_system')
                            ->label(__('filament-authorization::labels.is_system'))
                            ->live()
                            ->columnSpanFull(),

                        Toggle::make('is_deletable')
                            ->label(__('filament-authorization::labels.is_deletable'))
                            ->default(true)
                            ->visible(fn (Get $get): bool => (bool) $get('is_system')),

                        Toggle::make('is_name_editable')
                            ->label(__('filament-authorization::labels.is_name_editable'))
                            ->default(true)
                            ->visible(fn (Get $get): bool => (bool) $get('is_system')),

                        Toggle::make('are_permissions_editable')
                            ->label(__('filament-authorization::labels.are_permissions_editable'))
                            ->default(true)
                            ->visible(fn (Get $get): bool => (bool) $get('is_system')),
                    ]),

                PermissionsSelect::make('permissions')
                    ->disabled(fn (?Model $record): bool => $record instanceof Role && $record->arePermissionsLocked())
                    ->columnSpanFull(),
            ]);
    }

    protected static function canManageSystemRoles(): bool
    {
        $user = Filament::auth()->user();

        return $user !== null && FilamentAuthorization::canManageSystemRoles($user);
    }
}
