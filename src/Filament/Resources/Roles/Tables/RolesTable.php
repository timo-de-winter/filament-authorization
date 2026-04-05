<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
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
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
