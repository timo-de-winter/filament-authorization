<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Resources\RoleResource\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use TimoDeWinter\FilamentAuthorization\Facades\FilamentAuthorization;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\RoleResource;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! config('filament-authorization.guard.modifiable')) {
            $data['guard_name'] = config('filament-authorization.guard.default');
        }

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = new ($this->getModel())(Arr::except($data, 'permissions'));

        if (
            static::getResource()::isScopedToTenant() &&
            ($tenant = Filament::getTenant())
        ) {
            return $this->associateRecordWithTenant($record, $tenant);
        }

        $record->save();

        $record->syncPermissions(
            FilamentAuthorization::formatPermissionsForDatabase($data['permissions'])
        );

        return $record;
    }
}
