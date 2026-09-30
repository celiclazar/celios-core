<?php

declare(strict_types=1);

namespace Celios\Core\Policies;

use Celios\Core\Models\FormSubmission;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormSubmissionPolicy
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
        return $authUser->can('ViewAny:FormSubmission');
    }

    public function view(AuthUser $authUser, FormSubmission $formSubmission): bool
    {
        if (!$authUser->can('View:FormSubmission')) {
            return false;
        }

        // Check if the user is authorized for this specific form
        if ($formSubmission->relationLoaded('form') || $formSubmission->form) {
            return $formSubmission->form->canUserViewSubmissions($authUser);
        }

        return true;
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FormSubmission');
    }

    public function update(AuthUser $authUser, FormSubmission $formSubmission): bool
    {
        return $authUser->can('Update:FormSubmission');
    }

    public function delete(AuthUser $authUser, FormSubmission $formSubmission): bool
    {
        if (!$authUser->can('Delete:FormSubmission')) {
            return false;
        }

        if ($formSubmission->relationLoaded('form') || $formSubmission->form) {
            return $formSubmission->form->canUserViewSubmissions($authUser);
        }

        return true;
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FormSubmission');
    }

    public function restore(AuthUser $authUser, FormSubmission $formSubmission): bool
    {
        return $authUser->can('Restore:FormSubmission');
    }

    public function forceDelete(AuthUser $authUser, FormSubmission $formSubmission): bool
    {
        return $authUser->can('ForceDelete:FormSubmission');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FormSubmission');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FormSubmission');
    }

    public function replicate(AuthUser $authUser, FormSubmission $formSubmission): bool
    {
        return $authUser->can('Replicate:FormSubmission');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FormSubmission');
    }
}
