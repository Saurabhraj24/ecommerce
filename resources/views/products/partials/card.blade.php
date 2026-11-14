@props(['product'])

@php
    $primaryImage = $product->primaryImage?->image_url ?? $product->images->first()?->image_url ?? null;
    $secondaryImage = $product->images->skip(1)->first()?->image_url ?? null;
    $hasDiscount = $product->discount_price && $product->discount_price < $product->price;
    $discountPercent = $hasDiscount ? round((($product->price - $product->discount_price) / $product->price) * 100) : 0;
    $effectivePrice = $hasDiscount ? $product->discount_price : $product->price;
    $isInStock = $product->is_in_stock;
@endphp

<div class="group relative glass-card rounded-2xl overflow-hidden border border-white/60 hover:border-festive-rose/40 transition-all duration-300 hover:shadow-xl hover:-translate-y-1 flex flex-col h-full bg-white/70">
    {{-- Badges --}}
    <div class="absolute top-3 left-3 z-10 flex flex-col gap-1.5 pointer-events-none">
        @if($hasDiscount)
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-festive-rose text-white shadow-sm">
                -{{ $discountPercent }}% OFF
            </span>
        @endif
        @if($product->is_featured)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-festive-gold/90 text-festive-plum shadow-sm">
                Featured
            </span>
        @endif
        @if($product->season && $product->season !== 'all')
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-festive-lavender text-festive-plum border border-festive-plum/10">
                {{ ucfirst($product->season) }}
            </span>
        @endif
    </div>

    {{-- Wishlist Button --}}
    <div class="absolute top-3 right-3 z-10">
        @auth
            <button 
                type="button"
                @click.prevent="window.toggleWishlist({{ $product->id }}, $el)"
                class="w-9 h-9 rounded-full bg-white/80 backdrop-blur-md shadow-sm border border-white flex items-center justify-center text-festive-plum/70 hover:text-festive-rose hover:bg-white hover:scale-110 transition-all"
                title="Save to Wishlist"
                aria-label="Wishlist"
            >
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </button>
        @else
            <a 
                href="{{ route('login') }}" 
                class="w-9 h-9 rounded-full bg-white/80 backdrop-blur-md shadow-sm border border-white flex items-center justify-center text-festive-plum/70 hover:text-festive-rose hover:bg-white hover:scale-110 transition-all"
                title="Login to add to Wishlist"
                aria-label="Login for Wishlist"
            >
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
            </a>
        @endauth
    </div>

    {{-- Product Image Container --}}
    <a href="{{ route('products.show', $product->slug) }}" class="block relative aspect-3/4 overflow-hidden bg-festive-blush/30">
        @if($primaryImage)
            <img 
                src="{{ $primaryImage }}" 
                alt="{{ $product->name }}" 
                loading="lazy"
                class="w-full h-full object-cover object-top transition-transform duration-700 group-hover:scale-105 {{ $secondaryImage ? 'group-hover:opacity-0' : '' }}"
            >
            @if($secondaryImage)
                <img 
                    src="{{ $secondaryImage }}" 
                    alt="{{ $product->name }} alternative view" 
                    loading="lazy"
                    class="absolute inset-0 w-full h-full object-cover object-top opacity-0 transition-opacity duration-700 group-hover:opacity-100 group-hover:scale-105"
                >
            @endif
        @else
            <div class="w-full h-full flex flex-col items-center justify-center bg-festive-cream/80 text-festive-plum/40 p-4 text-center">
                <svg class="w-12 h-12 mb-2 stroke-current" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-xs font-serif italic text-festive-plum/60">{{ $product->name }}</span>
            </div>
        @endif

        {{-- Out of stock overlay --}}
        @if(!$isInStock)
            <div class="absolute inset-0 bg-white/70 backdrop-blur-[2px] flex items-center justify-center">
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-festive-charcoal text-white shadow">
                    Out of Stock
                </span>
            </div>
        @endif
    </a>

    {{-- Details --}}
    <div class="p-4 flex flex-col flex-1">
        {{-- Category & Rating --}}
        <div class="flex items-center justify-between text-xs text-festive-plum/60 mb-1">
            <span>{{ $product->category?->name ?? 'Collection' }}</span>
            @if($product->approved_reviews_avg_rating)
                <span class="inline-flex items-center gap-1 font-semibold text-festive-gold">
                    ★ {{ number_format($product->approved_reviews_avg_rating, 1) }}
                    @if($product->approved_reviews_count)
                        <span class="text-[10px] text-festive-plum/40">({{ $product->approved_reviews_count }})</span>
                    @endif
                </span>
            @endif
        </div>

        {{-- Product Title --}}
        <h3 class="font-serif text-base font-semibold text-festive-plum line-clamp-1 group-hover:text-festive-rose transition-colors mb-2">
            <a href="{{ route('products.show', $product->slug) }}">
                {{ $product->name }}
            </a>
        </h3>

        {{-- Fabric/Pattern Info if present --}}
        @if($product->fabric)
            <p class="text-[11px] text-festive-plum/60 mb-2 truncate">
                Fabric: <span class="font-medium text-festive-plum/80">{{ $product->fabric }}</span>
            </p>
        @endif

        {{-- Price & Action in Mt-auto --}}
        <div class="mt-auto pt-2 flex items-center justify-between border-t border-festive-plum/5">
            <div class="flex flex-col">
                <div class="flex items-baseline gap-2">
                    <span class="text-lg font-bold text-festive-rose font-serif">
                        ₹{{ number_format($effectivePrice, 2) }}
                    </span>
                    @if($hasDiscount)
                        <span class="text-xs text-festive-plum/40 line-through">
                            ₹{{ number_format($product->price, 2) }}
                        </span>
                    @endif
                </div>
            </div>

            <a 
                href="{{ route('products.show', $product->slug) }}" 
                class="px-3 py-1.5 rounded-lg text-xs font-medium text-festive-rose border border-festive-rose/30 hover:bg-festive-rose hover:text-white transition-all duration-200"
            >
                View
            </a>
        </div>
    </div>
</div>
