<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = [
            'store_name' => Setting::get('store_name', 'Bombay Fashion'),
            'store_tagline' => Setting::get('store_tagline', 'Amritsar\'s Premium Clothing & Uniform Destination'),
            'store_address' => Setting::get('store_address', 'Mahila College Road, opposite Mahila College, Amritsar, Punjab, India'),
            'store_phone' => Setting::get('store_phone', '+91 98765 43210'),
            'store_whatsapp' => Setting::get('store_whatsapp', '+919876543210'),
            'store_email' => Setting::get('store_email', 'care@bombayfashionamritsar.com'),
            'store_timings' => Setting::get('store_timings', 'Open daily, closes 9:30 PM'),
            'store_map_embed' => Setting::get('store_map_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3396.953835773837!2d74.8722!3d31.6340!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x391964aa569e7355%3A0x86b0394b92b6a7d!2sMahila%20College%20Rd%2C%20Amritsar%2C%20Punjab!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin'),

            'razorpay_enabled' => Setting::get('razorpay_enabled', true),
            'cod_enabled' => Setting::get('cod_enabled', true),
            'cod_max_limit' => Setting::get('cod_max_limit', 10000.0),

            'tax_rate' => Setting::get('tax_rate', 5.0),
            'tax_name' => Setting::get('tax_name', 'GST'),
            'shipping_charge' => Setting::get('shipping_charge', 50.0),
            'free_shipping_threshold' => Setting::get('free_shipping_threshold', 999.0),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_tagline' => 'nullable|string|max:255',
            'store_address' => 'required|string|max:500',
            'store_phone' => 'required|string|max:25',
            'store_whatsapp' => 'required|string|max:25',
            'store_email' => 'required|email|max:255',
            'store_timings' => 'required|string|max:255',
            'store_map_embed' => 'nullable|string',

            'razorpay_enabled' => 'nullable|boolean',
            'cod_enabled' => 'nullable|boolean',
            'cod_max_limit' => 'required|numeric|min:0',

            'tax_rate' => 'required|numeric|min:0|max:100',
            'tax_name' => 'required|string|max:50',
            'shipping_charge' => 'required|numeric|min:0',
            'free_shipping_threshold' => 'required|numeric|min:0',
        ]);

        Setting::set('store_name', $validated['store_name'], 'text', 'store');
        Setting::set('store_tagline', $validated['store_tagline'] ?? '', 'text', 'store');
        Setting::set('store_address', $validated['store_address'], 'text', 'store');
        Setting::set('store_phone', $validated['store_phone'], 'text', 'store');
        Setting::set('store_whatsapp', $validated['store_whatsapp'], 'text', 'store');
        Setting::set('store_email', $validated['store_email'], 'text', 'store');
        Setting::set('store_timings', $validated['store_timings'], 'text', 'store');
        if (!empty($validated['store_map_embed'])) {
            Setting::set('store_map_embed', $validated['store_map_embed'], 'text', 'store');
        }

        Setting::set('razorpay_enabled', $request->boolean('razorpay_enabled'), 'boolean', 'payment');
        Setting::set('cod_enabled', $request->boolean('cod_enabled'), 'boolean', 'payment');
        Setting::set('cod_max_limit', (float) $validated['cod_max_limit'], 'number', 'payment');

        Setting::set('tax_rate', (float) $validated['tax_rate'], 'number', 'tax');
        Setting::set('tax_name', $validated['tax_name'], 'text', 'tax');
        Setting::set('shipping_charge', (float) $validated['shipping_charge'], 'number', 'shipping');
        Setting::set('free_shipping_threshold', (float) $validated['free_shipping_threshold'], 'number', 'shipping');

        ActivityLog::log('settings_updated', null, null, ['updated_by' => auth()->user()->name]);

        return back()->with('success', 'Store and payment settings updated successfully!');
    }
}
