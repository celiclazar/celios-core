<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Available CMS Modules
    |--------------------------------------------------------------------------
    |
    | Define all modular features with their associated Filament resources,
    | pages, and page builder blocks.
    |
    */
    'modules' => [
        'blog' => [
            'name' => 'Blog & Articles',
            'description' => 'Publish multilingual articles, categories, and post feeds.',
            'icon' => 'heroicon-o-newspaper',
            'resources' => [
                \Celios\Core\Filament\Resources\Posts\PostResource::class,
                \Celios\Core\Filament\Resources\Categories\CategoryResource::class,
            ],
            'blocks' => [
                \Celios\Core\Filament\Blocks\PostsBlock::class,
            ],
        ],

        'forms' => [
            'name' => 'Custom Form Builder',
            'description' => 'Create dynamic forms and collect user submissions with spam protection.',
            'icon' => 'heroicon-o-clipboard-document-list',
            'resources' => [
                \Celios\Core\Filament\Resources\Forms\FormResource::class,
                \Celios\Core\Filament\Resources\Forms\FormSubmissionResource::class,
            ],
            'blocks' => [
                \Celios\Core\Filament\Blocks\FormBlock::class,
            ],
        ],

        'documents' => [
            'name' => 'Document Library',
            'description' => 'Manage downloadable files, categories, and tiered access levels.',
            'icon' => 'heroicon-o-document-duplicate',
            'resources' => [
                \Celios\Core\Filament\Resources\Documents\DocumentResource::class,
                \Celios\Core\Filament\Resources\DocumentCategories\DocumentCategoryResource::class,
            ],
            'blocks' => [
                \Celios\Core\Filament\Blocks\DocumentsBlock::class,
            ],
        ],

        'newsletter' => [
            'name' => 'Newsletter Marketing Engine',
            'description' => 'Manage subscribers, campaigns, delivery tracking, and double opt-in.',
            'icon' => 'heroicon-o-envelope',
            'resources' => [
                \Celios\Core\Filament\Resources\Newsletter\Campaigns\NewsletterCampaignResource::class,
                \Celios\Core\Filament\Resources\Newsletter\Subscribers\NewsletterSubscriberResource::class,
            ],
            'pages' => [
                \Celios\Core\Filament\Pages\NewsletterSettingsPage::class,
            ],
            'blocks' => [
                \Celios\Core\Filament\Blocks\NewsletterBlock::class,
            ],
        ],

        'payment' => [
            'name' => 'Payment Processing',
            'description' => 'Process online payments, manage gateways (Stripe, Bank Transfer), and view audit logs.',
            'icon' => 'heroicon-o-credit-card',
            'resources' => [
                \Modules\Payment\Filament\Resources\PaymentResource::class,
                \Modules\Payment\Filament\Resources\PaymentGatewayResource::class,
            ],
        ],

        'backups' => [
            'name' => 'System Backups',
            'description' => 'Create, monitor, and download automated database and file backups.',
            'icon' => 'heroicon-o-circle-stack',
            'pages' => [
                \Celios\Core\Filament\Pages\BackupManager::class,
            ],
        ],

        'queue_manager' => [
            'name' => 'Queue Inspector',
            'description' => 'Monitor background job queues, view metrics, and retry failed jobs.',
            'icon' => 'heroicon-o-queue-list',
            'pages' => [
                \Celios\Core\Filament\Pages\QueueManager::class,
            ],
        ],

        'activity_log' => [
            'name' => 'Audit & Activity Log',
            'description' => 'Detailed audit trail of all content updates, user logins, and administrative actions.',
            'icon' => 'heroicon-o-clipboard-document-check',
            'resources' => [
                \Celios\Core\Filament\Resources\Activities\ActivityResource::class,
            ],
        ],

        'crm' => [
            'name' => 'CRM & Leads',
            'description' => 'Manage customer leads, contact pipeline, interaction notes, and form-to-lead conversions.',
            'icon' => 'heroicon-o-user-group',
            'resources' => [
                \Celios\Core\Filament\Resources\Contacts\ContactResource::class,
            ],
        ],

        'visual_builder' => [
            'name' => 'Visual Page Designer (Vue 3 + Inertia)',
            'description' => 'Interactive full-screen visual drag-and-drop page builder with live multi-device preview.',
            'icon' => 'heroicon-o-paint-brush',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pricing Plan Presets
    |--------------------------------------------------------------------------
    |
    | Define tiers that can be assigned to client sites in 1-click.
    |
    */
    'plans' => [
        'starter' => [
            'name' => 'Starter Plan',
            'badge' => 'Core Only',
            'description' => 'Pages, Media Manager, and SEO sitemaps.',
            'modules' => [],
        ],
        'pro' => [
            'name' => 'Professional Plan',
            'badge' => 'Popular',
            'description' => 'Starter + Blog & Articles + Custom Form Builder.',
            'modules' => ['blog', 'forms'],
        ],
        'business' => [
            'name' => 'Business Plan',
            'badge' => 'Growth',
            'description' => 'Pro + Document Library + Newsletter + Payments + CRM + Visual Designer.',
            'modules' => ['blog', 'forms', 'documents', 'newsletter', 'payment', 'crm', 'visual_builder'],
        ],
        'enterprise' => [
            'name' => 'Enterprise / Agency',
            'badge' => 'All Inclusive',
            'description' => 'Full suite including CRM, Backups, Queue Inspector, Audit Logs, and Visual Designer.',
            'modules' => ['blog', 'forms', 'documents', 'newsletter', 'payment', 'crm', 'backups', 'queue_manager', 'activity_log', 'visual_builder'],
        ],
    ],
];
