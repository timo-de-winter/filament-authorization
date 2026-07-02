<?php

namespace TimoDeWinter\FilamentAuthorization\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use TimoDeWinter\FilamentAuthorization\Models\Role;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->jobTitle(),
            'guard_name' => 'web',
        ];
    }

    /**
     * A fully locked system role: protected from deletion, renaming and
     * permission edits.
     */
    public function system(): static
    {
        return $this->state([
            'is_system' => true,
            'is_deletable' => false,
            'is_name_editable' => false,
            'are_permissions_editable' => false,
        ]);
    }
}
