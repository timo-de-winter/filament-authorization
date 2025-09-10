<?php

namespace TimoDeWinter\FilamentAuthorization\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(Authenticatable $user): Response
    {
        if ($user->can('roles::view')) {
            return Response::allow();
        }

        return Response::deny();
    }

    public function view(Authenticatable $user, Role $role): Response
    {
        if ($user->can('roles::view')) {
            return Response::allow();
        }

        return Response::deny();
    }

    public function create(Authenticatable $user): Response
    {
        if ($user->can('roles::create')) {
            return Response::allow();
        }

        return Response::deny();
    }

    public function update(Authenticatable $user, Role $role): Response
    {
        if ($user->can('roles::update')) {
            return Response::allow();
        }

        return Response::deny();
    }

    public function delete(Authenticatable $user, Role $role): Response
    {
        if ($user->can('roles::delete')) {
            return Response::allow();
        }

        return Response::deny();
    }
}
