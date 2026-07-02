<?php

namespace TimoDeWinter\FilamentAuthorization\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Permission\PermissionServiceProvider;
use TimoDeWinter\FilamentAuthorization\FilamentAuthorizationServiceProvider;
use TimoDeWinter\FilamentAuthorization\Models\Role;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'TimoDeWinter\\FilamentAuthorization\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        $this->loadPermissionMigrations();
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            PermissionServiceProvider::class,
            FilamentAuthorizationServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        config()->set('permission.models.role', Role::class);
    }

    /**
     * Run the spatie permission tables migration plus the package's system-role
     * columns migration against the in-memory test database.
     */
    protected function loadPermissionMigrations(): void
    {
        (include __DIR__.'/../vendor/spatie/laravel-permission/database/migrations/create_permission_tables.php.stub')->up();
        (include __DIR__.'/../database/migrations/add_system_role_columns_to_roles_table.php.stub')->up();
    }
}
