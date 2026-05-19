<?php

namespace App\Helpers;

class SettingsHelper
{
    /**
     * Get a setting value from .env ONLY
     * Priority: .env > default (NO DATABASE)
     */
    public static function get(string $key, $default = null)
    {
        // Convert key to uppercase for .env lookup
        $envKey = strtoupper($key);
        
        // Get from .env
        $envValue = env($envKey);
        
        // Return env value or default
        return $envValue !== null ? $envValue : $default;
    }
    
    /**
     * Get multiple settings at once
     */
    public static function getMany(array $keys): array
    {
        $result = [];
        foreach ($keys as $key => $default) {
            if (is_numeric($key)) {
                // If no default provided
                $result[$default] = self::get($default);
            } else {
                // If default provided
                $result[$key] = self::get($key, $default);
            }
        }
        return $result;
    }
    
    /**
     * Get all settings for a specific prefix
     */
    public static function getByPrefix(string $prefix): array
    {
        $result = [];
        
        // Get all env variables with this prefix
        foreach ($_ENV as $key => $value) {
            if (str_starts_with($key, strtoupper($prefix))) {
                $settingKey = strtolower($key);
                $result[$settingKey] = $value;
            }
        }
        
        return $result;
    }
    
    /**
     * Get all settings for views (About Us, Store, etc.)
     * Priority: Database > .env > default
     */
    public static function getAllForView(): array
    {
        // Get all settings from database
        $dbSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
        
        return [
            // Store Settings (Database first, then .env, then default)
            'store_name' => $dbSettings['store_name'] ?? self::get('store_name', 'PharaShop'),
            'store_icon' => $dbSettings['store_icon'] ?? self::get('store_icon', 'fa-store'),
            'store_phone' => $dbSettings['store_phone'] ?? self::get('store_phone', '+855 12 345 678'),
            'store_email' => $dbSettings['store_email'] ?? self::get('store_email', 'info@pharashop.com'),
            'store_address' => $dbSettings['store_address'] ?? self::get('store_address', 'Phnom Penh, Cambodia'),
            'store_logo' => $dbSettings['store_logo'] ?? self::get('store_logo'),
            
            // About Us Hero
            'about_hero_title' => $dbSettings['about_hero_title'] ?? self::get('about_hero_title', 'About Us'),
            'about_hero_subtitle' => $dbSettings['about_hero_subtitle'] ?? self::get('about_hero_subtitle', 'Your trusted partner in mobile technology'),
            
            // About Us Story
            'about_story_title' => $dbSettings['about_story_title'] ?? self::get('about_story_title', 'Our Story'),
            'about_story_content' => $dbSettings['about_story_content'] ?? self::get('about_story_content', 'Welcome to PharaShop, your premier destination for smartphones and accessories.'),
            
            // About Us Statistics
            'about_stat_customers' => $dbSettings['about_stat_customers'] ?? self::get('about_stat_customers', '1000+'),
            'about_stat_products' => $dbSettings['about_stat_products'] ?? self::get('about_stat_products', '500+'),
            'about_stat_authentic' => $dbSettings['about_stat_authentic'] ?? self::get('about_stat_authentic', '100%'),
            'about_stat_support' => $dbSettings['about_stat_support'] ?? self::get('about_stat_support', '24/7'),
            
            // CV/Resume Settings
            'about_cv_enabled' => $dbSettings['about_cv_enabled'] ?? self::get('about_cv_enabled', '0'),
            'about_cv_name' => $dbSettings['about_cv_name'] ?? self::get('about_cv_name'),
            'about_cv_position' => $dbSettings['about_cv_position'] ?? self::get('about_cv_position'),
            'about_cv_email' => $dbSettings['about_cv_email'] ?? self::get('about_cv_email'),
            'about_cv_phone' => $dbSettings['about_cv_phone'] ?? self::get('about_cv_phone'),
            'about_cv_bio' => $dbSettings['about_cv_bio'] ?? self::get('about_cv_bio'),
            'about_cv_skills' => $dbSettings['about_cv_skills'] ?? self::get('about_cv_skills'),
            'about_cv_education' => $dbSettings['about_cv_education'] ?? self::get('about_cv_education'),
            'about_cv_experience' => $dbSettings['about_cv_experience'] ?? self::get('about_cv_experience'),
            'about_cv_photo' => $dbSettings['about_cv_photo'] ?? self::get('about_cv_photo'),
            
            // Footer Settings
            'footer_about' => $dbSettings['footer_about'] ?? self::get('footer_about', 'Your trusted destination for premium smartphones and accessories.'),
            'footer_copyright' => $dbSettings['footer_copyright'] ?? self::get('footer_copyright', '© 2026'),
            'footer_text' => $dbSettings['footer_text'] ?? self::get('footer_text', 'PharaShop. All rights reserved.'),
            'footer_facebook' => $dbSettings['footer_facebook'] ?? self::get('footer_facebook'),
            'footer_twitter' => $dbSettings['footer_twitter'] ?? self::get('footer_twitter'),
            'footer_instagram' => $dbSettings['footer_instagram'] ?? self::get('footer_instagram'),
            'footer_linkedin' => $dbSettings['footer_linkedin'] ?? self::get('footer_linkedin'),
            'footer_quick_links' => $dbSettings['footer_quick_links'] ?? self::get('footer_quick_links', '[{"title":"Home","url":"/","icon":"fa-home"},{"title":"About Us","url":"/about","icon":"fa-info-circle"},{"title":"Shop","url":"/shop","icon":"fa-store"},{"title":"Categories","url":"/categories","icon":"fa-list"},{"title":"Contact Us","url":"/contact","icon":"fa-envelope"}]'),
            'footer_support_links' => $dbSettings['footer_support_links'] ?? self::get('footer_support_links', '[{"title":"Help Center","url":"#","icon":"fa-headset"},{"title":"Shipping Info","url":"#","icon":"fa-box"},{"title":"Returns","url":"#","icon":"fa-undo"},{"title":"FAQ","url":"#","icon":"fa-question"}]'),
            'footer_section_titles' => $dbSettings['footer_section_titles'] ?? self::get('footer_section_titles', '{"quick_links":"Quick Links","support":"Support","follow_us":"Follow Us"}'),
        ];
    }
}
