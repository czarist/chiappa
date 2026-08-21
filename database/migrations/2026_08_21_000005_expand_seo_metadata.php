<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_metadata', function (Blueprint $table) {
            $table->string('keywords')->nullable()->after('description');
            $table->string('author')->nullable()->after('keywords');
            $table->string('og_image_alt')->nullable()->after('og_image');
            $table->string('twitter_title')->nullable()->after('twitter_card');
            $table->string('twitter_description', 320)->nullable()->after('twitter_title');
            $table->string('twitter_image')->nullable()->after('twitter_description');
        });
    }

    public function down(): void
    {
        Schema::table('seo_metadata', function (Blueprint $table) {
            $table->dropColumn(['keywords', 'author', 'og_image_alt', 'twitter_title', 'twitter_description', 'twitter_image']);
        });
    }
};
