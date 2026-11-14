<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Store details
            ['key' => 'store_name', 'value' => 'Bombay Fashion', 'type' => 'text', 'group' => 'store'],
            ['key' => 'store_tagline', 'value' => 'Amritsar\'s Premium Clothing & Uniform Destination', 'type' => 'text', 'group' => 'store'],
            ['key' => 'store_address', 'value' => 'Mahila College Road, opposite Mahila College, Amritsar, Punjab, India', 'type' => 'text', 'group' => 'store'],
            ['key' => 'store_phone', 'value' => '+91 98765 43210', 'type' => 'text', 'group' => 'store'],
            ['key' => 'store_whatsapp', 'value' => '+919876543210', 'type' => 'text', 'group' => 'store'],
            ['key' => 'store_email', 'value' => 'care@bombayfashionamritsar.com', 'type' => 'text', 'group' => 'store'],
            ['key' => 'store_timings', 'value' => 'Open daily, closes 9:30 PM', 'type' => 'text', 'group' => 'store'],
            ['key' => 'store_map_embed', 'value' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3396.953835773837!2d74.8722!3d31.6340!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x391964aa569e7355%3A0x86b0394b92b6a7d!2sMahila%20College%20Rd%2C%20Amritsar%2C%20Punjab!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin', 'type' => 'text', 'group' => 'store'],

            // Payment settings
            ['key' => 'razorpay_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'payment'],
            ['key' => 'cod_enabled', 'value' => '1', 'type' => 'boolean', 'group' => 'payment'],
            ['key' => 'cod_max_limit', 'value' => '10000', 'type' => 'number', 'group' => 'payment'],

            // Tax & shipping
            ['key' => 'tax_rate', 'value' => '5.0', 'type' => 'number', 'group' => 'tax'],
            ['key' => 'tax_name', 'value' => 'GST', 'type' => 'text', 'group' => 'tax'],
            ['key' => 'shipping_charge', 'value' => '50.0', 'type' => 'number', 'group' => 'shipping'],
            ['key' => 'free_shipping_threshold', 'value' => '999.0', 'type' => 'number', 'group' => 'shipping'],
            ['key' => 'currency_symbol', 'value' => '₹', 'type' => 'text', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
