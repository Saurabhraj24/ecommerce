@extends('layouts.app')

@section('title', 'Bombay Fashion Amritsar | Premium Sarees, Suits & School Uniforms')

@section('content')

    <!-- Hero Section / Dynamic Banner Slider -->
    <section class="relative overflow-hidden pt-4 pb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($heroBanners->isNotEmpty())
                <div x-data="{
                        current: 0,
                        total: {{ $heroBanners->count() }},
                        timer: null,
                        next() {
                            this.current = (this.current + 1) % this.total;
                        },
                        prev() {
                            this.current = (this.current - 1 + this.total) % this.total;
                        },
                        goTo(index) {
                            this.current = index;
                            this.resetAutoplay();
                        },
                        resetAutoplay() {
                            if (this.timer) clearInterval(this.timer);
                            if (this.total > 1) {
                                this.timer = setInterval(() => {
                                    this.next();
                                }, 3500);
                            }
                        },
                        init() {
                            this.resetAutoplay();
                        }
                    }"
                    class="relative rounded-3xl overflow-hidden glass-card shadow-2xl border border-primary-100 min-h-[450px] sm:min-h-[540px] md:min-h-[580px] lg:min-h-[620px] flex items-center group">
                    
                    @foreach ($heroBanners as $index => $banner)
                        <div x-show="current === {{ $index }}"
                             x-cloak
                             x-transition:enter="transition ease-out duration-700"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-500"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-105"
                             class="absolute inset-0 w-full h-full">
                            
                            <!-- High-Fashion Photorealistic Background Image -->
                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover object-center">
                            
                            <!-- Soft Light Ambient Gradient Overlay (Ensures Text Legibility) -->
                            <div class="absolute inset-0 bg-gradient-to-r from-cream via-cream/90 md:via-cream/70 to-transparent flex items-center">
                                <div class="p-6 sm:p-12 md:p-16 lg:p-20 max-w-xl lg:max-w-2xl space-y-3 sm:space-y-4">
                                    <span class="inline-block px-3.5 py-1 rounded-full text-[10px] sm:text-xs font-bold uppercase tracking-wider festive-badge shadow-2xs">
                                        Bombay Fashion • Amritsar Since 1998
                                    </span>
                                    <h2 class="font-serif text-2xl sm:text-4xl md:text-5xl font-extrabold text-plum leading-snug sm:leading-tight tracking-tight">
                                        {{ $banner->title }}
                                    </h2>
                                    @if ($banner->subtitle)
                                        <p class="text-xs sm:text-sm md:text-base text-plum/85 leading-relaxed font-normal sm:font-medium max-w-lg">
                                            {{ $banner->subtitle }}
                                        </p>
                                    @endif
                                    @if ($banner->link_url)
                                        <div class="pt-2 sm:pt-4">
                                            <a href="{{ $banner->link_url }}" class="inline-flex items-center space-x-2 px-6 py-3 sm:px-8 sm:py-3.5 rounded-full soft-btn-primary font-semibold text-xs sm:text-sm shadow-lg hover:shadow-xl transition">
                                                <span>{{ $banner->button_text ?: 'Explore Collection' }}</span>
                                                <svg class="w-4 h-4 shrink-0" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Previous & Next Arrow Controls -->
                    @if ($heroBanners->count() > 1)
                        <button type="button"
                                @click="prev(); resetAutoplay();" 
                                class="absolute left-3 sm:left-6 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/90 hover:bg-white text-plum/80 hover:text-primary-600 shadow-md border border-primary-100 flex items-center justify-center transition opacity-90 group-hover:opacity-100 z-20 cursor-pointer"
                                aria-label="Previous Slide">
                            <svg class="w-5 h-5 shrink-0" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <button type="button"
                                @click="next(); resetAutoplay();" 
                                class="absolute right-3 sm:right-6 top-1/2 -translate-y-1/2 w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-white/90 hover:bg-white text-plum/80 hover:text-primary-600 shadow-md border border-primary-100 flex items-center justify-center transition opacity-90 group-hover:opacity-100 z-20 cursor-pointer"
                                aria-label="Next Slide">
                            <svg class="w-5 h-5 shrink-0" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                        </button>

                        <!-- Slider Dot Indicators -->
                        <div class="absolute bottom-5 sm:bottom-8 left-1/2 -translate-x-1/2 flex items-center space-x-2 z-20 bg-white/80 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-primary-100/70 shadow-sm">
                            @foreach ($heroBanners as $idx => $b)
                                <button type="button"
                                        @click="goTo({{ $idx }})" 
                                        class="h-2 rounded-full transition-all duration-300 cursor-pointer"
                                        :class="current === {{ $idx }} ? 'w-8 bg-primary-600' : 'w-2 bg-plum/30 hover:bg-plum/50'"></button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <!-- Default Hero Banner when no custom banners uploaded yet (STRICT DATA RULE) -->
                <div class="relative rounded-3xl overflow-hidden glass-card shadow-xl border border-primary-100 p-5 sm:p-14 lg:p-18 bg-gradient-to-r from-blush/60 via-cream-100 to-peach/60">
                    <div class="max-w-2xl space-y-3 sm:space-y-4">
                        <span class="inline-block px-3 py-1 rounded-full text-[10px] sm:text-xs font-bold uppercase tracking-wider festive-badge">
                            Mahila College Road, Amritsar
                        </span>
                        <h1 class="font-serif text-2xl sm:text-4xl lg:text-5xl font-extrabold text-plum tracking-tight leading-snug sm:leading-tight">
                            Elegance for Every <span class="text-primary-600 italic">Celebration</span> & School Days
                        </h1>
                        <p class="text-xs sm:text-base text-plum/80 leading-relaxed font-normal sm:font-medium">
                            From handcrafted bridal sarees and traditional Punjabi attire to certified Amritsar school uniforms. Browse our collections or visit our store opposite Mahila College.
                        </p>
                        <div class="pt-2 sm:pt-4 flex flex-col sm:flex-row gap-2.5 sm:gap-3">
                            <a href="{{ route('products.index') }}" class="px-5 py-3 sm:px-7 sm:py-3.5 rounded-full soft-btn-primary font-semibold text-xs sm:text-sm shadow-md hover:shadow-lg transition inline-flex items-center justify-center space-x-2">
                                <span>Shop All Collections</span>
                                <svg class="w-4 h-4 shrink-0" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                            <a href="{{ route('uniforms.index') }}" class="px-5 py-3 sm:px-7 sm:py-3.5 rounded-full soft-btn-secondary font-semibold text-xs sm:text-sm shadow-md hover:shadow-lg transition inline-flex items-center justify-center space-x-2">
                                <span>Find School Uniform</span>
                                <svg class="w-4 h-4 shrink-0" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Key Store Highlights (Light Glass Cards) -->
    <section class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                <div class="p-4 sm:p-5 rounded-2xl glass-card flex items-center space-x-3.5 border border-primary-100/60 shadow-sm">
                    <div class="w-11 h-11 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center shrink-0 border border-primary-100">
                        <svg class="w-6 h-6 shrink-0" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-serif font-bold text-sm text-plum">100% Genuine</h4>
                        <p class="text-[11px] text-plum/70 font-medium">Quality verified fabrics</p>
                    </div>
                </div>

                <div class="p-4 sm:p-5 rounded-2xl glass-card flex items-center space-x-3.5 border border-gold-200/60 shadow-sm">
                    <div class="w-11 h-11 rounded-xl bg-gold-50 text-gold-700 flex items-center justify-center shrink-0 border border-gold-200">
                        <svg class="w-6 h-6 shrink-0" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-serif font-bold text-sm text-plum">COD Available</h4>
                        <p class="text-[11px] text-plum/70 font-medium">Pay cash on delivery</p>
                    </div>
                </div>

                <div class="p-4 sm:p-5 rounded-2xl glass-card flex items-center space-x-3.5 border border-teal-200/60 shadow-sm">
                    <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center shrink-0 border border-teal-200">
                        <svg class="w-6 h-6 shrink-0" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-serif font-bold text-sm text-plum">7-Day Exchanges</h4>
                        <p class="text-[11px] text-plum/70 font-medium">Hassle-free size exchange</p>
                    </div>
                </div>

                <div class="p-4 sm:p-5 rounded-2xl glass-card flex items-center space-x-3.5 border border-primary-100/60 shadow-sm">
                    <div class="w-11 h-11 rounded-xl bg-blush text-primary-700 flex items-center justify-center shrink-0 border border-primary-200">
                        <svg class="w-6 h-6 shrink-0" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-serif font-bold text-sm text-plum">Open Daily</h4>
                        <p class="text-[11px] text-plum/70 font-medium">Till 9:30 PM in Amritsar</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Category Tiles Section (Real DB Categories) -->
    <section class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-8 space-y-2">
                <span class="text-xs uppercase font-bold tracking-widest text-primary-600">Curated Indian Fashion</span>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-plum">Shop By Category</h2>
                <div class="w-16 h-1 bg-gradient-to-r from-gold-400 to-primary-500 mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach ($categories as $cat)
                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" 
                       class="group rounded-3xl p-5 glass-card glass-card-hover border border-primary-100/60 text-center flex flex-col items-center justify-between min-h-[190px] shadow-sm">
                        
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blush via-cream to-gold-50 border border-primary-100 flex items-center justify-center text-primary-600 group-hover:scale-110 group-hover:bg-primary-50 transition duration-300 shadow-inner">
                            @if ($cat->image_url)
                                <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" class="w-full h-full object-cover rounded-2xl">
                            @else
                                @php
                                    $iconName = strtolower($cat->name);
                                @endphp
                                @if (str_contains($iconName, 'men'))
                                    <!-- Men's Kurta / Sherwani Icon -->
                                    <svg class="w-8 h-8 text-primary-600 shrink-0" width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                @elseif (str_contains($iconName, 'saree'))
                                    <!-- Saree Drape Icon -->
                                    <svg class="w-8 h-8 text-primary-600 shrink-0" width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                                @elseif (str_contains($iconName, 'uniform'))
                                    <!-- School Uniform Badge Icon -->
                                    <svg class="w-8 h-8 text-gold-600 shrink-0" width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                                @elseif (str_contains($iconName, 'child'))
                                    <!-- Children / Kidswear Icon -->
                                    <svg class="w-8 h-8 text-teal-600 shrink-0" width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @elseif (str_contains($iconName, 'winter'))
                                    <!-- Winter Wear Snowflake Icon -->
                                    <svg class="w-8 h-8 text-blue-600 shrink-0" width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v18m0-18l-3 3m3-3l3 3m-3 15l-3-3m3 3l3-3M4 12h16m-16 0l3-3m-3 3l3 3m10-6l3 3m-3 3l3-3"></path></svg>
                                @elseif (str_contains($iconName, 'summer'))
                                    <!-- Summer Wear Sun Icon -->
                                    <svg class="w-8 h-8 text-gold-500 shrink-0" width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                @else
                                    <!-- Women's Indian Wear Sparkle Dress Icon -->
                                    <svg class="w-8 h-8 text-primary-600 shrink-0" width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                @endif
                            @endif
                        </div>

                        <div class="mt-4">
                            <h3 class="font-serif font-bold text-base text-plum group-hover:text-primary-600 transition">
                                {{ $cat->name }}
                            </h3>
                            <p class="text-[11px] text-plum/70 mt-1 line-clamp-1 font-medium">
                                {{ $cat->description ?? 'View full selection' }}
                            </p>
                        </div>

                        <span class="mt-3 text-xs font-bold text-primary-600 group-hover:translate-x-1 transition inline-flex items-center space-x-1 bg-primary-50 px-3 py-1 rounded-full border border-primary-100">
                            <span>Browse</span>
                            <span class="text-sm leading-none">&rarr;</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- New Arrivals Section (Real DB Products) -->
    <section class="py-12 bg-gradient-to-b from-transparent via-blush/20 to-transparent">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-8 space-y-2 sm:space-y-0">
                <div>
                    <span class="text-xs uppercase font-bold tracking-widest text-primary-600">Fresh From Amritsar</span>
                    <h2 class="font-serif text-3xl font-bold text-plum">New Arrivals</h2>
                </div>
                <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="text-xs font-bold text-primary-600 hover:text-primary-800 transition flex items-center space-x-1">
                    <span>View all new items</span>
                    <span>&rarr;</span>
                </a>
            </div>

            @if ($newArrivals->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach ($newArrivals as $product)
                        @include('products.partials.card', ['product' => $product])
                    @endforeach
                </div>
            @else
                <!-- Clean Friendly Empty State -->
                <div class="p-12 rounded-3xl glass-card text-center space-y-3 border border-white/80">
                    <div class="w-14 h-14 mx-auto rounded-full bg-cream-200 flex items-center justify-center text-plum/40">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <h3 class="font-serif font-bold text-base text-plum">No new arrivals cataloged yet</h3>
                    <p class="text-xs text-plum/60 max-w-sm mx-auto">
                        The store manager is currently cataloging new stock. Please check back shortly or browse other categories.
                    </p>
                </div>
            @endif
        </div>
    </section>

    <!-- School Uniforms Spotlight (Key Requirement) -->
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl glass-card p-8 sm:p-12 border border-white/90 bg-gradient-to-r from-cream-100 via-blush/30 to-peach/40 shadow-xl flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="max-w-xl space-y-3">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-gold-100 text-gold-800 border border-gold-200">
                        School Uniform Destination
                    </span>
                    <h2 class="font-serif text-3xl sm:text-4xl font-extrabold text-plum">
                        Official Uniforms for Amritsar Schools
                    </h2>
                    <p class="text-xs sm:text-sm text-plum/80 leading-relaxed font-medium">
                        Proper tailored shirts, trousers, skirts, blazers, tracksuits, ties, and belts for major private and public schools in Amritsar. Select your school, choose your grade, and add to bag in seconds.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('uniforms.index') }}" class="px-7 py-3.5 rounded-full soft-btn-primary text-sm font-semibold inline-flex items-center space-x-2">
                            <span>Open School Uniform Finder</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>

                <div class="w-full md:w-auto grid grid-cols-2 gap-3 text-center text-xs font-bold text-plum/80">
                    <div class="p-4 rounded-2xl bg-white/80 border border-white shadow-sm">
                        <div class="text-2xl font-serif text-primary-600 mb-1">Boys</div>
                        <div>Shirts & Pants</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/80 border border-white shadow-sm">
                        <div class="text-2xl font-serif text-primary-600 mb-1">Girls</div>
                        <div>Skirts & Tunics</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/80 border border-white shadow-sm">
                        <div class="text-2xl font-serif text-gold-600 mb-1">Winter</div>
                        <div>Warm Blazers</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/80 border border-white shadow-sm">
                        <div class="text-2xl font-serif text-teal-600 mb-1">Sports</div>
                        <div>Tracksuits & T-Shirts</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seasonal Collections (Winter Wear / Summer Wear) -->
    <section class="py-12" x-data="{ activeSeason: 'winter' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <span class="text-xs uppercase font-bold tracking-widest text-primary-600">Season Curations</span>
                    <h2 class="font-serif text-3xl font-bold text-plum">Weather & Seasons</h2>
                </div>

                <!-- Season Toggle Buttons -->
                <div class="flex items-center p-1 rounded-full glass-card border border-white">
                    <button @click="activeSeason = 'winter'"
                            :class="activeSeason === 'winter' ? 'soft-btn-primary' : 'text-plum/70 hover:text-plum'"
                            class="px-5 py-2 rounded-full text-xs font-bold transition">
                        Winter Wear (Woolens)
                    </button>
                    <button @click="activeSeason = 'summer'"
                            :class="activeSeason === 'summer' ? 'soft-btn-primary' : 'text-plum/70 hover:text-plum'"
                            class="px-5 py-2 rounded-full text-xs font-bold transition">
                        Summer Wear (Cottons)
                    </button>
                </div>
            </div>

            <!-- Winter Items -->
            <div x-show="activeSeason === 'winter'" x-transition>
                @if ($winterWear->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                        @foreach ($winterWear as $product)
                            @include('products.partials.card', ['product' => $product])
                        @endforeach
                    </div>
                @else
                    <div class="p-8 rounded-2xl glass-card text-center text-xs text-plum/60 border border-white/80">
                        No winter collection garments currently in stock. Check back during autumn and winter seasons.
                    </div>
                @endif
            </div>

            <!-- Summer Items -->
            <div x-show="activeSeason === 'summer'" x-transition style="display: none;">
                @if ($summerWear->isNotEmpty())
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                        @foreach ($summerWear as $product)
                            @include('products.partials.card', ['product' => $product])
                        @endforeach
                    </div>
                @else
                    <div class="p-8 rounded-2xl glass-card text-center text-xs text-plum/60 border border-white/80">
                        No summer collection garments currently in stock.
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Real Customer Reviews & Ratings (STRICT DATA RULE) -->
    <section class="py-12 bg-gradient-to-b from-transparent via-peach/20 to-transparent">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-10 space-y-2">
                <span class="text-xs uppercase font-bold tracking-widest text-primary-600">Real Feedback</span>
                <h2 class="font-serif text-3xl font-bold text-plum">What Amritsar Says</h2>
                <div class="w-16 h-1 bg-gradient-to-r from-gold-400 to-primary-500 mx-auto rounded-full"></div>
            </div>

            @if ($testimonials->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($testimonials as $review)
                        <div class="p-6 rounded-3xl glass-card border border-white/90 space-y-3 flex flex-col justify-between">
                            <div class="space-y-2">
                                <div class="flex items-center space-x-1 text-gold-500">
                                    @for ($s = 1; $s <= 5; $s++)
                                        <svg class="w-4 h-4 {{ $s <= $review->rating ? 'fill-current text-gold-500' : 'text-plum/20' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                                @if ($review->title)
                                    <h4 class="font-serif font-bold text-sm text-plum">{{ $review->title }}</h4>
                                @endif
                                <p class="text-xs text-plum/80 italic leading-relaxed">
                                    "{{ $review->comment }}"
                                </p>
                            </div>
                            <div class="pt-3 border-t border-primary-100/60 flex items-center justify-between text-xs">
                                <span class="font-bold text-plum">{{ $review->user?->name ?? 'Verified Customer' }}</span>
                                <span class="text-[11px] text-plum/50">{{ $review->created_at->format('M Y') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Clean Friendly Empty State (STRICT DATA RULE) -->
                <div class="p-8 rounded-2xl glass-card text-center text-xs text-plum/60 max-w-md mx-auto border border-white/80">
                    No customer reviews published yet. Purchased customers can leave a verified review from their order history!
                </div>
            @endif
        </div>
    </section>

    <!-- Visit Store & Google Map Embed -->
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl glass-card overflow-hidden border border-white/90 shadow-xl grid grid-cols-1 lg:grid-cols-2">
                <div class="p-8 sm:p-12 space-y-5 bg-gradient-to-br from-cream-100 via-blush/30 to-peach/30 flex flex-col justify-center">
                    <div>
                        <span class="text-xs uppercase font-bold tracking-widest text-primary-600">Visit Showroom</span>
                        <h2 class="font-serif text-3xl sm:text-4xl font-bold text-plum mt-1">Our Physical Store</h2>
                    </div>

                    <div class="space-y-3 text-sm text-plum/85">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            </div>
                            <div>
                                <strong class="block text-plum">Showroom Address:</strong>
                                <span>{{ $store['address'] }}</span>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-gold-100 text-gold-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <strong class="block text-plum">Opening Hours:</strong>
                                <span>{{ $store['timings'] }}</span>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div>
                                <strong class="block text-plum">Direct Contact:</strong>
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $store['phone']) }}" class="font-semibold text-primary-600 hover:underline">
                                    {{ $store['phone'] }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="https://maps.google.com/?q=Mahila+College+Road+Amritsar+Punjab" target="_blank" rel="noopener noreferrer" class="px-6 py-3 rounded-full soft-btn-outline text-xs font-semibold inline-flex items-center space-x-2">
                            <span>Open in Google Maps</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    </div>
                </div>

                <!-- Google Maps Embed -->
                <div class="min-h-[340px] w-full h-full bg-cream-200">
                    <iframe src="{{ $store['map_embed'] }}"
                            class="w-full h-full border-0 min-h-[340px]" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Bombay Fashion Store Location in Amritsar"></iframe>
                </div>
            </div>
        </div>
    </section>

@endsection
