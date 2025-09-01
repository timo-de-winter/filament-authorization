<?php

namespace TimoDeWinter\FilamentAuthorization\Filament\Resources\RoleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
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
        if (!config('filament-authorization.guard.modifiable')) {
            $data['guard_name'] = config('filament-authorization.guard.default');
        }

        return $data;
    }
}
