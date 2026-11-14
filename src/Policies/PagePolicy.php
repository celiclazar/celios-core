<?php

declare(strict_types=1);

namespace Celios\Core\Policies;

use Celios\Core\Models\Page;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PagePolicy
{
    use HandlesAuthorization;

    public function before(AuthUser $authUser, string $ability): ?bool
    {
        if (method_exists($authUser, 'hasRole') && ($authUser->hasRole('super_admin') || $authUser->hasRole('super-admin'))) {
            return true;
        }

        return null;
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Page');
    }

    public function view(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('View:Page');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Page');
    }

    public function update(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('Update:Page');
    }

    public function delete(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('Delete:Page');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Page');
    }

    public function restore(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('Restore:Page');
    }

    public function forceDelete(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('ForceDelete:Page');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Page');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Page');
    }

    public function replicate(AuthUser $authUser, Page $page): bool
    {
        return $authUser->can('Replicate:Page');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Page');
    }
}
