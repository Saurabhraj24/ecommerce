<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::active()->where('slug', $slug)->firstOrFail();
        return view('pages.show', compact('page'));
    }

    public function contact(): View
    {
        $store = [
            'name' => Setting::get('store_name', 'Bombay Fashion'),
            'address' => Setting::get('store_address', 'Mahila College Road, opposite Mahila College, Amritsar, Punjab, India'),
            'phone' => Setting::get('store_phone', '+91 98765 43210'),
            'whatsapp' => Setting::get('store_whatsapp', '+919876543210'),
            'email' => Setting::get('store_email', 'care@bombayfashionamritsar.com'),
            'timings' => Setting::get('store_timings', 'Open daily, closes 9:30 PM'),
            'map_embed' => Setting::get('store_map_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3396.953835773837!2d74.8722!3d31.6340!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x391964aa569e7355%3A0x86b0394b92b6a7d!2sMahila%20College%20Rd%2C%20Amritsar%2C%20Punjab!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin'),
        ];

        return view('pages.contact', compact('store'));
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:2000',
        ]);

        ContactMessage::create($validated);

        return back()->with('success', 'Thank you for reaching out to Bombay Fashion! Our team will get back to you shortly.');
    }
}
