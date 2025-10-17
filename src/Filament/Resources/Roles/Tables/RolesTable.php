<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use TimoDeWinter\FilamentModifiablePlugins\CustomizableTable;

class RolesTable
{
    public static function configure(CustomizableTable $table): CustomizableTable
    {
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
            ->defaultRecordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultToolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
