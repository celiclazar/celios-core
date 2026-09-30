<?php

namespace Celios\Core\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    public function before(AuthUser $authUser, string $ability): ?bool
    {
        if (method_exists($authUser, 'hasRole') && $authUser->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Document');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:Document');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Document');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('Update:Document');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('Delete:Document');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Document');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('Restore:Document');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDelete:Document');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Document');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Document');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('Replicate:Document');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Document');
    }
}
