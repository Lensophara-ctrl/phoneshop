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
        Schema::table('settings', function (Blueprint $table) {
            $table->text('about_hero_title')->nullable();
            $table->text('about_hero_subtitle')->nullable();
            $table->text('about_story_title')->nullable();
            $table->text('about_story_content')->nullable();
            $table->string('about_stat_customers')->default('1000+');
            $table->string('about_stat_products')->default('500+');
            $table->string('about_stat_authentic')->default('100%');
            $table->string('about_stat_support')->default('24/7');
            $table->text('about_mission')->nullable();
            $table->text('about_vision')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'about_hero_title',
                'about_hero_subtitle',
                'about_story_title',
                'about_story_content',
                'about_stat_customers',
                'about_stat_products',
                'about_stat_authentic',
                'about_stat_support',
                'about_mission',
                'about_vision',
            ]);
        });
    }
};
