<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class FooterSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'footer_about', 'value' => 'Your trusted destination for premium smartphones and accessories with the best prices and quality assurance.'],
            ['key' => 'footer_facebook', 'value' => 'https://facebook.com/phoneshop'],
            ['key' => 'footer_twitter', 'value' => 'https://twitter.com/phoneshop'],
            ['key' => 'footer_instagram', 'value' => 'https://instagram.com/phoneshop'],
            ['key' => 'footer_linkedin', 'value' => 'https://linkedin.com/company/phoneshop'],
            ['key' => 'footer_quick_links', 'value' => '[{"title":"Home","url":"/","icon":"fa-home"},{"title":"About Us","url":"/about","icon":"fa-info-circle"},{"title":"Shop","url":"/shop","icon":"fa-store"},{"title":"Categories","url":"/categories","icon":"fa-list"},{"title":"Contact Us","url":"/contact","icon":"fa-envelope"}]'],
            ['key' => 'footer_support_links', 'value' => '[{"title":"Help Center","url":"#","icon":"fa-headset"},{"title":"Shipping Info","url":"#","icon":"fa-box"},{"title":"Returns","url":"#","icon":"fa-undo"},{"title":"FAQ","url":"#","icon":"fa-question"}]'],
            ['key' => 'footer_section_titles', 'value' => '{"quick_links":"Quick Links","support":"Support","follow_us":"Follow Us"}'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
