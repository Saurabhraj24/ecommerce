<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $heroBanners = Banner::active()->position('hero')->get();
        $middleBanners = Banner::active()->position('middle')->get();

        $categories = Category::active()
            ->root()
            ->with(['activeChildren'])
            ->orderBy('display_order')
            ->get();

        // Featured products from real DB
        $featuredProducts = Product::active()
            ->featured()
            ->with(['category', 'primaryImage', 'images', 'variants'])
            ->latest()
            ->take(8)
            ->get();

        // New arrivals from real DB
        $newArrivals = Product::active()
            ->newArrivals()
            ->with(['category', 'primaryImage', 'images', 'variants'])
            ->latest()
            ->take(8)
            ->get();

        // Best sellers from real DB
        $bestSellers = Product::active()
            ->bestSellers()
            ->with(['category', 'primaryImage', 'images', 'variants'])
            ->latest()
            ->take(8)
            ->get();

        // Seasonal collections
        $winterWear = Product::active()
            ->season('winter')
            ->with(['category', 'primaryImage', 'images'])
            ->latest()
            ->take(6)
            ->get();

        $summerWear = Product::active()
            ->season('summer')
            ->with(['category', 'primaryImage', 'images'])
            ->latest()
            ->take(6)
            ->get();

        // Real customer reviews from DB
        $testimonials = Review::approved()
            ->where('rating', '>=', 4)
            ->with(['user', 'product'])
            ->latest()
            ->take(6)
            ->get();

        // Store location & details from DB settings
        $store = [
            'name' => Setting::get('store_name', 'Bombay Fashion'),
            'tagline' => Setting::get('store_tagline', 'Amritsar\'s Premium Clothing & Uniform Destination'),
            'address' => Setting::get('store_address', 'Mahila College Road, opposite Mahila College, Amritsar, Punjab, India'),
            'phone' => Setting::get('store_phone', '+91 98765 43210'),
            'whatsapp' => Setting::get('store_whatsapp', '+919876543210'),
            'email' => Setting::get('store_email', 'care@bombayfashionamritsar.com'),
            'timings' => Setting::get('store_timings', 'Open daily, closes 9:30 PM'),
            'map_embed' => Setting::get('store_map_embed', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3396.953835773837!2d74.8722!3d31.6340!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x391964aa569e7355%3A0x86b0394b92b6a7d!2sMahila%20College%20Rd%2C%20Amritsar%2C%20Punjab!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin'),
        ];

        return view('home', compact(
            'heroBanners',
            'middleBanners',
            'categories',
            'featuredProducts',
            'newArrivals',
            'bestSellers',
            'winterWear',
            'summerWear',
            'testimonials',
            'store'
        ));
    }
}
