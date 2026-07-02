<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use TimoDeWinter\FilamentAuthorization\Facades\FilamentAuthorization;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Pages\Concerns\InteractsWithSystemRoles;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\RoleResource;

class CreateRole extends CreateRecord
{
    use InteractsWithSystemRoles;

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
        $data = FilamentAuthorization::withoutUnmanageableSystemRoleFlags($data, $this->canManageSystemRoles());

        $attributes = [
            'name' => $data['name'],
            'guard_name' => $data['guard_name'],
        ];

        foreach (FilamentAuthorization::systemRoleFlagColumns() as $column) {
            if (array_key_exists($column, $data)) {
                $attributes[$column] = $data[$column];
            }
        }

        $record = new ($this->getModel())($attributes);

        if (
            static::getResource()::isScopedToTenant() &&
            ($tenant = Filament::getTenant())
        ) {
            return $this->associateRecordWithTenant($record, $tenant);
        }

        $record->save();

        $record->syncPermissions(
            FilamentAuthorization::formatPermissionsForDatabase($data['permissions'] ?? [])
        );

        return $record;
    }
}
