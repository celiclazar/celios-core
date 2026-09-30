<?php

return [
    // Admin Settings Page
    'title' => 'Cookie Management',
    'subtitle' => 'Configure GDPR-compliant cookie consent banners, privacy policies, and tracking scripts.',
    'settings_saved' => 'Cookie settings saved successfully!',
    'settings_saved_body' => 'Consent banner configuration and script rules have been updated.',

    // General Section
    'section_general' => 'Consent Banner Configuration',
    'section_general_desc' => 'Control the visibility, layout, and legal parameters of the cookie consent banner.',
    'enabled' => 'Enable Cookie Consent Banner',
    'enabled_help' => 'When enabled, first-time visitors will be presented with a cookie consent banner and script blocker.',
    'position' => 'Banner Position',
    'position_bottom_banner' => 'Bottom Bar (Full Width)',
    'position_bottom_right' => 'Floating Card (Bottom Right)',
    'position_bottom_left' => 'Floating Card (Bottom Left)',
    'position_center_modal' => 'Centered Modal Dialog',
    'expiry_days' => 'Consent Expiration (Days)',
    'expiry_days_help' => 'Number of days before the consent cookie expires and the visitor is prompted again (Default: 365 days).',
    'privacy_url' => 'Privacy Policy URL',
    'privacy_url_help' => 'Relative or absolute URL to your privacy/cookie policy page (e.g., /en/privacy-policy).',

    // Content Section
    'section_content' => 'Banner & Modal Texts',
    'section_content_desc' => 'Customize the title and descriptions displayed to website visitors.',
    'banner_title' => 'Banner Title',
    'banner_title_default' => 'We value your privacy',
    'banner_description' => 'Banner Description',
    'banner_description_default' => 'We use cookies to enhance your browsing experience, serve personalized content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.',

    // Categories Section
    'section_categories' => 'Cookie Categories & Script Management',
    'section_categories_desc' => 'Configure individual cookie categories and their associated tracking/marketing scripts.',

    // Necessary
    'cat_necessary' => 'Essential / Necessary Cookies',
    'cat_necessary_desc' => 'These cookies are essential for the website to function securely and cannot be disabled (e.g. sessions, CSRF security, authentication, system preferences).',
    'cat_necessary_always_active' => 'Always Active',

    // Analytics
    'cat_analytics' => 'Analytics & Performance',
    'cat_analytics_desc' => 'Allow us to count visits and traffic sources to measure and improve website performance (e.g. Google Analytics, Plausible, Matomo).',
    'cat_analytics_enabled' => 'Enable Analytics Category',
    'analytics_head_scripts' => 'Analytics Scripts (Executed on Consent)',
    'analytics_head_scripts_help' => 'Custom JavaScript or HTML snippets (such as Google Analytics / GTAG / Matomo) that will be injected ONLY after the visitor consents to Analytics cookies.',

    // Marketing
    'cat_marketing' => 'Marketing & Advertising',
    'cat_marketing_desc' => 'Used by advertising partners to build a profile of your interests and show you relevant adverts on other sites (e.g. Meta Pixel, Google Ads, LinkedIn Insight).',
    'cat_marketing_enabled' => 'Enable Marketing Category',
    'marketing_head_scripts' => 'Marketing / Pixel Scripts (Executed on Consent)',
    'marketing_head_scripts_help' => 'Marketing tags (such as Meta Pixel / Facebook Pixel, Google Ads remarketing) that will be injected ONLY after the visitor consents to Marketing cookies.',

    // Functional
    'cat_functional' => 'Functional & Preferences',
    'cat_functional_desc' => 'Enable enhanced functionality and personalization, such as remembering language preferences, video players, and user interface customizations.',
    'cat_functional_enabled' => 'Enable Functional Category',
    'functional_scripts' => 'Functional Scripts (Executed on Consent)',
    'functional_scripts_help' => 'Functional scripts (such as chat widgets, custom embeds) that will be injected ONLY after the visitor consents to Functional cookies.',

    // Frontend UI Buttons & Labels
    'ui_accept_all' => 'Accept All',
    'ui_reject_non_essential' => 'Reject Non-Essential',
    'ui_customize' => 'Cookie Preferences',
    'ui_save_preferences' => 'Save Preferences',
    'ui_preferences_title' => 'Cookie Preferences',
    'ui_preferences_subtitle' => 'Manage your cookie settings. You can change your preferences at any time.',
    'ui_privacy_link' => 'Privacy Policy',
    'ui_cookie_settings_link' => 'Cookie Settings',
    'ui_cookies_saved_alert' => 'Your cookie preferences have been saved.',
    'ui_close' => 'Close',
    'status_enabled' => 'Allowed',
    'status_disabled' => 'Blocked',
];
