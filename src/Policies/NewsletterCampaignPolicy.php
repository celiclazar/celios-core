<?php

declare(strict_types=1);

namespace Celios\Core\Policies;

use Celios\Core\Models\NewsletterCampaign;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class NewsletterCampaignPolicy
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
        return $authUser->can('ViewAny:NewsletterCampaign');
    }

    public function view(AuthUser $authUser, NewsletterCampaign $campaign): bool
    {
        return $authUser->can('View:NewsletterCampaign');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:NewsletterCampaign');
    }

    public function update(AuthUser $authUser, NewsletterCampaign $campaign): bool
    {
        return $authUser->can('Update:NewsletterCampaign');
    }

    public function delete(AuthUser $authUser, NewsletterCampaign $campaign): bool
    {
        return $authUser->can('Delete:NewsletterCampaign');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:NewsletterCampaign');
    }

    public function restore(AuthUser $authUser, NewsletterCampaign $campaign): bool
    {
        return $authUser->can('Restore:NewsletterCampaign');
    }

    public function forceDelete(AuthUser $authUser, NewsletterCampaign $campaign): bool
    {
        return $authUser->can('ForceDelete:NewsletterCampaign');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:NewsletterCampaign');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:NewsletterCampaign');
    }

    public function replicate(AuthUser $authUser, NewsletterCampaign $campaign): bool
    {
        return $authUser->can('Replicate:NewsletterCampaign');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:NewsletterCampaign');
    }
}
