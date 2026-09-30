<?php

namespace Celios\Core\Enums;

enum PageType: string
{
    case STANDARD = 'standard';
    case HOME = 'home';
    case TERMS_AND_CONDITIONS = 'terms_and_conditions';
    case PRIVACY_POLICY = 'privacy_policy';
    case COOKIE_POLICY = 'cookie_policy';

    public function label(): string
    {
        return match ($this) {
            self::STANDARD => __('fields.page_type_standard'),
            self::HOME => __('fields.page_type_home'),
            self::TERMS_AND_CONDITIONS => __('fields.page_type_terms_and_conditions'),
            self::PRIVACY_POLICY => __('fields.page_type_privacy_policy'),
            self::COOKIE_POLICY => __('fields.page_type_cookie_policy'),
        };
    }

    public function isSystem(): bool
    {
        return $this !== self::STANDARD;
    }

    public static function systemTypes(): array
    {
        return [
            self::HOME,
            self::TERMS_AND_CONDITIONS,
            self::PRIVACY_POLICY,
            self::COOKIE_POLICY,
        ];
    }

    /**
     * Standardized slugs for each supported locale.
     */
    public function standardSlugs(): array
    {
        return match ($this) {
            self::HOME => [
                'sr' => 'home',
                'en' => 'home',
                'it' => 'home',
            ],
            self::TERMS_AND_CONDITIONS => [
                'sr' => 'uslovi-koriscenja',
                'en' => 'terms-and-conditions',
                'it' => 'termini-e-condizioni',
            ],
            self::PRIVACY_POLICY => [
                'sr' => 'politika-privatnosti',
                'en' => 'privacy-policy',
                'it' => 'informativa-sulla-privacy',
            ],
            self::COOKIE_POLICY => [
                'sr' => 'politika-kolacica',
                'en' => 'cookie-policy',
                'it' => 'informativa-sui-cookie',
            ],
            self::STANDARD => [],
        };
    }

    public function standardSlug(string $locale): ?string
    {
        $slugs = $this->standardSlugs();
        return $slugs[$locale] ?? null;
    }

    /**
     * Default page titles for each supported locale.
     */
    public function defaultTitles(): array
    {
        return match ($this) {
            self::HOME => [
                'sr' => 'Početna',
                'en' => 'Home',
                'it' => 'Home',
            ],
            self::TERMS_AND_CONDITIONS => [
                'sr' => 'Uslovi korišćenja',
                'en' => 'Terms and Conditions',
                'it' => 'Termini e Condizioni',
            ],
            self::PRIVACY_POLICY => [
                'sr' => 'Politika privatnosti',
                'en' => 'Privacy Policy',
                'it' => 'Informativa sulla Privacy',
            ],
            self::COOKIE_POLICY => [
                'sr' => 'Politika kolačića',
                'en' => 'Cookie Policy',
                'it' => 'Informativa sui Cookie',
            ],
            self::STANDARD => [],
        };
    }

    public function defaultTitle(string $locale): ?string
    {
        $titles = $this->defaultTitles();
        return $titles[$locale] ?? null;
    }

    /**
     * Filament badge color for each type.
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::STANDARD => 'gray',
            self::HOME => 'primary',
            self::TERMS_AND_CONDITIONS => 'warning',
            self::PRIVACY_POLICY => 'info',
            self::COOKIE_POLICY => 'success',
        };
    }
}
