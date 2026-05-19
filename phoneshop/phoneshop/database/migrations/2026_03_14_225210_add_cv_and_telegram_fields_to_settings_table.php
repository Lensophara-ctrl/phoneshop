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
            // CV/Resume fields
            $table->boolean('about_cv_enabled')->default(false)->after('about_vision');
            $table->string('about_cv_photo')->nullable()->after('about_cv_enabled');
            $table->string('about_cv_name')->nullable()->after('about_cv_photo');
            $table->string('about_cv_position')->nullable()->after('about_cv_name');
            $table->string('about_cv_email')->nullable()->after('about_cv_position');
            $table->string('about_cv_phone')->nullable()->after('about_cv_email');
            $table->text('about_cv_bio')->nullable()->after('about_cv_phone');
            $table->text('about_cv_skills')->nullable()->after('about_cv_bio');
            $table->text('about_cv_education')->nullable()->after('about_cv_skills');
            $table->text('about_cv_experience')->nullable()->after('about_cv_education');
            
            // Telegram notification settings
            $table->boolean('telegram_enabled')->default(false)->after('about_cv_experience');
            $table->string('telegram_bot_token')->nullable()->after('telegram_enabled');
            $table->string('telegram_chat_id')->nullable()->after('telegram_bot_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'about_cv_enabled',
                'about_cv_photo',
                'about_cv_name',
                'about_cv_position',
                'about_cv_email',
                'about_cv_phone',
                'about_cv_bio',
                'about_cv_skills',
                'about_cv_education',
                'about_cv_experience',
                'telegram_enabled',
                'telegram_bot_token',
                'telegram_chat_id',
            ]);
        });
    }
};
