<?php

namespace Celios\Core\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class UserPolicy
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

    public function viewAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser) || $authUser->can('ViewAny:User');
    }

    public function view(AuthUser $authUser, ?AuthUser $targetUser = null): bool
    {
        if ($targetUser && $this->isSuperAdmin($targetUser) && ! $this->isSuperAdmin($authUser)) {
            return false;
        }

        return $this->isAdmin($authUser) || $authUser->can('View:User');
    }

    public function create(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser) || $authUser->can('Create:User');
    }

    public function update(AuthUser $authUser, ?AuthUser $targetUser = null): bool
    {
        if ($targetUser && $this->isSuperAdmin($targetUser) && ! $this->isSuperAdmin($authUser)) {
            return false;
        }

        return $this->isAdmin($authUser) || $authUser->can('Update:User');
    }

    public function delete(AuthUser $authUser, ?AuthUser $targetUser = null): bool
    {
        if ($targetUser && $this->isSuperAdmin($targetUser)) {
            return false;
        }

        if ($targetUser && $targetUser->id === $authUser->id) {
            return false;
        }

        return $this->isAdmin($authUser) || $authUser->can('Delete:User');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser) || $authUser->can('DeleteAny:User');
    }

    public function restore(AuthUser $authUser, ?AuthUser $targetUser = null): bool
    {
        if ($targetUser && $this->isSuperAdmin($targetUser) && ! $this->isSuperAdmin($authUser)) {
            return false;
        }

        return $this->isAdmin($authUser) || $authUser->can('Restore:User');
    }

    public function forceDelete(AuthUser $authUser, ?AuthUser $targetUser = null): bool
    {
        if ($targetUser && $this->isSuperAdmin($targetUser)) {
            return false;
        }

        if ($targetUser && $targetUser->id === $authUser->id) {
            return false;
        }

        return $this->isAdmin($authUser) || $authUser->can('ForceDelete:User');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser) || $authUser->can('ForceDeleteAny:User');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser) || $authUser->can('RestoreAny:User');
    }

    public function replicate(AuthUser $authUser, ?AuthUser $targetUser = null): bool
    {
        if ($targetUser && $this->isSuperAdmin($targetUser)) {
            return false;
        }

        return $this->isAdmin($authUser) || $authUser->can('Replicate:User');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $this->isAdmin($authUser) || $authUser->can('Reorder:User');
    }
}
