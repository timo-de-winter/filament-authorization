<?php

namespace TimoDeWinter\FilamentAuthorization\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminRoleCommand extends Command
{
    protected $signature = 'authorization:create-admin-role {name?} {guard=web}';

    protected $description = 'This will create an administrator role that has all permissions.';

    public function handle(): void
    {
        $name = $this->argument('name') ?? $this->ask('name');

        $role = Role::create([
            'name' => $name,
            'guard_name' => $this->argument('guard'),
        ]);

        $role->syncPermissions(Permission::where('guard_name', $this->argument('guard'))->get());

        if ($this->confirm('Do you want to assign the role to a user?')) {
            $model = $this->ask('What is your user model?', '\App\Models\User');
            $column = $this->ask('What column do you want to find the user by?', 'email');

            $findBy = $this->ask("Provide the $column to find the model by. If we find multiple records we will attach the role to all.");

            $model::where($column, $findBy)
                ->eachById(function (Model $record) use ($role) {
                    $record->assignRole($role);
                });
        }

        $this->info('The role has been created successfully');
    }
}
