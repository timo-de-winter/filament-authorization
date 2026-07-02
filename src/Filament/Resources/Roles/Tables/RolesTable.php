<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use TimoDeWinter\FilamentAuthorization\Models\Role;
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

                IconColumn::make('is_system')
                    ->label(__('filament-authorization::labels.system_role'))
                    ->boolean()
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
                DeleteAction::make()
                    ->hidden(fn (Model $record): bool => $record instanceof Role && $record->isDeleteLocked()),
            ])
            ->defaultToolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            [$locked, $deletable] = $records->partition(
                                fn (Model $record): bool => $record instanceof Role && $record->isDeleteLocked()
                            );

                            $deletable->each->delete();

                            if ($locked->isNotEmpty()) {
                                Notification::make()
                                    ->warning()
                                    ->title(__('filament-authorization::labels.system_roles_not_deleted'))
                                    ->body(trans_choice(
                                        'filament-authorization::labels.system_roles_skipped',
                                        $locked->count(),
                                        ['count' => $locked->count()],
                                    ))
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }
}
