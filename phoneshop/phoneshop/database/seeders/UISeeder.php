<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UISeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Slide::truncate();
        \App\Models\Setting::truncate();

        \App\Models\Slide::create([
            'title' => 'Discover the Next Generation of Mobile',
            'description' => 'Explore our curated collection of the latest smartphones with cutting-edge technology and exceptional design.',
            'button_text' => 'Shop Now',
            'button_link' => '#latest-phones',
            'is_active' => true,
            'order' => 1,
        ]);

        \App\Models\Slide::create([
            'title' => 'Exclusive iPhone Deals',
            'description' => 'Get the latest iPhone models at unbeatable prices. Limited time offer!',
            'button_text' => 'View iPhones',
            'button_link' => '/category/1',
            'is_active' => true,
            'order' => 2,
        ]);

        \App\Models\Setting::create(['key' => 'footer_text', 'value' => 'PhoneShop. All rights reserved.']);
        \App\Models\Setting::create(['key' => 'footer_copyright', 'value' => '&copy; '.date('Y')]);
        \App\Models\Setting::create(['key' => 'store_name', 'value' => 'PhoneShop']);
        \App\Models\Setting::create(['key' => 'footer_about', 'value' => 'Your premier destination for the latest smartphones and mobile accessories. We provide high-quality products with exceptional customer service.']);
        \App\Models\Setting::create(['key' => 'contact_address', 'value' => '123 Mobile Street, Tech City']);
        \App\Models\Setting::create(['key' => 'contact_phone', 'value' => '+855 12 345 678']);
        \App\Models\Setting::create(['key' => 'contact_email', 'value' => 'info@phoneshop.com']);
        \App\Models\Setting::create(['key' => 'social_facebook', 'value' => 'https://facebook.com']);
        \App\Models\Setting::create(['key' => 'social_instagram', 'value' => 'https://instagram.com']);
        \App\Models\Setting::create(['key' => 'social_telegram', 'value' => 'https://t.me']);
    }
}
