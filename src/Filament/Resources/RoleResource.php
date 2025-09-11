<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Resources;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
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

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    public static function getModelLabel(): string
    {
        return __('filament-authorization::labels.role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-authorization::labels.roles');
    }

    public static function form(Form $form): Form
    {
        return self::getCustomForm($form, function (Form $form) {
            return $form
                ->schema([
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
                    TextColumn::make('guard_name'),

                    TextColumn::make('name')
                        ->searchable()
                        ->sortable(),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ]);
    }
}
