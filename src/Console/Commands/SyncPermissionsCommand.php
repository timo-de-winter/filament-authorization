<?php

namespace TimoDeWinter\FilamentAuthorization\Console\Commands;

use Spatie\Permission\PermissionRegistrar;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use TimoDeWinter\FilamentAuthorization\Facades\FilamentAuthorization;

class SyncPermissionsCommand extends Command
{
    protected $signature = 'authorization:sync-permissions {guard=web}';

    protected $description = 'This will sync all injected permissions to your database.';

    public function handle(): void
    {
        $allPermissions = [];

        $guard = $this->argument('guard');

        foreach (FilamentAuthorization::getAllPermissions() as $permissions) {
            $allPermissions = [...$allPermissions, ...$permissions];
        }

        $permissions = FilamentAuthorization::formatPermissionsForDatabase($allPermissions, filterOnEnabled: false);

        $existingPermissions = Permission::where('guard_name', $guard)->get()->keyBy('name');

        $existingPermissions->diffKeys(collect($permissions)->mapWithKeys(fn ($p) => [$p => $p]))->each->delete();

        foreach ($permissions as $permission) {
            if ($existingPermissions->has($permission)) {
                continue;
            }

            Permission::create([
                'name' => $permission,
                'guard_name' => $guard,
            ]);
        }

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
