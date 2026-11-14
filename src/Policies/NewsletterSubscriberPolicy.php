<?php

declare(strict_types=1);

namespace Celios\Core\Policies;

use Celios\Core\Models\NewsletterSubscriber;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class NewsletterSubscriberPolicy
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
        return $authUser->can('ViewAny:NewsletterSubscriber');
    }

    public function view(AuthUser $authUser, NewsletterSubscriber $subscriber): bool
    {
        return $authUser->can('View:NewsletterSubscriber');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:NewsletterSubscriber');
    }

    public function update(AuthUser $authUser, NewsletterSubscriber $subscriber): bool
    {
        return $authUser->can('Update:NewsletterSubscriber');
    }

    public function delete(AuthUser $authUser, NewsletterSubscriber $subscriber): bool
    {
        return $authUser->can('Delete:NewsletterSubscriber');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:NewsletterSubscriber');
    }

    public function restore(AuthUser $authUser, NewsletterSubscriber $subscriber): bool
    {
        return $authUser->can('Restore:NewsletterSubscriber');
    }

    public function forceDelete(AuthUser $authUser, NewsletterSubscriber $subscriber): bool
    {
        return $authUser->can('ForceDelete:NewsletterSubscriber');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:NewsletterSubscriber');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:NewsletterSubscriber');
    }

    public function replicate(AuthUser $authUser, NewsletterSubscriber $subscriber): bool
    {
        return $authUser->can('Replicate:NewsletterSubscriber');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:NewsletterSubscriber');
    }
}
