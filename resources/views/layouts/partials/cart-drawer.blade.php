@php
    $drawerCart = app(\App\Services\CartService::class)->getCart();
    $freeShippingThreshold = (float) \App\Models\Setting::get('free_shipping_threshold', 999.0);
    $diffForFreeShipping = max(0, $freeShippingThreshold - $drawerCart->subtotal);
@endphp

<div x-data="{ 
        open: false,
        subtotal: {{ (float) $drawerCart->subtotal }},
        grandTotal: {{ (float) $drawerCart->grand_total }},
        itemCount: {{ (int) $drawerCart->item_count }},
        threshold: {{ (float) $freeShippingThreshold }},
        diff: {{ (float) $diffForFreeShipping }},
        async updateQty(itemId, newQty) {
            try {
                const res = await fetch('/cart/' + itemId, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ quantity: newQty })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    window.showToast ? window.showToast(data.message, 'error') : alert(data.message);
                }
            } catch(e) {
                console.error(e);
            }
        },
        async removeItem(itemId) {
            try {
                const res = await fetch('/cart/' + itemId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                window.location.reload();
            } catch(e) {
                console.error(e);
            }
        }
     }"
     @open-cart-drawer.window="open = true"
     @open-cart.window="open = true"
     @keydown.window.escape="open = false"
     class="relative z-50">

    <!-- Backdrop -->
    <div x-show="open"
         x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-plum/40 backdrop-blur-xs transition-opacity"></div>

    <!-- Slide-over Panel -->
    <div x-show="open"
         x-cloak
         x-transition:enter="transform transition ease-in-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 max-w-full flex pl-10 z-50">
        
        <div class="w-screen max-w-md bg-white/95 backdrop-blur-2xl shadow-2xl border-l border-white flex flex-col">
            
            <!-- Drawer Header -->
            <div class="p-5 border-b border-primary-100/60 flex items-center justify-between bg-gradient-to-r from-blush/40 to-cream-100/50">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <h2 class="font-serif font-bold text-lg text-plum">Your Shopping Bag ({{ $drawerCart->item_count }})</h2>
                </div>
                <button @click="open = false" class="p-2 rounded-full text-plum/50 hover:text-plum hover:bg-white/60 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Free Shipping Progress Meter -->
            @if ($drawerCart->items->isNotEmpty())
                <div class="px-5 py-3 bg-peach/30 border-b border-primary-100/40 text-xs">
                    @if ($diffForFreeShipping > 0)
                        <p class="text-plum/80 mb-1.5 font-medium">
                            Add <span class="font-bold text-primary-600">₹{{ number_format($diffForFreeShipping, 2) }}</span> more to get <span class="font-bold text-emerald-600">FREE delivery</span>!
                        </p>
                        <div class="w-full bg-white/80 rounded-full h-2 overflow-hidden border border-primary-100/60">
                            <div class="bg-gradient-to-r from-gold-400 to-primary-500 h-2 rounded-full transition-all duration-500"
                                 style="width: {{ min(100, round(($drawerCart->subtotal / $freeShippingThreshold) * 100)) }}%"></div>
                        </div>
                    @else
                        <p class="text-emerald-700 font-semibold flex items-center space-x-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span>Congratulations! You have unlocked FREE shipping.</span>
                        </p>
                    @endif
                </div>
            @endif

            <!-- Items List -->
            <div class="flex-1 overflow-y-auto p-5 space-y-4">
                @forelse ($drawerCart->items as $item)
                    <div class="flex items-center space-x-4 p-3 rounded-2xl glass-card border border-white/80">
                        <div class="w-16 h-18 rounded-xl bg-cream-200 overflow-hidden shrink-0 border border-white">
                            @if ($item->product->main_image_url)
                                <img src="{{ $item->product->main_image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-xs font-serif text-plum/40">BF</div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-plum truncate">{{ $item->product->name }}</h4>
                            @if ($item->variant)
                                <p class="text-[11px] text-plum/60">{{ $item->variant->label }}</p>
                            @endif
                            <p class="text-xs font-semibold text-primary-600 mt-0.5">
                                ₹{{ number_format($item->unit_price, 2) }}
                            </p>

                            <!-- Quantity Stepper -->
                            <div class="flex items-center space-x-2 mt-2">
                                <button @click="updateQty({{ $item->id }}, {{ $item->quantity - 1 }})" class="w-6 h-6 rounded-lg bg-cream-100 hover:bg-cream-200 text-plum flex items-center justify-center text-xs font-bold transition">
                                    -
                                </button>
                                <span class="text-xs font-bold w-5 text-center">{{ $item->quantity }}</span>
                                <button @click="updateQty({{ $item->id }}, {{ $item->quantity + 1 }})" class="w-6 h-6 rounded-lg bg-cream-100 hover:bg-cream-200 text-plum flex items-center justify-center text-xs font-bold transition">
                                    +
                                </button>
                            </div>
                        </div>
                        <button @click="removeItem({{ $item->id }})" class="text-plum/30 hover:text-rose-500 transition p-1" title="Remove item">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                @empty
                    <!-- Friendly Empty State (STRICT DATA RULE) -->
                    <div class="py-16 text-center space-y-3">
                        <div class="w-16 h-16 mx-auto rounded-full bg-primary-50 flex items-center justify-center text-primary-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <h3 class="font-serif font-bold text-lg text-plum">Your shopping bag is empty</h3>
                        <p class="text-xs text-plum/60 max-w-xs mx-auto">
                            Explore our festive collection of Sarees, Kurtas, Punjabi Suits, and Amritsar School Uniforms.
                        </p>
                        <a href="{{ route('products.index') }}" @click="open = false" class="inline-block mt-2 px-5 py-2.5 rounded-full soft-btn-primary text-xs font-semibold">
                            Start Shopping
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Drawer Footer with Subtotal & Checkout -->
            @if ($drawerCart->items->isNotEmpty())
                <div class="p-5 border-t border-primary-100/60 bg-white/90 space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-plum/70">Subtotal:</span>
                        <span class="font-bold text-plum text-base">₹{{ number_format($drawerCart->subtotal, 2) }}</span>
                    </div>
                    <p class="text-[11px] text-plum/50">Taxes, discounts, and delivery calculated at checkout.</p>
                    
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <a href="{{ route('cart.index') }}" class="w-full text-center py-3 rounded-xl soft-btn-outline text-xs font-semibold">
                            View Bag
                        </a>
                        <a href="{{ route('checkout.index') }}" class="w-full text-center py-3 rounded-xl soft-btn-primary text-xs font-semibold">
                            Checkout
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
