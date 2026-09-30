<?php

declare(strict_types=1);

namespace Celios\Core\Policies;

use Celios\Core\Models\Form;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormPolicy
{
    use HandlesAuthorization;

    public function before(AuthUser $authUser, string $ability): ?bool
    {
        if ($authUser->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Form');
    }

    public function view(AuthUser $authUser, Form $form): bool
    {
        return $authUser->can('View:Form');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Form');
    }

    public function update(AuthUser $authUser, Form $form): bool
    {
        return $authUser->can('Update:Form');
    }

    public function delete(AuthUser $authUser, Form $form): bool
    {
        return $authUser->can('Delete:Form');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Form');
    }

    public function restore(AuthUser $authUser, Form $form): bool
    {
        return $authUser->can('Restore:Form');
    }

    public function forceDelete(AuthUser $authUser, Form $form): bool
    {
        return $authUser->can('ForceDelete:Form');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Form');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Form');
    }

    public function replicate(AuthUser $authUser, Form $form): bool
    {
        return $authUser->can('Replicate:Form');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Form');
    }
}
