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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('headline')->nullable();
            $table->text('description')->nullable();
            $table->string('copyright_text')->nullable();

            // Multi-domain setup
            $table->string('primary_domain')->nullable()->index();
            $table->json('domains')->nullable();

            // Brand media (Curator UUID references)
            $table->string('logo_light_id', 36)->nullable();
            $table->string('logo_dark_id', 36)->nullable();
            $table->string('favicon_id', 36)->nullable();

            // Contact & Organization Info
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->string('tax_id')->nullable();
            $table->json('social_links')->nullable();

            // Localization & Currencies
            $table->string('default_locale')->default('sr');
            $table->json('supported_locales')->nullable();
            $table->string('default_currency', 3)->default('EUR');
            $table->json('supported_currencies')->nullable();
            $table->string('currency_symbol_position')->default('after');

            // Status & Defaults
            $table->boolean('is_default')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
