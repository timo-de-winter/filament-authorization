<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use TimoDeWinter\FilamentAuthorization\Facades\FilamentAuthorization;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\Pages\Concerns\InteractsWithSystemRoles;
use TimoDeWinter\FilamentAuthorization\Filament\Resources\Roles\RoleResource;
use TimoDeWinter\FilamentAuthorization\Models\Role;

class EditRole extends EditRecord
{
    use InteractsWithSystemRoles;

    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->hidden(fn (): bool => $this->record instanceof Role && $this->record->isDeleteLocked()),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['permissions'] = FilamentAuthorization::formatPermissionsFromDatabase($this->record->permissions);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! config('filament-authorization.guard.modifiable')) {
            $data['guard_name'] = config('filament-authorization.guard.default');
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data = FilamentAuthorization::withoutUnmanageableSystemRoleFlags($data, $this->canManageSystemRoles());

        // Evaluate locks against the persisted role: a manager must unlock a flag
        // and save before the locked field can be edited (two-step "unlock first").
        $nameLocked = $record instanceof Role && $record->isNameLocked();
        $permissionsLocked = $record instanceof Role && $record->arePermissionsLocked();

        $attributes = [
            'guard_name' => $data['guard_name'],
            'name' => $nameLocked ? $record->name : ($data['name'] ?? $record->name),
        ];

        foreach (FilamentAuthorization::systemRoleFlagColumns() as $column) {
            if (array_key_exists($column, $data)) {
                $attributes[$column] = $data[$column];
            }
        }

        $record->update($attributes);

        if (! $permissionsLocked) {
            $record->syncPermissions(
                FilamentAuthorization::formatPermissionsForDatabase($data['permissions'] ?? [])
            );
        }

        return $record;
    }
}
