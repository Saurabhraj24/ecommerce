@php
    $navCategories = \App\Models\Category::active()->root()->with(['activeChildren'])->orderBy('display_order')->get();
    $cart = app(\App\Services\CartService::class)->getCart();
    $wishlistCount = Auth::check() ? Auth::user()->wishlists()->count() : 0;
    $storeTimings = \App\Models\Setting::get('store_timings', 'Open daily, closes 9:30 PM');
    $storePhone = \App\Models\Setting::get('store_phone', '+91 98765 43210');
@endphp

<!-- Top Announcement Banner (Light Warm Peach/Blush) -->
<div class="bg-gradient-to-r from-blush via-cream-100 to-peach text-plum/90 text-xs py-2 px-4 border-b border-primary-100/60 hidden sm:block">
    <div class="max-w-7xl mx-auto flex justify-between items-center font-medium">
        <div class="flex items-center space-x-4">
            <span class="flex items-center space-x-1.5 text-primary-600 font-semibold">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                <span>Mahila College Road, opposite Mahila College, Amritsar</span>
            </span>
            <span class="text-plum/40">•</span>
            <span>{{ $storeTimings }}</span>
        </div>
        <div class="flex items-center space-x-5 text-plum/80">
            <a href="{{ route('orders.track') }}" class="hover:text-primary-600 transition flex items-center space-x-1">
                <svg class="w-3.5 h-3.5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <span>Track Order</span>
            </a>
            <span class="text-plum/40">•</span>
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $storePhone) }}" class="hover:text-primary-600 transition font-semibold">
                {{ $storePhone }}
            </a>
        </div>
    </div>
</div>

<!-- Main Glassmorphic Sticky Header -->
<header x-data="{ 
            mobileMenuOpen: false, 
            searchOpen: false, 
            searchQuery: '', 
            searchResults: { products: [], categories: [] },
            isSearching: false,
            async search() {
                if (this.searchQuery.trim().length < 2) {
                    this.searchResults = { products: [], categories: [] };
                    return;
                }
                this.isSearching = true;
                try {
                    const res = await fetch(`/search/live?q=${encodeURIComponent(this.searchQuery)}`);
                    this.searchResults = await res.json();
                } catch(e) {
                    console.error(e);
                } finally {
                    this.isSearching = false;
                }
            }
        }"
        class="sticky top-0 z-40 bg-white/98 backdrop-blur-xl border-b border-primary-100/70 shadow-xs transition-all duration-300">
    <div class="max-w-[1400px] mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 sm:h-20 gap-2 sm:gap-4 lg:gap-5">
            
            <!-- Mobile Menu Toggle Button -->
            <div class="flex items-center lg:hidden shrink-0">
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="p-1.5 sm:p-2 rounded-xl text-plum/80 hover:text-primary-600 hover:bg-primary-50 focus:outline-none transition"
                        aria-label="Open mobile menu">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Brand Logo -->
            <div class="flex items-center shrink-0">
                <a href="{{ route('home') }}" class="group flex items-center space-x-2 sm:space-x-2.5 shrink-0">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-gradient-to-tr from-primary-600 to-primary-400 flex items-center justify-center text-white shadow-md shadow-primary-500/20 group-hover:scale-105 transition duration-300 shrink-0">
                        <span class="font-serif font-bold text-sm sm:text-xl tracking-wider">BF</span>
                    </div>
                    <div class="shrink-0 flex flex-col justify-center">
                        <span class="block font-serif text-base sm:text-xl lg:text-2xl font-bold tracking-tight text-plum group-hover:text-primary-600 transition leading-tight whitespace-nowrap">
                            Bombay Fashion
                        </span>
                        <span class="block text-[8px] sm:text-[10px] uppercase tracking-wider font-semibold text-gold-600 whitespace-nowrap">
                            Amritsar • Since 1998
                        </span>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation Links -->
            <nav class="hidden lg:flex items-center space-x-0.5 xl:space-x-1.5 font-medium text-xs xl:text-sm text-plum/90">
                <a href="{{ route('home') }}" class="px-2.5 xl:px-3 py-2 rounded-xl hover:text-primary-600 hover:bg-white/60 transition whitespace-nowrap {{ request()->routeIs('home') ? 'text-primary-600 font-semibold bg-white/70 shadow-sm' : '' }}">
                    Home
                </a>

                <!-- All Clothing Catalog -->
                <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative">
                    <a href="{{ route('products.index') }}" 
                       class="px-2.5 xl:px-3 py-2 rounded-xl hover:text-primary-600 hover:bg-white/60 transition flex items-center space-x-1 whitespace-nowrap {{ request()->routeIs('products.*') ? 'text-primary-600 font-semibold' : '' }}">
                        <span>Shop Collections</span>
                        <svg class="w-3.5 h-3.5 text-plum/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </a>

                    <!-- Mega Dropdown Menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="absolute left-0 mt-1 w-96 rounded-2xl p-4 glass-modal border border-white/90 shadow-2xl z-50">
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ($navCategories as $cat)
                                <div class="p-2.5 rounded-xl hover:bg-primary-50/70 transition">
                                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="font-serif font-bold text-plum hover:text-primary-600 text-sm block">
                                        {{ $cat->name }}
                                    </a>
                                    @if ($cat->activeChildren->isNotEmpty())
                                        <div class="mt-1 space-y-1">
                                            @foreach ($cat->activeChildren->take(3) as $sub)
                                                <a href="{{ route('products.index', ['subcategory' => $sub->slug]) }}" class="block text-xs text-plum/70 hover:text-primary-600">
                                                    {{ $sub->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3 pt-3 border-t border-primary-100/60 flex justify-between items-center text-xs">
                            <a href="{{ route('products.index') }}" class="text-primary-600 font-semibold hover:underline">
                                Browse all items &rarr;
                            </a>
                            <span class="text-plum/50">Festive & Daily Wear</span>
                        </div>
                    </div>
                </div>

                <!-- School Uniforms -->
                <a href="{{ route('uniforms.index') }}" class="px-2.5 xl:px-3 py-2 rounded-xl hover:text-primary-600 hover:bg-white/60 transition flex items-center space-x-1 text-primary-700 font-semibold whitespace-nowrap {{ request()->routeIs('uniforms.*') ? 'bg-primary-50 text-primary-600 shadow-sm' : '' }}">
                    <span class="w-2 h-2 rounded-full bg-gold-500 animate-pulse"></span>
                    <span>School Uniforms</span>
                </a>

                <!-- Seasonal Winter / Summer Collections -->
                <a href="{{ route('products.index', ['season' => 'winter']) }}" class="px-2.5 xl:px-3 py-2 rounded-xl hover:text-primary-600 hover:bg-white/60 transition whitespace-nowrap">
                    Winter Wear
                </a>
                <a href="{{ route('products.index', ['season' => 'summer']) }}" class="px-2.5 xl:px-3 py-2 rounded-xl hover:text-primary-600 hover:bg-white/60 transition whitespace-nowrap">
                    Summer Wear
                </a>

                <a href="{{ route('contact') }}" class="px-2.5 xl:px-3 py-2 rounded-xl hover:text-primary-600 hover:bg-white/60 transition whitespace-nowrap">
                    Our Store
                </a>
            </nav>

            <!-- Live Search Bar + Actions -->
            <div class="flex items-center space-x-1.5 sm:space-x-3 shrink-0">
                
                <!-- Search Input Desktop -->
                <div class="relative hidden md:block w-32 lg:w-36 xl:w-44 focus-within:w-48 xl:focus-within:w-56 transition-all duration-300" @click.away="searchResults = { products: [], categories: [] }">
                    <div class="relative">
                        <input type="text"
                               x-model="searchQuery"
                               @input.debounce.300ms="search()"
                               @focus="search()"
                               placeholder="Search saree, suit..."
                               class="w-full text-xs py-2 pl-8 pr-3 rounded-full soft-input placeholder:text-plum/40 focus:ring-2 focus:ring-primary-500/20"
                               aria-label="Search clothing items">
                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-plum/40">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <!-- Live Results Dropdown -->
                    <div x-show="searchResults.products.length > 0 || searchResults.categories.length > 0"
                         x-transition
                         class="absolute left-0 right-0 mt-2 rounded-2xl p-3 glass-modal border border-white/90 shadow-2xl z-50 max-h-96 overflow-y-auto">
                        <template x-if="searchResults.categories.length > 0">
                            <div class="mb-3">
                                <span class="text-[11px] uppercase tracking-wider font-bold text-plum/40 px-2 block mb-1">Categories</span>
                                <template x-for="cat in searchResults.categories" :key="cat.slug">
                                    <a :href="cat.url" class="block px-2.5 py-1.5 rounded-lg text-xs font-medium text-plum hover:bg-primary-50 hover:text-primary-600 transition" x-text="cat.name"></a>
                                </template>
                            </div>
                        </template>

                        <template x-if="searchResults.products.length > 0">
                            <div>
                                <span class="text-[11px] uppercase tracking-wider font-bold text-plum/40 px-2 block mb-1">Products</span>
                                <template x-for="prod in searchResults.products" :key="prod.id">
                                    <a :href="prod.url" class="flex items-center space-x-3 p-2 rounded-xl hover:bg-primary-50/80 transition">
                                        <div class="w-10 h-10 rounded-lg bg-cream-200 overflow-hidden shrink-0 border border-white">
                                            <template x-if="prod.image">
                                                <img :src="prod.image" :alt="prod.name" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!prod.image">
                                                <div class="w-full h-full flex items-center justify-center text-[10px] text-plum/40">BF</div>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-plum truncate" x-text="prod.name"></p>
                                            <p class="text-[11px] text-primary-600 font-semibold" x-text="prod.price"></p>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Wishlist Icon -->
                <a href="{{ route('wishlist.index') }}" class="relative p-1.5 sm:p-2.5 rounded-xl sm:rounded-2xl text-plum/70 hover:text-primary-600 hover:bg-white/60 transition shrink-0" aria-label="View Wishlist">
                    <svg class="w-5 h-5 shrink-0" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <span id="wishlist-count-badge" class="absolute -top-1 -right-1 w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-primary-500 text-white text-[9px] sm:text-[10px] font-bold flex items-center justify-center shadow-sm" style="display: {{ $wishlistCount > 0 ? 'inline-flex' : 'none' }};">
                        {{ $wishlistCount }}
                    </span>
                </a>

                <!-- Shopping Bag / Cart Drawer Button -->
                <a href="{{ route('cart.index') }}" 
                   @click.prevent="$dispatch('open-cart-drawer')" 
                   class="relative p-1.5 sm:p-2.5 rounded-xl sm:rounded-2xl text-plum/80 hover:text-primary-600 hover:bg-white/60 transition focus:outline-none shrink-0 cursor-pointer" 
                   aria-label="Open Shopping Bag">
                    <svg class="w-5 h-5 shrink-0" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span id="header-cart-badge" class="absolute -top-1 -right-1 w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-gold-500 text-white text-[9px] sm:text-[10px] font-bold flex items-center justify-center shadow-sm" style="display: {{ $cart->item_count > 0 ? 'inline-flex' : 'none' }};">
                        {{ $cart->item_count }}
                    </span>
                </a>

                <!-- User Account Dropdown -->
                <div x-data="{ userMenuOpen: false }" @click.away="userMenuOpen = false" class="relative shrink-0">
                    @auth
                        <button @click="userMenuOpen = !userMenuOpen" class="flex items-center space-x-1.5 p-1 rounded-full hover:bg-white/60 transition border border-transparent hover:border-white/80 focus:outline-none shrink-0">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-gradient-to-tr from-primary-600 to-rose-400 text-white font-bold text-xs flex items-center justify-center shadow-sm shrink-0">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="text-xs font-semibold text-plum hidden xl:inline max-w-[120px] truncate">{{ Str::before(Auth::user()->name, ' - ') }}</span>
                            <svg class="w-3.5 h-3.5 text-plum/50 hidden sm:block shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="userMenuOpen"
                             x-transition
                             class="absolute right-0 mt-2 w-56 rounded-2xl p-2 glass-modal border border-white/90 shadow-2xl z-50">
                            <div class="px-3 py-2 border-b border-primary-100/60 mb-1">
                                <p class="text-xs font-bold text-plum truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[11px] text-plum/60 truncate">{{ Auth::user()->email }}</p>
                                <span class="inline-block mt-1 text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-primary-100 text-primary-700">
                                    {{ Auth::user()->role?->display_name ?? 'Customer' }}
                                </span>
                            </div>

                            @if (Auth::user()->canAccessAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2 px-3 py-2 rounded-xl text-xs font-semibold text-primary-700 hover:bg-primary-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    <span>Store Admin Panel</span>
                                </a>
                            @endif

                            <a href="{{ route('orders.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded-xl text-xs font-medium text-plum hover:bg-primary-50 hover:text-primary-600 transition">
                                <svg class="w-4 h-4 text-plum/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                <span>My Orders & Returns</span>
                            </a>
                            <a href="{{ route('profile.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded-xl text-xs font-medium text-plum hover:bg-primary-50 hover:text-primary-600 transition">
                                <svg class="w-4 h-4 text-plum/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span>Profile Details</span>
                            </a>
                            <a href="{{ route('profile.addresses') }}" class="flex items-center space-x-2 px-3 py-2 rounded-xl text-xs font-medium text-plum hover:bg-primary-50 hover:text-primary-600 transition">
                                <svg class="w-4 h-4 text-plum/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                <span>Address Book</span>
                            </a>

                            <div class="border-t border-primary-100/60 my-1 pt-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center space-x-2 px-3 py-2 rounded-xl text-xs font-medium text-rose-600 hover:bg-rose-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        <span>Log Out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-full soft-btn-primary text-xs font-semibold items-center space-x-1 whitespace-nowrap shadow-sm shrink-0">
                            <svg class="w-3.5 h-3.5 shrink-0" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            <span class="hidden sm:inline">Sign In</span>
                        </a>
                    @endauth
                </div>

            </div>
        </div>
    </div>

    <!-- Mobile Slideout Menu -->
    <div x-show="mobileMenuOpen" 
         x-transition
         class="lg:hidden border-t border-primary-100/60 bg-white/95 backdrop-blur-xl px-4 pt-3 pb-6 space-y-3">
        
        <!-- Mobile Search Input -->
        <div class="relative">
            <input type="text"
                   x-model="searchQuery"
                   @input.debounce.300ms="search()"
                   placeholder="Search sarees, suits, uniforms..."
                   class="w-full text-xs py-2.5 pl-9 pr-4 rounded-full soft-input">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-plum/40">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <nav class="space-y-1 text-sm font-medium text-plum">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-xl hover:bg-primary-50">Home</a>
            <a href="{{ route('products.index') }}" class="block px-3 py-2 rounded-xl hover:bg-primary-50">All Collections</a>
            <a href="{{ route('uniforms.index') }}" class="block px-3 py-2 rounded-xl hover:bg-primary-50 text-primary-700 font-semibold">School Uniforms</a>
            <a href="{{ route('products.index', ['season' => 'winter']) }}" class="block px-3 py-2 rounded-xl hover:bg-primary-50">Winter Wear</a>
            <a href="{{ route('products.index', ['season' => 'summer']) }}" class="block px-3 py-2 rounded-xl hover:bg-primary-50">Summer Wear</a>
            <a href="{{ route('orders.track') }}" class="block px-3 py-2 rounded-xl hover:bg-primary-50">Track Your Order</a>
            <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-xl hover:bg-primary-50">Visit Store in Amritsar</a>
        </nav>
    </div>
</header>
