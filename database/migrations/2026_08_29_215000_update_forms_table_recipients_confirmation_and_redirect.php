<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->json('recipient_user_ids')->nullable()->after('fields');
            $table->boolean('send_confirmation_email')->default(false)->after('recipient_user_ids');
            $table->json('confirmation_email_subject')->nullable()->after('send_confirmation_email');
            $table->json('confirmation_email_body')->nullable()->after('confirmation_email_subject');
            $table->string('redirect_type')->default('none')->after('confirmation_email_body');
            $table->foreignId('redirect_page_id')->nullable()->after('redirect_type')->constrained('pages')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropForeign(['redirect_page_id']);
            $table->dropColumn([
                'recipient_user_ids',
                'send_confirmation_email',
                'confirmation_email_subject',
                'confirmation_email_body',
                'redirect_type',
                'redirect_page_id',
            ]);
        });
    }
};
