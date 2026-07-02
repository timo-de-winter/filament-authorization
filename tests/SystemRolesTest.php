<?php

use Illuminate\Auth\GenericUser;
use TimoDeWinter\FilamentAuthorization\Facades\FilamentAuthorization;
use TimoDeWinter\FilamentAuthorization\Models\Role;

describe('Role protection flags', function () {
    it('casts the protection flags to booleans', function () {
        $role = Role::factory()->create([
            'is_system' => 1,
            'is_deletable' => 0,
            'is_name_editable' => 1,
            'are_permissions_editable' => 0,
        ])->refresh();

        expect($role->is_system)->toBeTrue()
            ->and($role->is_deletable)->toBeFalse()
            ->and($role->is_name_editable)->toBeTrue()
            ->and($role->are_permissions_editable)->toBeFalse();
    });

    it('never locks a normal (non-system) role even when flags are off', function () {
        $role = Role::factory()->create([
            'is_system' => false,
            'is_deletable' => false,
            'is_name_editable' => false,
            'are_permissions_editable' => false,
        ]);

        expect($role->isSystemRole())->toBeFalse()
            ->and($role->isDeleteLocked())->toBeFalse()
            ->and($role->isNameLocked())->toBeFalse()
            ->and($role->arePermissionsLocked())->toBeFalse();
    });

    it('locks every aspect of a fully protected system role', function () {
        $role = Role::factory()->system()->create();

        expect($role->isSystemRole())->toBeTrue()
            ->and($role->isDeleteLocked())->toBeTrue()
            ->and($role->isNameLocked())->toBeTrue()
            ->and($role->arePermissionsLocked())->toBeTrue();
    });

    it('unlocks aspects of a system role independently', function () {
        $role = Role::factory()->system()->create(['is_name_editable' => true]);

        expect($role->isNameLocked())->toBeFalse()
            ->and($role->isDeleteLocked())->toBeTrue()
            ->and($role->arePermissionsLocked())->toBeTrue();
    });
});

describe('Delete guard', function () {
    it('prevents deleting a delete-locked system role', function () {
        $role = Role::factory()->system()->create();

        $role->delete();

        expect(Role::query()->whereKey($role->getKey())->exists())->toBeTrue();
    });

    it('allows deletion once the role is unlocked', function () {
        $role = Role::factory()->system()->create();

        $role->update(['is_deletable' => true]);
        $role->delete();

        expect(Role::query()->whereKey($role->getKey())->exists())->toBeFalse();
    });

    it('deletes a normal role', function () {
        $role = Role::factory()->create();

        $role->delete();

        expect(Role::query()->whereKey($role->getKey())->exists())->toBeFalse();
    });
});

describe('System role management authorization', function () {
    it('fails closed when no manager callback is registered', function () {
        expect(FilamentAuthorization::canManageSystemRoles(new GenericUser(['id' => 1])))->toBeFalse();
    });

    it('delegates the decision to the registered callback', function () {
        $user = new GenericUser(['id' => 1]);

        FilamentAuthorization::authorizeSystemRoleManagementUsing(fn (): bool => false);
        expect(FilamentAuthorization::canManageSystemRoles($user))->toBeFalse();

        FilamentAuthorization::authorizeSystemRoleManagementUsing(fn (): bool => true);
        expect(FilamentAuthorization::canManageSystemRoles($user))->toBeTrue();
    });

    it('passes the actor to the manager callback', function () {
        $received = null;

        FilamentAuthorization::authorizeSystemRoleManagementUsing(function ($user) use (&$received): bool {
            $received = $user;

            return true;
        });

        $actor = new GenericUser(['id' => 7]);
        FilamentAuthorization::canManageSystemRoles($actor);

        expect($received)->toBe($actor);
    });
});

describe('Flag tampering protection', function () {
    it('strips system-role flags from data when the actor may not manage them', function () {
        $data = [
            'name' => 'Editor',
            'is_system' => true,
            'is_deletable' => false,
            'is_name_editable' => false,
            'are_permissions_editable' => false,
            'permissions' => [],
        ];

        expect(FilamentAuthorization::withoutUnmanageableSystemRoleFlags($data, false))
            ->toBe(['name' => 'Editor', 'permissions' => []]);
    });

    it('keeps system-role flags when the actor may manage them', function () {
        $data = ['name' => 'Editor', 'is_system' => true, 'is_deletable' => false];

        expect(FilamentAuthorization::withoutUnmanageableSystemRoleFlags($data, true))->toBe($data);
    });
});
