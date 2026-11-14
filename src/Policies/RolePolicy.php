<?php

declare(strict_types=1);

namespace Celios\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    protected function isSuperAdmin(AuthUser $user): bool
    {
        return method_exists($user, 'hasRole') && ($user->hasRole('super_admin') || $user->hasRole('super-admin'));
    }

    protected function isAdmin(AuthUser $user): bool
    {
        return method_exists($user, 'hasRole') && ($this->isSuperAdmin($user) || $user->hasRole('admin'));
    }

    protected function isSuperAdminRole(Role $role): bool
    {
        return in_array($role->name, ['super_admin', 'super-admin'], true);
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser) || $authUser->can('ViewAny:Role');
    }

    public function view(AuthUser $authUser, Role $role): bool
    {
        if ($this->isSuperAdminRole($role) && ! $this->isSuperAdmin($authUser)) {
            return false;
        }

        return $this->isAdmin($authUser) || $authUser->can('View:Role');
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser) || $authUser->can('Create:Role');
    }

    public function update(AuthUser $authUser, Role $role): bool
    {
        if ($this->isSuperAdminRole($role) && ! $this->isSuperAdmin($authUser)) {
            return false;
        }

        return $this->isAdmin($authUser) || $authUser->can('Update:Role');
    }

    public function delete(AuthUser $authUser, Role $role): bool
    {
        // Neither super_admin nor the admin system role can be deleted
        if ($this->isSuperAdminRole($role) || $role->name === 'admin') {
            return false;
        }

        return $this->isAdmin($authUser) || $authUser->can('Delete:Role');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser) || $authUser->can('DeleteAny:Role');
    }

    public function restore(AuthUser $authUser, Role $role): bool
    {
        if ($this->isSuperAdminRole($role) && ! $this->isSuperAdmin($authUser)) {
            return false;
        }

        return $this->isAdmin($authUser) || $authUser->can('Restore:Role');
    }

    public function forceDelete(AuthUser $authUser, Role $role): bool
    {
        if ($this->isSuperAdminRole($role) || $role->name === 'admin') {
            return false;
        }

        return $this->isAdmin($authUser) || $authUser->can('ForceDelete:Role');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser) || $authUser->can('ForceDeleteAny:Role');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser) || $authUser->can('RestoreAny:Role');
    }

    public function replicate(AuthUser $authUser, Role $role): bool
    {
        if ($this->isSuperAdminRole($role)) {
            return false;
        }

        return $this->isAdmin($authUser) || $authUser->can('Replicate:Role');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser) || $authUser->can('Reorder:Role');
    }
}
