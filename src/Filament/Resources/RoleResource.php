<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\RoleResource\Pages\ListRoles;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\RoleResource\Pages\CreateRole;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\RoleResource\Pages\EditRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use TimoDeWinter\FilamentAuthorization\Filament\Forms\Components\PermissionsSelect;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\RoleResource\Pages;
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

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';

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
        });
    }

    public static function table(Table $table): Table
    {
        return self::getCustomTable($table, function (CustomizableTable $table) {
            return $table
                ->defaultColumns([
                    TextColumn::make('name')
                        ->label(__('filament-authorization::labels.name'))
                        ->searchable()
                        ->sortable(),

                    TextColumn::make('permissions_count')
                        ->label(__('filament-authorization::labels.permissions_count'))
                        ->counts('permissions')
                        ->badge()
                        ->sortable(),

                    TextColumn::make('guard_name')
                        ->toggleable()
                        ->sortable()
                        ->visible(fn () => config('filament-authorization.guard.modifiable')),
                ])
                ->defaultActions([
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                ->defaultBulkActions([
                    BulkActionGroup::make([
                        DeleteBulkAction::make(),
                    ]),
                ]);
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
