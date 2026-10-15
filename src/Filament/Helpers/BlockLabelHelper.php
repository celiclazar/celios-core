<?php

namespace Celios\Core\Filament\Helpers;

class BlockLabelHelper
{
    /**
     * Resolves a localized preview string from block state and appends it to the base label.
     *
     * Example output: "Hero sekcija: Dobrodošli na platformu"
     */
    public static function make(
        string $baseLabel,
        ?array $state = null,
        array $fieldKeys = ['heading', 'title', 'name', 'badge_text', 'section_tag', 'body', 'caption'],
        int $maxLength = 50
    ): string {
        if (empty($state)) {
            return $baseLabel;
        }

        $previewText = null;

        foreach ($fieldKeys as $key) {
            if (! isset($state[$key])) {
                continue;
            }

            $previewText = self::resolveStringValue($state[$key]);

            if ($previewText !== null) {
                break;
            }
        }

        if (empty($previewText)) {
            return $baseLabel;
        }

        $cleanText = trim(preg_replace('/\s+/', ' ', strip_tags($previewText)));

        if (empty($cleanText)) {
            return $baseLabel;
        }

        $truncated = mb_strlen($cleanText) > $maxLength
            ? mb_substr($cleanText, 0, $maxLength).'...'
            : $cleanText;

        return "{$baseLabel}: {$truncated}";
    }

    /**
     * Appends an item count or summary to the base label.
     *
     * Example output: "Često postavljana pitanja (4 pitanja)"
     */
    public static function withCount(
        string $baseLabel,
        ?array $state = null,
        string $arrayKey = 'items',
        string $singularUnit = 'stavka',
        string $pluralUnit = 'stavki'
    ): string {
        if (empty($state)) {
            return $baseLabel;
        }

        // Try heading/title first if available
        $headingPreview = null;
        foreach (['heading', 'title', 'name'] as $key) {
            if (! isset($state[$key])) {
                continue;
            }

            $headingPreview = self::resolveStringValue($state[$key]);
            if ($headingPreview !== null) {
                break;
            }
        }

        $items = $state[$arrayKey] ?? [];
        $count = is_array($items) ? count($items) : 0;
        $countText = $count === 1 ? "1 {$singularUnit}" : "{$count} {$pluralUnit}";

        if (! empty($headingPreview)) {
            $cleanHeading = trim(preg_replace('/\s+/', ' ', strip_tags($headingPreview)));
            if (! empty($cleanHeading)) {
                $truncated = mb_strlen($cleanHeading) > 35 ? mb_substr($cleanHeading, 0, 35).'...' : $cleanHeading;

                return "{$baseLabel}: {$truncated} ({$countText})";
            }
        }

        if ($count > 0) {
            return "{$baseLabel} ({$countText})";
        }

        return $baseLabel;
    }

    /**
     * Safely and recursively extracts a scalar string preview from any value, localized or nested.
     */
    public static function resolveStringValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value) || is_numeric($value)) {
            $trimmed = trim((string) $value);

            return $trimmed !== '' ? $trimmed : null;
        }

        if (is_array($value)) {
            $locale = app()->getLocale();

            // Try current locale first
            if (isset($value[$locale])) {
                $candidate = self::resolveStringValue($value[$locale]);
                if ($candidate !== null) {
                    return $candidate;
                }
            }

            // Fallback locales
            foreach (['sr', 'en', 'it'] as $lang) {
                if (isset($value[$lang])) {
                    $candidate = self::resolveStringValue($value[$lang]);
                    if ($candidate !== null) {
                        return $candidate;
                    }
                }
            }

            // Fallback to first scalar in array
            foreach ($value as $item) {
                $candidate = self::resolveStringValue($item);
                if ($candidate !== null) {
                    return $candidate;
                }
            }
        }

        return null;
    }
}
