<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Resources\RoleResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use TimoDeWinter\FilamentAuthorization\Facades\FilamentAuthorization;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\RoleResource;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['permissions'] = FilamentAuthorization::formatPermissionsFromDatabase($this->record->permissions);

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update([
            'name' => $data['name'],
            'guard_name'  => $data['guard_name'],
        ]);

        $record->syncPermissions(
            FilamentAuthorization::formatPermissionsForDatabase($data['permissions'])
        );

        return $record;
    }
}
