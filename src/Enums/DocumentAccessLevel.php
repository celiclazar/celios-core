<?php

namespace Celios\Core\Enums;

enum DocumentAccessLevel: string
{
    case PUBLIC = 'public';
    case AUTHENTICATED = 'authenticated';
    case ROLES = 'roles';
    case INTERNAL = 'internal';

    public function label(): string
    {
        return match ($this) {
            self::PUBLIC => __('documents.access_public'),
            self::AUTHENTICATED => __('documents.access_authenticated'),
            self::ROLES => __('documents.access_roles'),
            self::INTERNAL => __('documents.access_internal'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PUBLIC => 'success',
            self::AUTHENTICATED => 'info',
            self::ROLES => 'warning',
            self::INTERNAL => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PUBLIC => 'heroicon-o-globe-alt',
            self::AUTHENTICATED => 'heroicon-o-user-group',
            self::ROLES => 'heroicon-o-shield-check',
            self::INTERNAL => 'heroicon-o-lock-closed',
        };
    }

    public function isPublic(): bool
    {
        return $this === self::PUBLIC;
    }

    public function isInternal(): bool
    {
        return $this === self::INTERNAL;
    }
}
