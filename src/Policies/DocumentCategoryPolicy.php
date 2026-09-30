<?php

namespace Celios\Core\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentCategoryPolicy
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
        return $authUser->can('ViewAny:DocumentCategory');
    }

    public function view(AuthUser $authUser): bool
    {
        return $authUser->can('View:DocumentCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DocumentCategory');
    }

    public function update(AuthUser $authUser): bool
    {
        return $authUser->can('Update:DocumentCategory');
    }

    public function delete(AuthUser $authUser): bool
    {
        return $authUser->can('Delete:DocumentCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:DocumentCategory');
    }

    public function restore(AuthUser $authUser): bool
    {
        return $authUser->can('Restore:DocumentCategory');
    }

    public function forceDelete(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDelete:DocumentCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DocumentCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DocumentCategory');
    }

    public function replicate(AuthUser $authUser): bool
    {
        return $authUser->can('Replicate:DocumentCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DocumentCategory');
    }
}
