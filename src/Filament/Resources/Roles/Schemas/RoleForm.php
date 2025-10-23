<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use TimoDeWinter\FilamentAuthorization\Filament\Schemas\Components\PermissionsSelect;

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

                PermissionsSelect::make('permissions')
                    ->columnSpanFull(),
            ]);
    }
}
