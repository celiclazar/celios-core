<?php

use Celios\Core\Enums\PageType;
use Celios\Core\Models\Page;

if (! function_exists('page_type_url')) {
    /**
     * Get the standardized URL for a given system page type.
     */
    function page_type_url(PageType|string $type, ?string $locale = null): ?string
    {
        return Page::urlForType($type, $locale);
    }
}
