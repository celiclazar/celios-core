<?php

use Celios\Core\Filament\Blocks\DocumentsBlock;
use Celios\Core\Filament\Blocks\FormBlock;
use Celios\Core\Filament\Blocks\NewsletterBlock;
use Celios\Core\Filament\Blocks\PostsBlock;
use Celios\Core\Filament\Pages\BackupManager;
use Celios\Core\Filament\Pages\NewsletterSettingsPage;
use Celios\Core\Filament\Pages\QueueManager;
use Celios\Core\Filament\Resources\Activities\ActivityResource;
use Celios\Core\Filament\Resources\Categories\CategoryResource;
use Celios\Core\Filament\Resources\DocumentCategories\DocumentCategoryResource;
use Celios\Core\Filament\Resources\Documents\DocumentResource;
use Celios\Core\Filament\Resources\Forms\FormResource;
use Celios\Core\Filament\Resources\Forms\FormSubmissionResource;
use Celios\Core\Filament\Resources\Newsletter\Campaigns\NewsletterCampaignResource;
use Celios\Core\Filament\Resources\Newsletter\Subscribers\NewsletterSubscriberResource;
use Celios\Core\Filament\Resources\Posts\PostResource;
use Modules\Crm\Filament\Resources\Contacts\ContactResource;
use Modules\Payment\Filament\Resources\PaymentGatewayResource;
use Modules\Payment\Filament\Resources\PaymentResource;

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
                PostResource::class,
                CategoryResource::class,
            ],
            'blocks' => [
                PostsBlock::class,
            ],
        ],

        'forms' => [
            'name' => 'Custom Form Builder',
            'description' => 'Create dynamic forms and collect user submissions with spam protection.',
            'icon' => 'heroicon-o-clipboard-document-list',
            'resources' => [
                FormResource::class,
                FormSubmissionResource::class,
            ],
            'blocks' => [
                FormBlock::class,
            ],
        ],

        'documents' => [
            'name' => 'Document Library',
            'description' => 'Manage downloadable files, categories, and tiered access levels.',
            'icon' => 'heroicon-o-document-duplicate',
            'resources' => [
                DocumentResource::class,
                DocumentCategoryResource::class,
            ],
            'blocks' => [
                DocumentsBlock::class,
            ],
        ],

        'newsletter' => [
            'name' => 'Newsletter Marketing Engine',
            'description' => 'Manage subscribers, campaigns, delivery tracking, and double opt-in.',
            'icon' => 'heroicon-o-envelope',
            'resources' => [
                NewsletterCampaignResource::class,
                NewsletterSubscriberResource::class,
            ],
            'pages' => [
                NewsletterSettingsPage::class,
            ],
            'blocks' => [
                NewsletterBlock::class,
            ],
        ],

        'payment' => [
            'name' => 'Payment Processing',
            'description' => 'Process online payments, manage gateways (Stripe, Bank Transfer), and view audit logs.',
            'icon' => 'heroicon-o-credit-card',
            'resources' => [
                PaymentResource::class,
                PaymentGatewayResource::class,
            ],
        ],

        'backups' => [
            'name' => 'System Backups',
            'description' => 'Create, monitor, and download automated database and file backups.',
            'icon' => 'heroicon-o-circle-stack',
            'pages' => [
                BackupManager::class,
            ],
        ],

        'queue_manager' => [
            'name' => 'Queue Inspector',
            'description' => 'Monitor background job queues, view metrics, and retry failed jobs.',
            'icon' => 'heroicon-o-queue-list',
            'pages' => [
                QueueManager::class,
            ],
        ],

        'activity_log' => [
            'name' => 'Audit & Activity Log',
            'description' => 'Detailed audit trail of all content updates, user logins, and administrative actions.',
            'icon' => 'heroicon-o-clipboard-document-check',
            'resources' => [
                ActivityResource::class,
            ],
        ],

        'crm' => [
            'name' => 'CRM & Leads',
            'description' => 'Manage customer leads, contact pipeline, interaction notes, and form-to-lead conversions.',
            'icon' => 'heroicon-o-user-group',
            'resources' => [
                ContactResource::class,
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
