@php
    $storeName = \App\Models\Setting::get('store_name', 'Bombay Fashion');
    $storeAddress = \App\Models\Setting::get('store_address', 'Mahila College Road, opposite Mahila College, Amritsar, Punjab, India');
    $storePhone = \App\Models\Setting::get('store_phone', '+91 98765 43210');
    $storeEmail = \App\Models\Setting::get('store_email', 'care@bombayfashionamritsar.com');
    $storeTimings = \App\Models\Setting::get('store_timings', 'Open daily, closes 9:30 PM');
    $footerCategories = \App\Models\Category::active()->root()->take(6)->get();
@endphp

<!-- Light Festive Footer (No Dark Backgrounds) -->
<footer class="bg-gradient-to-b from-cream-100 via-blush/40 to-peach/50 text-plum border-t border-primary-100/70 pt-12 sm:pt-16 pb-28 md:pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Main Footer Columns (2 columns on mobile for Left & Right layout) -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-6 sm:gap-10 pb-10 border-b border-primary-200/50">
            
            <!-- Store Story & Brand (Spans 2 columns on mobile) -->
            <div class="col-span-2 space-y-4">
                <div class="flex items-center space-x-2">
                    <div class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-primary-600 to-primary-400 flex items-center justify-center text-white shadow-md shadow-primary-500/20">
                        <span class="font-serif font-bold text-lg">BF</span>
                    </div>
                    <span class="font-serif text-2xl font-bold tracking-tight text-plum">
                        {{ $storeName }}
                    </span>
                </div>
                <p class="text-xs text-plum/70 leading-relaxed max-w-sm">
                    Amritsar's trusted retail destination for family fashion, designer bridal sarees, suits, festive kurtas, and official school uniforms. Serving generations with authentic Indian textiles and care.
                </p>

                <!-- Store Physical Address & Timings -->
                <div class="space-y-2 pt-1 text-xs text-plum/80">
                    <div class="flex items-start space-x-2.5">
                        <svg class="w-4 h-4 text-primary-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        <span>{{ $storeAddress }}</span>
                    </div>
                    <div class="flex items-center space-x-2.5">
                        <svg class="w-4 h-4 text-gold-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-medium text-emerald-800">{{ $storeTimings }}</span>
                    </div>
                    <div class="flex items-center space-x-2.5">
                        <svg class="w-4 h-4 text-primary-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $storePhone) }}" class="font-semibold text-plum hover:text-primary-600 transition">
                            {{ $storePhone }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Left Column: Collections -->
            <div class="col-span-1 space-y-3">
                <h3 class="font-serif font-bold text-sm text-plum uppercase tracking-wider text-primary-800">
                    Collections
                </h3>
                <ul class="space-y-2 text-xs text-plum/75">
                    @foreach ($footerCategories as $cat)
                        <li>
                            <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="hover:text-primary-600 transition">
                                {{ $cat->name }}
                            </a>
                        </li>
                    @endforeach
                    <li>
                        <a href="{{ route('uniforms.index') }}" class="font-semibold text-primary-600 hover:underline">
                            School Uniforms
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Right Column: Customer Care -->
            <div class="col-span-1 space-y-3">
                <h3 class="font-serif font-bold text-sm text-plum uppercase tracking-wider text-primary-800">
                    Customer Care
                </h3>
                <ul class="space-y-2 text-xs text-plum/75">
                    <li>
                        <a href="{{ route('orders.track') }}" class="hover:text-primary-600 transition flex items-center space-x-1">
                            <span class="text-gold-600 font-bold">•</span>
                            <span>Track Order</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.show', 'shipping-policy') }}" class="hover:text-primary-600 transition">
                            Shipping
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.show', 'return-policy') }}" class="hover:text-primary-600 transition">
                            7-Day Returns
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="hover:text-primary-600 transition">
                            Contact Us
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.show', 'privacy-policy') }}" class="hover:text-primary-600 transition">
                            Privacy Policy
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.show', 'terms-conditions') }}" class="hover:text-primary-600 transition">
                            Terms & Conditions
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Safe Payments & Store Guarantees (Spans 2 columns on mobile) -->
            <div class="col-span-2 lg:col-span-1 space-y-3 pt-2 lg:pt-0">
                <h3 class="font-serif font-bold text-sm text-plum uppercase tracking-wider text-primary-800">
                    Payment & Trust
                </h3>
                <p class="text-xs text-plum/70 leading-relaxed">
                    100% secure checkout via Razorpay with UPI (Google Pay, PhonePe, Paytm), Cards, and Cash on Delivery across India.
                </p>

                <!-- Payment Badges in a flexible 2-row wrap -->
                <div class="flex flex-wrap gap-1.5 pt-1">
                    <span class="px-2 py-1 rounded-md bg-white border border-primary-100 text-[10px] font-bold text-plum/80 shadow-2xs">UPI</span>
                    <span class="px-2 py-1 rounded-md bg-white border border-primary-100 text-[10px] font-bold text-plum/80 shadow-2xs">Razorpay</span>
                    <span class="px-2 py-1 rounded-md bg-white border border-primary-100 text-[10px] font-bold text-plum/80 shadow-2xs">RuPay</span>
                    <span class="px-2 py-1 rounded-md bg-white border border-primary-100 text-[10px] font-bold text-plum/80 shadow-2xs">Cards</span>
                    <span class="px-2 py-1 rounded-md bg-white border border-primary-100 text-[10px] font-bold text-emerald-700 shadow-2xs">COD Available</span>
                </div>

                <div class="pt-2 text-[11px] text-plum/60 flex items-center space-x-3">
                    <span>Free Shipping above ₹999</span>
                    <span>•</span>
                    <span>Quality Verified</span>
                </div>
            </div>

        </div>

        <!-- Copyright & Bottom Line -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-plum/60 space-y-3 sm:space-y-0">
            <div>
                &copy; {{ date('Y') }} {{ $storeName }}, Amritsar. All rights reserved.
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('pages.show', 'privacy-policy') }}" class="hover:underline">Privacy</a>
                <span>•</span>
                <a href="{{ route('pages.show', 'terms-conditions') }}" class="hover:underline">Terms</a>
                <span>•</span>
                <a href="{{ route('pages.show', 'return-policy') }}" class="hover:underline">Returns</a>
                <span>•</span>
                <a href="{{ route('contact') }}" class="hover:underline">Store Locator</a>
            </div>
        </div>

    </div>
</footer>
