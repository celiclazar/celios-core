<?php

return [
    'title' => 'Brands & Domains',
    'single' => 'Brand',
    'plural' => 'Brands',

    // Tabs
    'tab_identity' => 'Brand & Identity',
    'tab_media' => 'Logos & Media',
    'tab_contact' => 'Contact & Legal',
    'tab_localization' => 'Languages & Currencies',

    // Section headings & descriptions
    'identity_section' => 'Brand Information',
    'identity_desc' => 'Core identity and domain routing for this brand.',
    'media_section' => 'Brand Imagery & Icons',
    'media_desc' => 'High-resolution logo files for light and dark environments, plus the browser favicon.',
    'contact_section' => 'Official Contact & Business Entity',
    'contact_desc' => 'Public contact details, office location, and social media channels.',
    'localization_section' => 'Regional & Internationalization',
    'localization_desc' => 'Define default and supported languages and currencies for this brand.',

    // Fields
    'name' => 'Brand Name',
    'slug' => 'Identifier Slug',
    'headline' => 'Headline / Slogan',
    'description' => 'Description / Meta Bio',
    'copyright_text' => 'Copyright Notice',
    'primary_domain' => 'Primary Domain',
    'primary_domain_helper' => 'e.g. brand.com or localhost. Used for incoming domain matching.',
    'domains' => 'Additional Domains / Aliases',
    'domains_helper' => 'Alternative domains or subdomains mapped to this brand (e.g. brand.co.uk).',
    'is_default' => 'Default Brand',
    'is_default_helper' => 'The default fallback brand when no specific domain matches.',
    'is_active' => 'Active Status',

    // Media
    'logo_light' => 'Light Mode Logo',
    'logo_dark' => 'Dark Mode Logo',
    'favicon' => 'Website Favicon',

    // Contact
    'company_name' => 'Legal Company Name',
    'email' => 'Public Contact Email',
    'phone' => 'Customer Service Phone',
    'address' => 'Street Address',
    'city' => 'City',
    'postal_code' => 'Postal / ZIP Code',
    'country' => 'Country',
    'tax_id' => 'Tax / VAT Number',
    'social_links' => 'Social Media Profiles',

    // Localization
    'default_locale' => 'Default Language',
    'supported_locales' => 'Supported Languages',
    'default_currency' => 'Default Currency',
    'supported_currencies' => 'Supported Currencies',
    'currency_symbol_position' => 'Currency Symbol Position',
    'currency_before' => 'Before amount (e.g. $100)',
    'currency_after' => 'After amount (e.g. 100 EUR)',

    // Safeguards
    'cannot_delete_default_brand' => 'You cannot delete the default active brand. Assign another default brand first.',
];
