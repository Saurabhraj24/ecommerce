@extends('layouts.app')

@section('title', 'Shopping Cart - Bombay Fashion Amritsar')

@section('content')
<div class="py-10 bg-festive-ivory/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-serif font-bold text-festive-plum">Your Shopping Cart</h1>
            <p class="text-xs text-festive-plum/70 mt-1">Review your selections before proceeding to secure checkout.</p>
        </div>

        @if(!$cart || $cart->items->isEmpty())
            {{-- Clean Empty State - STRICT NO FAKE DATA --}}
            <div class="glass-card rounded-3xl p-12 text-center border border-white/80 bg-white/80 max-w-lg mx-auto shadow-md">
                <div class="w-20 h-20 rounded-full bg-festive-blush mx-auto flex items-center justify-center text-festive-rose mb-5">
                    <svg class="w-10 h-10 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-serif font-bold text-festive-plum mb-2">Your Cart is Empty</h2>
                <p class="text-sm text-festive-plum/70 mb-6">
                    Looks like you haven't added any clothing pieces or school uniforms to your cart yet.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-3">
                    <a href="{{ route('products.index') }}" class="px-6 py-3 rounded-xl bg-festive-rose text-white text-xs font-bold shadow-md hover:bg-festive-rose/90 transition-all">
                        Browse Collections
                    </a>
                    <a href="{{ route('uniforms.index') }}" class="px-6 py-3 rounded-xl bg-white border border-festive-rose/30 text-festive-rose text-xs font-bold hover:bg-festive-blush transition-all">
                        School Uniforms
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- Left: Cart Items List (8 cols) --}}
                <div class="lg:col-span-8 space-y-4">
                    
                    {{-- Free shipping progress bar if configured --}}
                    @if($summary['free_shipping_threshold'] > 0)
                        @php
                            $remaining = max(0, $summary['free_shipping_threshold'] - $summary['subtotal']);
                            $percent = min(100, round(($summary['subtotal'] / $summary['free_shipping_threshold']) * 100));
                        @endphp
                        <div class="glass-card rounded-2xl p-4 border border-white/80 bg-white/80 shadow-sm">
                            <div class="flex justify-between text-xs font-bold text-festive-plum mb-1.5">
                                @if($remaining > 0)
                                    <span>Add <span class="text-festive-rose">₹{{ number_format($remaining, 2) }}</span> more for FREE Amritsar delivery!</span>
                                @else
                                    <span class="text-emerald-700 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Congratulations! You qualify for FREE Delivery!
                                    </span>
                                @endif
                                <span class="text-festive-plum/50">{{ $percent }}%</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-festive-plum/10 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-festive-rose to-festive-gold transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endif

                    {{-- Items Table Card --}}
                    <div class="glass-card rounded-3xl p-6 border border-white/80 bg-white/80 shadow-md divide-y divide-festive-plum/10">
                        @foreach($cart->items as $item)
                            @php
                                $img = $item->product?->primaryImage?->image_url ?? $item->product?->images->first()?->image_url ?? null;
                            @endphp
                            <div class="py-5 first:pt-0 last:pb-0 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                                
                                {{-- Item image & info --}}
                                <div class="flex gap-4 items-center flex-1">
                                    <a href="{{ route('products.show', $item->product->slug) }}" class="w-20 h-24 rounded-xl overflow-hidden bg-festive-blush/20 flex-shrink-0 border border-festive-plum/10">
                                        @if($img)
                                            <img src="{{ $img }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-festive-plum/40 text-[10px] text-center p-1">No Image</div>
                                        @endif
                                    </a>

                                    <div class="flex-1">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-festive-rose">
                                            {{ $item->product?->category?->name ?? 'Collection' }}
                                        </span>
                                        <h3 class="text-sm font-serif font-bold text-festive-plum hover:text-festive-rose transition-colors">
                                            <a href="{{ route('products.show', $item->product->slug) }}">
                                                {{ $item->product->name }}
                                            </a>
                                        </h3>
                                        
                                        @if($item->variant)
                                            <div class="flex items-center gap-2 text-xs text-festive-plum/70 mt-0.5">
                                                @if($item->variant->size)
                                                    <span class="font-medium">Size: {{ $item->variant->size }}</span>
                                                @endif
                                                @if($item->variant->color)
                                                    <span>• Color: {{ $item->variant->color }}</span>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="text-xs font-bold text-festive-plum mt-1">
                                            ₹{{ number_format($item->unit_price, 2) }} each
                                        </div>
                                    </div>
                                </div>

                                {{-- Quantity stepper + line total --}}
                                <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-3">
                                    <div class="flex items-center gap-3">
                                        {{-- Stepper --}}
                                        <div class="flex items-center border border-festive-plum/20 rounded-xl bg-white/80 overflow-hidden shadow-sm">
                                            <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                <input type="hidden" name="quantity" value="{{ max(1, $item->quantity - 1) }}">
                                                <button type="submit" class="px-2.5 py-1 text-festive-plum hover:bg-festive-blush text-xs font-bold">-</button>
                                            </form>
                                            
                                            <span class="w-8 text-center text-xs font-bold text-festive-plum">
                                                {{ $item->quantity }}
                                            </span>

                                            <form action="{{ route('cart.update') }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                                <button type="submit" class="px-2.5 py-1 text-festive-plum hover:bg-festive-blush text-xs font-bold">+</button>
                                            </form>
                                        </div>

                                        {{-- Delete button --}}
                                        <form action="{{ route('cart.remove') }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                                            <button 
                                                type="submit" 
                                                class="text-festive-plum/40 hover:text-red-600 transition-colors p-1"
                                                title="Remove item"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                    {{-- Line Subtotal --}}
                                    <div class="text-sm font-bold font-serif text-festive-rose">
                                        ₹{{ number_format($item->subtotal, 2) }}
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>

                    {{-- Continue Shopping Link --}}
                    <div class="pt-2">
                        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-festive-rose hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Continue Browsing Collections
                        </a>
                    </div>
                </div>

                {{-- Right: Order Summary Card (4 cols) --}}
                <div class="lg:col-span-4 space-y-4">
                    
                    {{-- Coupon Card --}}
                    <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/80 shadow-sm">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-festive-plum mb-3 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-festive-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Apply Store Coupon
                        </h3>

                        @if(!empty($summary['coupon']))
                            <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50 border border-emerald-200">
                                <div class="text-xs">
                                    <span class="font-bold font-mono text-emerald-800">{{ $summary['coupon']['code'] }}</span>
                                    <p class="text-[10px] text-emerald-600">Discount of ₹{{ number_format($summary['discount'], 2) }} applied!</p>
                                </div>
                                <form action="{{ route('cart.coupon.remove') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs text-rose-600 hover:underline font-bold">Remove</button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('cart.coupon.apply') }}" method="POST" class="flex gap-2">
                                @csrf
                                <input 
                                    type="text" 
                                    name="code" 
                                    placeholder="e.g. FESTIVE10" 
                                    required 
                                    class="w-full text-xs uppercase font-mono rounded-xl border-festive-plum/20 bg-white p-2.5 text-festive-plum focus:ring-festive-rose"
                                >
                                <button type="submit" class="px-4 py-2.5 rounded-xl bg-festive-plum text-white text-xs font-bold hover:bg-festive-plum/90 transition-all flex-shrink-0">
                                    Apply
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Summary Calculations --}}
                    <div class="glass-card rounded-3xl p-6 border border-white/80 bg-white/80 shadow-md space-y-4">
                        <h2 class="text-lg font-serif font-bold text-festive-plum pb-3 border-b border-festive-plum/10">
                            Order Summary
                        </h2>

                        <div class="space-y-2.5 text-xs text-festive-plum/80">
                            <div class="flex justify-between">
                                <span>Bag Subtotal ({{ $summary['item_count'] }} {{ Str::plural('item', $summary['item_count']) }})</span>
                                <span class="font-bold text-festive-plum">₹{{ number_format($summary['subtotal'], 2) }}</span>
                            </div>

                            @if($summary['discount'] > 0)
                                <div class="flex justify-between text-emerald-700">
                                    <span>Coupon Discount</span>
                                    <span class="font-bold">-₹{{ number_format($summary['discount'], 2) }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between">
                                <span>GST / Taxes ({{ $summary['tax_rate'] }}%)</span>
                                <span class="font-bold text-festive-plum">₹{{ number_format($summary['tax'], 2) }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span>Estimated Delivery</span>
                                @if($summary['shipping'] == 0)
                                    <span class="font-bold text-emerald-700 uppercase tracking-wider text-[11px]">Free</span>
                                @else
                                    <span class="font-bold text-festive-plum">₹{{ number_format($summary['shipping'], 2) }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Total --}}
                        <div class="pt-4 border-t border-festive-plum/15 flex justify-between items-baseline">
                            <div>
                                <span class="text-sm font-bold text-festive-plum">Total Amount</span>
                                <p class="text-[10px] text-festive-plum/50">Includes all local taxes</p>
                            </div>
                            <span class="text-2xl font-bold font-serif text-festive-rose">
                                ₹{{ number_format($summary['total'], 2) }}
                            </span>
                        </div>

                        {{-- Checkout Action Button --}}
                        <div class="pt-2">
                            <a 
                                href="{{ route('checkout.index') }}" 
                                class="w-full py-3.5 px-6 rounded-xl font-bold text-sm bg-festive-rose text-white shadow-lg shadow-festive-rose/25 hover:bg-festive-rose/90 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2"
                            >
                                <span>Proceed to Checkout</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>

                        {{-- Trust Badges --}}
                        <div class="pt-4 border-t border-festive-plum/10 text-center">
                            <p class="text-[11px] text-festive-plum/60 flex items-center justify-center gap-1">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Verified Checkout • Razorpay & Cash on Delivery
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        @endif

    </div>
</div>
@endsection
