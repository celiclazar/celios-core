<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sitemap Status
    |--------------------------------------------------------------------------
    |
    | Enable or disable the sitemap generation and endpoint.
    |
    */
    'enabled' => env('SITEMAP_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Sitemap Cache
    |--------------------------------------------------------------------------
    |
    | Cache configuration for dynamic XML generation in seconds.
    | Set to 0 to disable caching during development.
    |
    */
    'cache_key' => 'sitemap_xml_cache',
    'cache_ttl' => env('SITEMAP_CACHE_TTL', 86400), // 24 hours

    /*
    |--------------------------------------------------------------------------
    | Static File Generation
    |--------------------------------------------------------------------------
    |
    | When enabled, sitemap can also be saved to a physical public XML file.
    |
    */
    'generate_static' => env('SITEMAP_GENERATE_STATIC', true),
    'static_path' => public_path('sitemap.xml'),

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Include multilingual hreflang alternates (xhtml:link) and image tags.
    |
    */
    'include_alternates' => true,
    'include_images' => true,

    /*
    |--------------------------------------------------------------------------
    | Models Configuration
    |--------------------------------------------------------------------------
    */
    'models' => [
        'pages' => [
            'enabled' => true,
            'class' => \App\Models\Page::class,
            'priority' => 0.8,
            'changefreq' => 'weekly',
        ],
        'posts' => [
            'enabled' => true,
            'class' => \App\Models\Post::class,
            'priority' => 0.7,
            'changefreq' => 'weekly',
        ],
        'categories' => [
            'enabled' => true,
            'class' => \App\Models\Category::class,
            'priority' => 0.6,
            'changefreq' => 'weekly',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Core / Static Routes
    |--------------------------------------------------------------------------
    */
    'static_routes' => [
        'home' => [
            'priority' => 1.0,
            'changefreq' => 'daily',
        ],
        'blog' => [
            'priority' => 0.8,
            'changefreq' => 'daily',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded Paths / Patterns
    |--------------------------------------------------------------------------
    |
    | Patterns that should be excluded from the sitemap and disallowed in robots.txt.
    |
    */
    'excluded_patterns' => [
        '/admin*',
        '/profile*',
        '/login*',
        '/register*',
        '/forgot-password*',
        '/reset-password*',
        '/welcome-inertia*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom URLs
    |--------------------------------------------------------------------------
    |
    | Any extra custom URLs to include in the sitemap.
    |
    */
    'custom_urls' => [],

];
