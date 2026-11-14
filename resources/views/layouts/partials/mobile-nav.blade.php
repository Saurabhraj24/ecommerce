@php
    $cart = app(\App\Services\CartService::class)->getCart();
    $cartCount = $cart ? $cart->items->sum('quantity') : 0;
@endphp

<!-- Mobile Bottom Navigation Bar (Visible only on mobile devices) -->
<nav class="fixed bottom-0 inset-x-0 z-30 bg-white/95 backdrop-blur-md border-t border-primary-100 shadow-lg md:hidden">
    <div class="grid grid-cols-5 h-16 max-w-md mx-auto items-center px-1">
        
        <!-- Home -->
        <a href="{{ route('home') }}" 
           class="flex flex-col items-center justify-center py-1 transition {{ request()->routeIs('home') ? 'text-primary-600 font-bold' : 'text-plum/70 hover:text-primary-600 font-medium' }}">
            <svg class="w-5 h-5 shrink-0" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="text-[10px] mt-1">Home</span>
        </a>

        <!-- Collections -->
        <a href="{{ route('products.index') }}" 
           class="flex flex-col items-center justify-center py-1 transition {{ request()->routeIs('products.*') && !request()->routeIs('uniforms.*') ? 'text-primary-600 font-bold' : 'text-plum/70 hover:text-primary-600 font-medium' }}">
            <svg class="w-5 h-5 shrink-0" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            <span class="text-[10px] mt-1">Shop</span>
        </a>

        <!-- School Uniforms -->
        <a href="{{ route('uniforms.index') }}" 
           class="flex flex-col items-center justify-center py-1 transition {{ request()->routeIs('uniforms.*') ? 'text-gold-600 font-bold' : 'text-plum/70 hover:text-gold-600 font-medium' }}">
            <div class="relative">
                <svg class="w-5 h-5 shrink-0" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path>
                </svg>
            </div>
            <span class="text-[10px] mt-1">Uniforms</span>
        </a>

        <!-- Bag / Cart -->
        <a href="{{ route('cart.index') }}" 
           @click.prevent="$dispatch('open-cart-drawer')"
           class="flex flex-col items-center justify-center py-1 relative text-plum/70 hover:text-primary-600 transition font-medium cursor-pointer">
            <div class="relative">
                <svg class="w-5 h-5 shrink-0" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span id="mobile-nav-cart-count" 
                      class="absolute -top-1.5 -right-2 bg-primary-600 text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center {{ $cartCount > 0 ? '' : 'hidden' }}">
                    {{ $cartCount }}
                </span>
            </div>
            <span class="text-[10px] mt-1">Bag</span>
        </a>

        <!-- Account -->
        @auth
            <a href="{{ route('profile.index') }}" 
               class="flex flex-col items-center justify-center py-1 transition {{ request()->routeIs('profile.*') ? 'text-primary-600 font-bold' : 'text-plum/70 hover:text-primary-600 font-medium' }}">
                <svg class="w-5 h-5 shrink-0" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="text-[10px] mt-1">Account</span>
            </a>
        @else
            <a href="{{ route('login') }}" 
               class="flex flex-col items-center justify-center py-1 transition {{ request()->routeIs('login') ? 'text-primary-600 font-bold' : 'text-plum/70 hover:text-primary-600 font-medium' }}">
                <svg class="w-5 h-5 shrink-0" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
                <span class="text-[10px] mt-1">Sign In</span>
            </a>
        @endauth

    </div>
</nav>
