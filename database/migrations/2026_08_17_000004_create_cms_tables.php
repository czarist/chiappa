<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('slug')->unique();
            $table->json('settings')->nullable(); $table->boolean('active')->default(false); $table->timestamps();
        });
        Schema::create('pages', function (Blueprint $table) {
            $table->id(); $table->string('title'); $table->string('slug')->unique();
            $table->string('status')->default('draft')->index(); $table->string('template')->default('default');
            $table->string('featured_image')->nullable(); $table->timestamp('published_at')->nullable()->index(); $table->timestamps();
        });
        Schema::create('seo_metadata', function (Blueprint $table) {
            $table->id(); $table->foreignId('page_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('title')->nullable(); $table->text('description')->nullable(); $table->string('canonical_url')->nullable();
            $table->boolean('indexable')->default(true); $table->boolean('followable')->default(true);
            $table->string('og_title')->nullable(); $table->text('og_description')->nullable(); $table->string('og_image')->nullable();
            $table->string('twitter_card')->default('summary_large_image'); $table->timestamps();
        });
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id(); $table->foreignId('page_id')->constrained()->cascadeOnDelete(); $table->string('type');
            $table->unsignedInteger('position')->default(0); $table->boolean('active')->default(true);
            $table->json('content')->nullable(); $table->json('settings')->nullable(); $table->timestamps(); $table->index(['page_id', 'position']);
        });
        Schema::create('settings', function (Blueprint $table) { $table->id(); $table->string('key')->unique(); $table->json('value')->nullable(); $table->timestamps(); });
        Schema::create('media', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); $table->string('name'); $table->string('path');
            $table->string('mime_type'); $table->unsignedBigInteger('size')->default(0); $table->string('alt_text')->nullable(); $table->timestamps();
        });
        Schema::create('menus', function (Blueprint $table) { $table->id(); $table->string('name'); $table->string('location')->default('primary'); $table->timestamps(); });
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('menu_id')->constrained()->cascadeOnDelete(); $table->foreignId('page_id')->nullable()->constrained()->nullOnDelete();
            $table->string('label'); $table->string('url')->nullable(); $table->string('target')->default('_self'); $table->unsignedInteger('position')->default(0);
            $table->boolean('active')->default(true); $table->timestamps(); $table->index(['menu_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items'); Schema::dropIfExists('menus'); Schema::dropIfExists('media'); Schema::dropIfExists('settings');
        Schema::dropIfExists('page_sections'); Schema::dropIfExists('seo_metadata'); Schema::dropIfExists('pages'); Schema::dropIfExists('themes');
    }
};
