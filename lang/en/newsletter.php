<?php

return [
    // Public block & subscription
    'block_title' => 'Newsletter Signup',
    'block_heading' => 'Heading',
    'block_subtitle' => 'Subtitle',
    'block_description' => 'Description',
    'block_button_text' => 'Button Text',
    'block_disclaimer' => 'Privacy Disclaimer',
    'block_default_title' => 'Stay updated with our latest news',
    'block_default_subtitle' => 'Subscribe to our newsletter and receive exclusive updates, insights, and stories directly in your inbox.',
    'block_default_description' => 'Subscribe to our newsletter and receive exclusive updates, insights, and stories directly in your inbox.',
    'block_default_button' => 'Subscribe',
    'block_default_disclaimer' => 'We respect your privacy. Unsubscribe at any time with one click.',
    'email_placeholder' => 'Enter your email address...',
    'subscribed_success' => 'Thank you for subscribing!',
    'already_subscribed' => 'You are already subscribed to our newsletter.',
    'confirm_email_sent' => 'Please check your email inbox and confirm your subscription.',
    'submission_failed' => 'Something went wrong. Please try again.',

    // Verification & Unsubscribe public pages
    'confirm_subject' => 'Please confirm your subscription to :app',
    'confirm_title' => 'Confirm Your Subscription',
    'confirm_greeting' => 'Hello :name,',
    'confirm_instruction' => 'Thank you for signing up for our newsletter. Please click the button below to verify your email address and activate your subscription.',
    'confirm_button' => 'Confirm My Subscription',
    'confirm_trouble' => 'If you are having trouble clicking the button, copy and paste this URL into your browser:',
    'confirm_ignore' => 'If you did not request this subscription, no further action is needed and you will not receive any emails.',
    'sent_to' => 'This email was sent to :email',

    'verified_page_title' => 'Subscription Confirmed',
    'verify_success_title' => 'Subscription Confirmed!',
    'verify_success_message' => 'Thank you! Your email has been verified and your newsletter subscription is now active.',
    'verify_invalid_or_expired' => 'This verification link is invalid, has expired, or has already been used.',
    'notice' => 'Notice',
    'back_to_home' => 'Back to Homepage',

    'unsubscribed_page_title' => 'Unsubscribed',
    'unsubscribed_title' => 'You Have Been Unsubscribed',
    'unsubscribed_desc' => ':email has been successfully removed from our mailing list. You will no longer receive marketing emails from us.',
    'unsubscribed_success' => 'You have been successfully unsubscribed.',

    // Email layout & campaign
    'footer_reason' => 'You are receiving this email because you subscribed to updates on our website.',
    'unsubscribe' => 'Unsubscribe',

    // Test connection
    'test_connection_subject' => 'Test Email from Celios CMS Newsletter',
    'test_connection_body' => "This is a test email sent from :app to verify your newsletter mail configuration.\n\nTimestamp: :date\nIf you received this email, your mailing configuration is working correctly!",
    'test_connection_success' => 'Test email sent successfully!',
    'test_connection_failed' => 'Failed to send test email: :error',

    // Statuses
    'status_pending' => 'Pending Confirmation',
    'status_active' => 'Active',
    'status_unsubscribed' => 'Unsubscribed',
    'status_bounced' => 'Bounced',

    'campaign_status_draft' => 'Draft',
    'campaign_status_scheduled' => 'Scheduled',
    'campaign_status_sending' => 'Sending',
    'campaign_status_sent' => 'Sent',
    'campaign_status_failed' => 'Failed',
    'campaign_status_cancelled' => 'Cancelled',
];
