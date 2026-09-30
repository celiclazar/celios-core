<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'App\\Models\\User' => 'Celios\\Core\\Models\\User',
            'App\\Models\\Post' => 'Celios\\Core\\Models\\Post',
            'App\\Models\\Page' => 'Celios\\Core\\Models\\Page',
            'App\\Models\\Category' => 'Celios\\Core\\Models\\Category',
            'App\\Models\\Form' => 'Celios\\Core\\Models\\Form',
            'App\\Models\\FormSubmission' => 'Celios\\Core\\Models\\FormSubmission',
            'App\\Models\\Document' => 'Celios\\Core\\Models\\Document',
            'App\\Models\\DocumentCategory' => 'Celios\\Core\\Models\\DocumentCategory',
            'App\\Models\\Menu' => 'Celios\\Core\\Models\\Menu',
            'App\\Models\\MenuItem' => 'Celios\\Core\\Models\\MenuItem',
            'App\\Models\\NewsletterSubscriber' => 'Celios\\Core\\Models\\NewsletterSubscriber',
            'App\\Models\\NewsletterCampaign' => 'Celios\\Core\\Models\\NewsletterCampaign',
            'App\\Models\\Setting' => 'Celios\\Core\\Models\\Setting',
        ];

        // 1. model_has_roles
        if (Schema::hasTable('model_has_roles')) {
            foreach ($map as $old => $new) {
                DB::table('model_has_roles')->where('model_type', $old)->update(['model_type' => $new]);
            }
        }

        // 2. model_has_permissions
        if (Schema::hasTable('model_has_permissions')) {
            foreach ($map as $old => $new) {
                DB::table('model_has_permissions')->where('model_type', $old)->update(['model_type' => $new]);
            }
        }

        // 3. activity_log
        if (Schema::hasTable('activity_log')) {
            foreach ($map as $old => $new) {
                DB::table('activity_log')->where('subject_type', $old)->update(['subject_type' => $new]);
                DB::table('activity_log')->where('causer_type', $old)->update(['causer_type' => $new]);
            }
        }

        // 4. media
        if (Schema::hasTable('media')) {
            foreach ($map as $old => $new) {
                DB::table('media')->where('model_type', $old)->update(['model_type' => $new]);
            }
        }
    }

    public function down(): void
    {
        // No-op
    }
};
