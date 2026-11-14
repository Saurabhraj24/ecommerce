@extends('layouts.app')

@section('title', $product->name . ' - Bombay Fashion Amritsar')
@section('meta_description', $product->meta_description ?? Str::limit(strip_tags($product->description), 150))

@section('content')
@php
    $hasDiscount = $product->discount_price && $product->discount_price < $product->price;
    $discountPercent = $hasDiscount ? round((($product->price - $product->discount_price) / $product->price) * 100) : 0;
    $basePrice = $hasDiscount ? (float)$product->discount_price : (float)$product->price;

    $variantsJson = $product->variants->map(function($v) use ($basePrice) {
        $effPrice = $basePrice + (float)($v->additional_price ?? 0);
        return [
            'id' => $v->id,
            'size' => (string)($v->size ?? ''),
            'color' => (string)($v->color ?? ''),
            'color_code' => $v->color_code,
            'additional_price' => (float)($v->additional_price ?? 0),
            'price' => $effPrice,
            'stock' => (int)($v->stock ?? 0),
            'sku' => $v->sku,
        ];
    });

    $defaultVariant = $product->variants->firstWhere('stock', '>', 0) ?? $product->variants->first();
    $initialStock = $defaultVariant ? (int)$defaultVariant->stock : (int)$product->stock;
    $initialQuantity = $initialStock > 0 ? 1 : 0;

    // Detect Category & Product Type for Deep Size Grid
    $catSlug = strtolower($product->category?->slug ?? '');
    $catName = strtolower($product->category?->name ?? '');
    $prodName = strtolower($product->name);

    $isBottom = Str::contains($prodName, ['chino', 'trouser', 'pant', 'jean', 'short', 'pyjama', 'tracksuit', 'bottom']) 
             || Str::contains($catSlug, ['trouser', 'jean', 'bottom', 'short']);
    
    $isSareeOrShawl = Str::contains($prodName, ['saree', 'shawl', 'lohi', 'poncho', 'dupatta', 'stole'])
                   || Str::contains($catSlug, ['saree', 'shawl', 'dupatta']);

    $isKids = Str::contains($prodName, ['kid', 'boy', 'girl', 'infant', 'bear', 'child'])
           || Str::contains($catSlug, ['children', 'boy', 'girl', 'infant']);

    $sizeChartType = $isBottom ? 'bottom' : ($isSareeOrShawl ? 'drape' : ($isKids ? 'kids' : 'top'));

    // Category-specific measurement charts (measurements in inches)
    if ($sizeChartType === 'bottom') {
        $chartTitle = 'Chinos & Trousers Deep Measurement Grid';
        $chartSubtitle = 'Precision Italian & comfort cuts tailored with gripper waistband';
        $chartColumns = ['Size', 'Waist', 'Inseam', 'Hip', 'Thigh', 'Length', 'Rise'];
        $chartRows = [
            ['size' => '28', 'waist' => 28, 'inseam' => 30, 'hip' => 36, 'thigh' => 21, 'length' => 39, 'rise' => 10.0, 'fit' => 'Slim / Regular'],
            ['size' => '30', 'waist' => 30, 'inseam' => 30.5, 'hip' => 38, 'thigh' => 22, 'length' => 40, 'rise' => 10.5, 'fit' => 'Regular Fit'],
            ['size' => '32', 'waist' => 32, 'inseam' => 31, 'hip' => 40, 'thigh' => 23, 'length' => 40.5, 'rise' => 11.0, 'fit' => 'Most Popular'],
            ['size' => '34', 'waist' => 34, 'inseam' => 31.5, 'hip' => 42, 'thigh' => 24, 'length' => 41, 'rise' => 11.5, 'fit' => 'Comfort Fit'],
            ['size' => '36', 'waist' => 36, 'inseam' => 32, 'hip' => 44, 'thigh' => 25, 'length' => 41.5, 'rise' => 12.0, 'fit' => 'Relaxed Fit'],
            ['size' => '38', 'waist' => 38, 'inseam' => 32, 'hip' => 46, 'thigh' => 26, 'length' => 42, 'rise' => 12.5, 'fit' => 'Classic Fit'],
            ['size' => '40', 'waist' => 40, 'inseam' => 32.5, 'hip' => 48, 'thigh' => 27, 'length' => 42.5, 'rise' => 13.0, 'fit' => 'Plus Size'],
        ];
    } elseif ($sizeChartType === 'drape') {
        $chartTitle = 'Saree & Ethnic Drapery Specifications';
        $chartSubtitle = 'Handcrafted drape dimensions verified by Bombay Fashion masters';
        $chartColumns = ['Style', 'Total Length', 'Width / Panna', 'Blouse Piece', 'Pallu Drop'];
        $chartRows = [
            ['size' => 'Standard', 'length_m' => '5.5 Meters', 'length_in' => 216, 'width_in' => 44, 'blouse' => '0.8 M (Unstitched)', 'pallu' => '1.2 Meters', 'fit' => 'Universal Drape'],
        ];
    } elseif ($sizeChartType === 'kids') {
        $chartTitle = 'Children & Youth Sizing Grid';
        $chartSubtitle = 'Age-matched comfortable growth allowances for active kids';
        $chartColumns = ['Size / Age', 'Chest', 'Waist', 'Length', 'Child Height'];
        $chartRows = [
            ['size' => '2-3 Yrs', 'chest' => 23, 'waist' => 21, 'length' => 17, 'height' => '90-98 cm', 'fit' => 'Toddler'],
            ['size' => '4-5 Yrs', 'chest' => 25, 'waist' => 22, 'length' => 19, 'height' => '99-110 cm', 'fit' => 'Little Kids'],
            ['size' => '6-7 Yrs', 'chest' => 27, 'waist' => 23.5, 'length' => 21, 'height' => '111-122 cm', 'fit' => 'Active Kids'],
            ['size' => '8-9 Yrs', 'chest' => 29, 'waist' => 25, 'length' => 23, 'height' => '123-134 cm', 'fit' => 'Junior'],
            ['size' => '10-11 Yrs', 'chest' => 31, 'waist' => 26.5, 'length' => 25, 'height' => '135-146 cm', 'fit' => 'Pre-Teen'],
        ];
    } else {
        $chartTitle = 'Shirts, Kurtas & Tops Deep Measurement Grid';
        $chartSubtitle = 'Bespoke tailoring cuts from Amritsar with comfortable ease allowance';
        $chartColumns = ['Size', 'Chest / Bust', 'Shoulder', 'Length', 'Sleeve', 'Collar'];
        $chartRows = [
            ['size' => 'S (36)', 'chest' => 38, 'shoulder' => 17.5, 'length' => 28.5, 'sleeve' => 24.5, 'collar' => 15.0, 'fit' => 'Slim / Regular'],
            ['size' => 'M (38)', 'chest' => 40, 'shoulder' => 18.0, 'length' => 29.5, 'sleeve' => 25.0, 'collar' => 15.5, 'fit' => 'Regular Fit'],
            ['size' => 'L (40)', 'chest' => 42, 'shoulder' => 18.5, 'length' => 30.0, 'sleeve' => 25.5, 'collar' => 16.0, 'fit' => 'Most Popular'],
            ['size' => 'XL (42)', 'chest' => 44, 'shoulder' => 19.0, 'length' => 30.5, 'sleeve' => 26.0, 'collar' => 16.5, 'fit' => 'Comfort Fit'],
            ['size' => 'XXL (44)', 'chest' => 46, 'shoulder' => 19.5, 'length' => 31.0, 'sleeve' => 26.5, 'collar' => 17.0, 'fit' => 'Relaxed Fit'],
            ['size' => '3XL (46)', 'chest' => 48, 'shoulder' => 20.0, 'length' => 31.5, 'sleeve' => 27.0, 'collar' => 17.5, 'fit' => 'Plus Size'],
        ];
    }
@endphp

<div 
    class="py-8 bg-festive-ivory/40"
    x-data="{
        activeImage: '{{ $product->primaryImage?->image_url ?? $product->images->first()?->image_url ?? '' }}',
        selectedVariantId: {{ $defaultVariant?->id ?? 'null' }},
        variants: {{ $variantsJson->toJson() }},
        basePrice: {{ (float)$basePrice }},
        originalPrice: {{ (float)$product->price }},
        quantity: {{ $initialQuantity }},
        unit: 'in',
        showSizeGrid: false,
        sizeGuideOpen: false,
        zoomOpen: false,
        guideTab: 'measurements',

        get currentVariant() {
            return this.variants.find(v => v.id == this.selectedVariantId) || null;
        },
        get currentPrice() {
            if (this.currentVariant && this.currentVariant.price) {
                return parseFloat(this.currentVariant.price);
            }
            return this.basePrice;
        },
        get currentStock() {
            if (this.currentVariant) {
                return parseInt(this.currentVariant.stock ?? 0);
            }
            return {{ (int) $product->stock }};
        },
        get isAvailable() {
            return this.currentStock > 0;
        },
        selectVariant(id) {
            this.selectedVariantId = id;
            if (this.currentStock > 0) {
                this.quantity = Math.max(1, Math.min(this.quantity || 1, this.currentStock));
            } else {
                this.quantity = 0;
            }
        },
        selectVariantBySize(sizeLabel) {
            if (!sizeLabel) return;
            const clean = sizeLabel.toString().trim().toLowerCase();
            const match = this.variants.find(v => {
                if (!v.size) return false;
                const vSize = v.size.toString().trim().toLowerCase();
                return vSize === clean || clean.startsWith(vSize) || vSize.startsWith(clean);
            });
            if (match) {
                this.selectVariant(match.id);
            }
        },
        formatVal(valInInches) {
            if (valInInches === null || valInInches === undefined || valInInches === '') return '-';
            if (this.unit === 'cm') {
                return (parseFloat(valInInches) * 2.54).toFixed(1) + ' cm';
            }
            return valInInches + '″';
        }
    }"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Breadcrumb --}}
        <nav class="flex text-xs text-festive-plum/60 mb-4 sm:mb-6 overflow-hidden" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-1 text-[11px] sm:text-xs">
                <li><a href="{{ route('home') }}" class="hover:text-festive-rose transition-colors">Home</a></li>
                <li><span class="text-festive-plum/30">/</span></li>
                <li><a href="{{ route('products.index') }}" class="hover:text-festive-rose transition-colors">Collections</a></li>
                @if($product->category)
                    <li><span class="text-festive-plum/30">/</span></li>
                    <li><a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-festive-rose transition-colors">{{ $product->category->name }}</a></li>
                @endif
                <li><span class="text-festive-plum/30">/</span></li>
                <li class="text-festive-rose font-medium truncate max-w-[140px] xs:max-w-[200px] sm:max-w-xs">{{ $product->name }}</li>
            </ol>
        </nav>

        {{-- Product Main Card --}}
        <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 border border-white/80 bg-white/80 shadow-lg mb-8 sm:mb-12 w-full max-w-full overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12">
                
                {{-- Left: Image Gallery (5 cols) --}}
                <div class="lg:col-span-5 flex flex-col gap-3 sm:gap-4 w-full min-w-0">
                    {{-- Main Image with Zoom click --}}
                    <div class="relative w-full aspect-3/4 max-h-[460px] sm:max-h-[560px] rounded-2xl overflow-hidden bg-festive-blush/20 border border-festive-plum/10 group cursor-zoom-in" @click="zoomOpen = true">
                        <template x-if="activeImage">
                            <img 
                                :src="activeImage" 
                                alt="{{ $product->name }}" 
                                class="w-full h-full object-cover object-top transition-transform duration-500 group-hover:scale-105"
                            >
                        </template>
                        <template x-if="!activeImage">
                            <div class="w-full h-full flex flex-col items-center justify-center text-festive-plum/40 p-4">
                                <svg class="w-16 h-16 mb-2 stroke-current" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-xs font-serif italic text-festive-plum/60">{{ $product->name }}</span>
                            </div>
                        </template>

                        {{-- Floating Badge --}}
                        <div class="absolute top-3 left-3 flex flex-col gap-1.5 pointer-events-none">
                            @if($hasDiscount)
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-festive-rose text-white shadow-sm">
                                    -{{ $discountPercent }}% OFF
                                </span>
                            @endif
                            @if($product->season && $product->season !== 'all')
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-festive-lavender text-festive-plum border border-festive-plum/10">
                                    {{ ucfirst($product->season) }}
                                </span>
                            @endif
                        </div>

                        {{-- Zoom hint --}}
                        <div class="absolute bottom-3 right-3 bg-white/85 backdrop-blur-md px-2.5 py-1 rounded-lg text-[10px] text-festive-plum/70 flex items-center gap-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                            </svg>
                            Click to expand
                        </div>
                    </div>

                    {{-- Thumbnails --}}
                    @if($product->images->count() > 1)
                        <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-thin">
                            @foreach($product->images as $img)
                                <button 
                                    type="button"
                                    @click="activeImage = '{{ $img->image_url }}'"
                                    :class="activeImage === '{{ $img->image_url }}' ? 'border-festive-rose ring-2 ring-festive-rose/30' : 'border-festive-plum/10 hover:border-festive-rose/50'"
                                    class="w-16 h-20 rounded-xl overflow-hidden border-2 bg-white flex-shrink-0 transition-all"
                                >
                                    <img src="{{ $img->image_url }}" alt="Thumbnail" class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Right: Product Details (7 cols) --}}
                <div class="lg:col-span-7 flex flex-col min-w-0">
                    
                    {{-- Category & SKU --}}
                    <div class="flex flex-wrap items-center justify-between text-xs text-festive-plum/60 mb-2 gap-2">
                        <span class="font-bold uppercase tracking-wider text-festive-rose">
                            {{ $product->category?->name ?? 'Bombay Fashion Exclusive' }}
                        </span>
                        <span class="font-mono text-[11px] bg-festive-blush/40 px-2 py-0.5 rounded-md border border-festive-plum/5">
                            SKU: <span x-text="currentVariant ? currentVariant.sku : '{{ $product->sku ?? 'BF-'.str_pad($product->id, 5, '0', STR_PAD_LEFT) }}'"></span>
                        </span>
                    </div>

                    {{-- Title --}}
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-serif font-bold text-festive-plum mb-3 leading-snug">
                        {{ $product->name }}
                    </h1>

                    {{-- Ratings & Reviews count --}}
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-4">
                        @if($product->approved_reviews_avg_rating)
                            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-festive-gold/15 text-festive-plum text-xs font-bold shrink-0">
                                <span class="text-festive-gold">★</span>
                                <span>{{ number_format($product->approved_reviews_avg_rating, 1) }} / 5.0</span>
                            </div>
                            <a href="#reviews" class="text-xs text-festive-plum/70 hover:text-festive-rose underline shrink-0">
                                {{ $product->approved_reviews_count }} {{ Str::plural('verified review', $product->approved_reviews_count) }}
                            </a>
                        @else
                            <span class="text-xs text-festive-plum/50 italic shrink-0">No reviews yet</span>
                        @endif
                        <span class="text-festive-plum/20 hidden xs:inline">•</span>
                        <span class="text-[11px] sm:text-xs text-festive-teal font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            100% Authentic Amritsar Store Guarantee
                        </span>
                    </div>

                    {{-- Price Display --}}
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-festive-blush/30 border border-festive-plum/5 mb-6">
                        <div class="flex flex-wrap items-baseline gap-2 sm:gap-3">
                            <span class="text-2xl sm:text-3xl font-bold font-serif text-festive-rose">
                                ₹<span x-text="Number(currentPrice).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                            </span>
                            @if($hasDiscount)
                                <span class="text-sm sm:text-base text-festive-plum/40 line-through">
                                    ₹{{ number_format($product->price, 2) }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[11px] sm:text-xs font-bold bg-festive-rose text-white shrink-0">
                                    Save {{ $discountPercent }}%
                                </span>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center gap-2 mt-2 pt-2 border-t border-festive-plum/5 text-[11px] sm:text-xs text-festive-plum/60">
                            <span class="flex items-center gap-1 text-emerald-700 font-medium">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Inclusive of all taxes
                            </span>
                            <span class="text-festive-plum/20">•</span>
                            <span>Free store pickup in Amritsar</span>
                        </div>
                    </div>

                    {{-- Variants Selection Form --}}
                    <form action="{{ route('cart.add') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="product_variant_id" :value="selectedVariantId">
                        <input type="hidden" name="variant_id" :value="selectedVariantId">

                        {{-- If Variants exist --}}
                        @if($product->variants->isNotEmpty())
                            <div class="space-y-3">
                                {{-- Size Selection Bar with Interactive Grid Toggle & Unit Switch --}}
                                <div class="flex flex-wrap items-center justify-between gap-2.5 p-2.5 sm:p-3 rounded-2xl bg-festive-blush/40 border border-festive-plum/10">
                                    <div class="flex items-center gap-2">
                                        <span class="p-1.5 rounded-lg bg-festive-rose/10 text-festive-rose shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        </span>
                                        <div>
                                            <div class="text-xs font-bold text-festive-plum flex items-center gap-1.5">
                                                <span>Choose Size & Fit</span>
                                                <template x-if="currentVariant && currentVariant.size">
                                                    <span class="text-[11px] font-bold text-festive-rose bg-white px-2 py-0.5 rounded-md border border-festive-rose/20 shadow-xs" x-text="'Selected: ' + currentVariant.size"></span>
                                                </template>
                                            </div>
                                            <p class="text-[10px] text-festive-plum/60">Bespoke cuts with Bombay comfort seam</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 ml-auto">
                                        {{-- Unit Switcher (Inches vs Centimeters) --}}
                                        <div class="inline-flex rounded-lg p-0.5 bg-white border border-festive-plum/15 text-[10px] font-bold shadow-xs">
                                            <button 
                                                type="button" 
                                                @click="unit = 'in'" 
                                                :class="unit === 'in' ? 'bg-festive-rose text-white shadow-xs' : 'text-festive-plum/70 hover:text-festive-rose'"
                                                class="px-2 py-0.5 rounded transition-all cursor-pointer"
                                                title="View in Inches"
                                            >
                                                IN (″)
                                            </button>
                                            <button 
                                                type="button" 
                                                @click="unit = 'cm'" 
                                                :class="unit === 'cm' ? 'bg-festive-rose text-white shadow-xs' : 'text-festive-plum/70 hover:text-festive-rose'"
                                                class="px-2 py-0.5 rounded transition-all cursor-pointer"
                                                title="View in Centimeters"
                                            >
                                                CM
                                            </button>
                                        </div>

                                        {{-- Size Grid Animated Toggle Switch --}}
                                        <button 
                                            type="button" 
                                            @click="showSizeGrid = !showSizeGrid" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer"
                                            :class="showSizeGrid ? 'bg-festive-rose text-white ring-2 ring-festive-rose/30 shadow-md' : 'bg-white text-festive-plum border border-festive-rose/30 hover:border-festive-rose hover:bg-festive-rose/5'"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                            </svg>
                                            <span x-text="showSizeGrid ? 'Hide Grid ▲' : 'Measurements Grid ▼'"></span>
                                        </button>

                                        {{-- Detailed Modal Link --}}
                                        <button 
                                            type="button" 
                                            @click="sizeGuideOpen = true" 
                                            class="text-xs text-festive-rose hover:underline font-semibold flex items-center gap-1 p-1"
                                            title="View body diagram and guide"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span class="hidden xs:inline">Guide</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Deep Measurements Grid (In-Place Interactive Accordion) --}}
                                <div 
                                    x-show="showSizeGrid" 
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-2"
                                    class="p-3 sm:p-4 rounded-2xl bg-gradient-to-br from-white to-festive-blush/20 border-2 border-festive-rose/20 shadow-md"
                                >
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                        <div>
                                            <h4 class="text-xs sm:text-sm font-bold text-festive-plum font-serif flex items-center gap-1.5">
                                                <span>{{ $chartTitle }}</span>
                                                <span class="text-[10px] font-sans font-semibold text-festive-teal bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                                    Showing in <span x-text="unit === 'cm' ? 'Centimeters (cm)' : 'Inches (″)'"></span>
                                                </span>
                                            </h4>
                                            <p class="text-[11px] text-festive-plum/60">{{ $chartSubtitle }}</p>
                                        </div>
                                        <div class="text-[11px] text-festive-plum/70 italic">
                                            💡 Click any row below to instantly select that size!
                                        </div>
                                    </div>

                                    <div class="overflow-x-auto rounded-xl border border-festive-plum/10 bg-white/90 shadow-inner">
                                        <table class="w-full text-left text-xs border-collapse">
                                            <thead>
                                                <tr class="bg-festive-blush/50 text-festive-plum font-bold border-b border-festive-plum/15">
                                                    @foreach($chartColumns as $col)
                                                        <th class="py-2.5 px-3 text-center first:text-left">{{ $col }}</th>
                                                    @endforeach
                                                    <th class="py-2.5 px-3 text-center">Fit Style</th>
                                                    <th class="py-2.5 px-3 text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-festive-plum/10 text-festive-plum/80">
                                                @foreach($chartRows as $row)
                                                    @php
                                                        $rowSize = $row['size'] ?? '';
                                                    @endphp
                                                    <tr 
                                                        @click="selectVariantBySize('{{ $rowSize }}')"
                                                        class="cursor-pointer transition-colors hover:bg-festive-blush/30"
                                                        :class="{
                                                            'bg-festive-rose/10 font-bold border-l-4 border-l-festive-rose text-festive-rose': currentVariant && (currentVariant.size === '{{ $rowSize }}' || '{{ $rowSize }}'.includes(currentVariant.size))
                                                        }"
                                                    >
                                                        <td class="py-2.5 px-3 text-left font-bold text-festive-plum whitespace-nowrap">
                                                            <div class="flex items-center gap-1.5">
                                                                <span>{{ $rowSize }}</span>
                                                                <template x-if="currentVariant && (currentVariant.size === '{{ $rowSize }}' || '{{ $rowSize }}'.includes(currentVariant.size))">
                                                                    <span class="w-2 h-2 rounded-full bg-festive-rose"></span>
                                                                </template>
                                                            </div>
                                                        </td>

                                                        @if($sizeChartType === 'bottom')
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['waist'] }})"></td>
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['inseam'] }})"></td>
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['hip'] }})"></td>
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['thigh'] }})"></td>
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['length'] }})"></td>
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['rise'] }})"></td>
                                                        @elseif($sizeChartType === 'drape')
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap">{{ $row['length_m'] }} (<span x-text="formatVal({{ $row['length_in'] }})"></span>)</td>
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['width_in'] }})"></td>
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap">{{ $row['blouse'] }}</td>
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap">{{ $row['pallu'] }}</td>
                                                        @elseif($sizeChartType === 'kids')
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['chest'] }})"></td>
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['waist'] }})"></td>
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['length'] }})"></td>
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap">{{ $row['height'] }}</td>
                                                        @else
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['chest'] }})"></td>
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['shoulder'] }})"></td>
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['length'] }})"></td>
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['sleeve'] }})"></td>
                                                            <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['collar'] }})"></td>
                                                        @endif

                                                        <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-festive-lavender/40 text-festive-plum">
                                                                {{ $row['fit'] ?? 'Standard Fit' }}
                                                            </span>
                                                        </td>

                                                        <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                                            <button 
                                                                type="button" 
                                                                @click.stop="selectVariantBySize('{{ $rowSize }}')"
                                                                class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all"
                                                                :class="currentVariant && (currentVariant.size === '{{ $rowSize }}' || '{{ $rowSize }}'.includes(currentVariant.size)) ? 'bg-festive-rose text-white shadow-xs' : 'bg-festive-blush text-festive-plum hover:bg-festive-rose hover:text-white'"
                                                            >
                                                                <span x-text="currentVariant && (currentVariant.size === '{{ $rowSize }}' || '{{ $rowSize }}'.includes(currentVariant.size)) ? '✓ Selected' : 'Select Size'"></span>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="flex flex-wrap items-center justify-between gap-2 mt-3 pt-2 border-t border-festive-plum/10 text-[11px] text-festive-plum/70">
                                        <div class="flex items-center gap-1.5 text-festive-rose font-medium">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Boutique Guarantee: Free custom alteration at Mahila College Road shop!</span>
                                        </div>
                                        <button 
                                            type="button" 
                                            @click="sizeGuideOpen = true" 
                                            class="text-festive-plum hover:text-festive-rose underline font-semibold cursor-pointer"
                                        >
                                            View How to Measure Body &rarr;
                                        </button>
                                    </div>
                                </div>

                                {{-- Variant Choice Buttons / Cards --}}
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                    <template x-for="v in variants" :key="v.id">
                                        <label 
                                            class="cursor-pointer border-2 rounded-xl p-3 flex flex-col transition-all relative"
                                            :class="{
                                                'border-festive-rose bg-festive-rose/5 ring-1 ring-festive-rose shadow-sm': selectedVariantId === v.id,
                                                'border-festive-plum/15 hover:border-festive-rose/50 bg-white/80': selectedVariantId !== v.id && v.stock > 0,
                                                'opacity-50 border-dashed border-festive-plum/20 bg-gray-50/50 cursor-not-allowed': v.stock <= 0
                                            }"
                                        >
                                            <input 
                                                type="radio" 
                                                name="variant_choice" 
                                                :value="v.id" 
                                                @change="selectVariant(v.id)"
                                                :checked="selectedVariantId === v.id"
                                                class="sr-only"
                                            >
                                            <div class="flex items-center justify-between text-xs font-bold text-festive-plum">
                                                <span class="flex items-center gap-1.5">
                                                    <span x-text="v.size ? ('Size: ' + v.size) : (v.color ?? 'Option')"></span>
                                                    <template x-if="selectedVariantId === v.id">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-festive-rose"></span>
                                                    </template>
                                                </span>
                                                <template x-if="v.stock > 5">
                                                    <span class="text-[10px] text-emerald-700 font-medium bg-emerald-50 px-1.5 py-0.5 rounded" x-text="v.stock + ' in stock'"></span>
                                                </template>
                                                <template x-if="v.stock > 0 && v.stock <= 5">
                                                    <span class="text-[10px] text-amber-700 font-bold bg-amber-50 px-1.5 py-0.5 rounded" x-text="'Only ' + v.stock + ' left'"></span>
                                                </template>
                                                <template x-if="v.stock <= 0">
                                                    <span class="text-[10px] text-rose-600 font-bold bg-rose-50 px-1.5 py-0.5 rounded">Sold Out</span>
                                                </template>
                                            </div>
                                            <div class="flex items-center justify-between mt-1 text-[11px] text-festive-plum/60">
                                                <template x-if="v.color">
                                                    <span class="flex items-center gap-1.5">
                                                        <template x-if="v.color_code">
                                                            <span class="w-2.5 h-2.5 rounded-full border border-black/10 inline-block shrink-0" :style="'background-color: ' + v.color_code"></span>
                                                        </template>
                                                        <span x-text="v.color"></span>
                                                    </span>
                                                </template>
                                                <template x-if="v.additional_price > 0">
                                                    <span class="text-[10px] text-festive-gold font-bold ml-auto" x-text="'+₹' + v.additional_price"></span>
                                                </template>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        @endif

                        {{-- Stock status & Quantity Selector --}}
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 pt-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-medium text-festive-plum/70">Quantity:</span>
                                <div class="flex items-center border border-festive-plum/20 rounded-xl bg-white/70 overflow-hidden shadow-sm">
                                    <button 
                                        type="button" 
                                        @click="if (isAvailable) quantity = Math.max(1, quantity - 1)" 
                                        :disabled="!isAvailable || quantity <= 1"
                                        class="px-3 py-1.5 text-festive-plum hover:bg-festive-blush text-sm font-bold transition-colors disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
                                    >-</button>
                                    <input 
                                        type="number" 
                                        name="quantity" 
                                        x-model.number="quantity" 
                                        :min="isAvailable ? 1 : 0" 
                                        :max="currentStock"
                                        :disabled="!isAvailable"
                                        class="w-12 text-center text-xs font-bold text-festive-plum border-0 focus:ring-0 p-1 bg-transparent disabled:opacity-50"
                                    >
                                    <button 
                                        type="button" 
                                        @click="if (isAvailable) quantity = Math.min(currentStock, quantity + 1)" 
                                        :disabled="!isAvailable || quantity >= currentStock"
                                        class="px-3 py-1.5 text-festive-plum hover:bg-festive-blush text-sm font-bold transition-colors disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
                                    >+</button>
                                </div>
                            </div>

                            {{-- Stock Indicator Pill --}}
                            <div>
                                <template x-if="isAvailable">
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-xs">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span x-text="currentStock <= 5 ? ('Hurry, only ' + currentStock + ' left in stock!') : ('In Stock (' + currentStock + ' units ready at Amritsar store)')"></span>
                                    </span>
                                </template>
                                <template x-if="!isAvailable">
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                        <span>Currently Out of Stock for this size</span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                            <button 
                                type="submit" 
                                :disabled="!isAvailable"
                                class="w-full py-3.5 px-6 rounded-xl font-bold text-sm bg-festive-rose text-white shadow-lg shadow-festive-rose/25 hover:bg-festive-rose/90 hover:scale-[1.01] active:scale-[0.99] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                <span x-text="isAvailable ? 'Add to Cart' : 'Currently Out of Stock'"></span>
                            </button>

                            <button 
                                type="button"
                                @click="window.toggleWishlist({{ $product->id }}, $el)"
                                class="w-full py-3.5 px-6 rounded-xl font-semibold text-sm border-2 border-festive-rose text-festive-rose hover:bg-festive-rose/5 active:scale-[0.99] transition-all flex items-center justify-center gap-2 bg-white/50 cursor-pointer"
                            >
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                                <span>Save to Wishlist</span>
                            </button>
                        </div>
                    </form>

                    {{-- Value Props / Local highlights --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-5 mt-5 border-t border-festive-plum/10 text-xs text-festive-plum/80">
                        <div class="flex items-center gap-2">
                            <span class="p-2 rounded-lg bg-festive-blush text-festive-rose shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                            </span>
                            <span>Direct pickup or delivery from Mahila College Road shop</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="p-2 rounded-lg bg-festive-lavender text-festive-plum shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            <span>Cash on Delivery & Razorpay UPI accepted</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Details Tabs: Description, Fabric Specs, Uniform details --}}
        <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 border border-white/80 bg-white/80 shadow-md mb-8 sm:mb-12">
            <h2 class="text-lg sm:text-xl font-serif font-bold text-festive-plum mb-4 pb-3 border-b border-festive-plum/10">
                Product Details & Specifications
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm text-festive-plum/80 leading-relaxed">
                <div>
                    <h3 class="font-serif font-semibold text-festive-plum text-base mb-2">Description</h3>
                    <div class="prose prose-sm max-w-none text-festive-plum/80">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="font-serif font-semibold text-festive-plum text-base mb-2">Material & Care</h3>
                    <div class="bg-festive-ivory/80 rounded-2xl p-4 border border-festive-plum/10 space-y-2.5 text-xs">
                        @if($product->fabric)
                            <div class="flex justify-between py-1 border-b border-festive-plum/5">
                                <span class="font-medium text-festive-plum/60">Fabric:</span>
                                <span class="font-bold text-festive-plum">{{ $product->fabric }}</span>
                            </div>
                        @endif
                        @if($product->pattern)
                            <div class="flex justify-between py-1 border-b border-festive-plum/5">
                                <span class="font-medium text-festive-plum/60">Pattern / Weave:</span>
                                <span class="font-bold text-festive-plum">{{ $product->pattern }}</span>
                            </div>
                        @endif
                        @if($product->season)
                            <div class="flex justify-between py-1 border-b border-festive-plum/5">
                                <span class="font-medium text-festive-plum/60">Season Wear:</span>
                                <span class="font-bold text-festive-plum">{{ ucfirst($product->season) }}</span>
                            </div>
                        @endif
                        @if($product->is_school_uniform && $product->uniform)
                            <div class="flex justify-between py-1 border-b border-festive-plum/5">
                                <span class="font-medium text-festive-plum/60">School:</span>
                                <span class="font-bold text-festive-rose">{{ $product->uniform->school_name }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-festive-plum/5">
                                <span class="font-medium text-festive-plum/60">Class / Grade:</span>
                                <span class="font-bold text-festive-plum">{{ $product->uniform->class_grade }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="font-medium text-festive-plum/60">Uniform Type:</span>
                                <span class="font-bold text-festive-plum">{{ ucfirst($product->uniform->gender) }} Uniform</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Reviews Section (Verified Delivered Purchases with Photo Uploads) --}}
        <div id="reviews" 
             x-data="{ 
                 editOpen: false, 
                 selectedRating: {{ $userReview ? $userReview->rating : 5 }},
                 fitFeedback: '{{ $userReview ? ($userReview->fit_feedback ?? 'true_to_size') : 'true_to_size' }}',
                 qualityRating: '{{ $userReview ? ($userReview->quality_rating ?? 'exceptional') : 'exceptional' }}',
                 imagePreviews: [],
                 previewImages(event) {
                     this.imagePreviews = [];
                     const files = event.target.files;
                     if (!files) return;
                     const count = Math.min(files.length, 3);
                     for (let i = 0; i < count; i++) {
                         const reader = new FileReader();
                         reader.onload = (e) => {
                             this.imagePreviews.push(e.target.result);
                         };
                         reader.readAsDataURL(files[i]);
                     }
                 },
                 photoModalOpen: false,
                 modalPhotoUrl: ''
             }"
             class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 border border-white/80 bg-white/80 shadow-md mb-8 sm:mb-12">
            
            {{-- Header & Overall Score --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-5 border-b border-festive-plum/10 gap-4 mb-6">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-serif font-bold text-festive-plum">
                            Customer Reviews & Ratings
                        </h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            ✓ 100% Verified Purchases
                        </span>
                    </div>
                    <p class="text-xs text-festive-plum/60 mt-1">Real reviews and photo uploads from patrons who received their order.</p>
                </div>

                @if($product->approvedReviews->isNotEmpty())
                    <div class="flex items-center gap-3 bg-festive-blush/40 px-4 py-2.5 rounded-2xl border border-festive-plum/5">
                        <span class="text-3xl font-bold font-serif text-festive-gold">
                            {{ number_format($product->approved_reviews_avg_rating ?? $product->approvedReviews->avg('rating'), 1) }}
                        </span>
                        <div class="flex flex-col">
                            <div class="flex text-festive-gold text-sm">
                                @php $avgRating = round($product->approved_reviews_avg_rating ?? $product->approvedReviews->avg('rating')); @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <span>{{ $i <= $avgRating ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                            <span class="text-[11px] text-festive-plum/60 font-medium">Based on {{ $product->approvedReviews->count() }} verified {{ Str::plural('buyer', $product->approvedReviews->count()) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- SECTION 1: CUSTOMER ELIGIBILITY NOTICES & FORMS --}}

            {{-- 1A: Customer has received product & already submitted a review (One review per product) --}}
            @if($userReview)
                <div class="mb-8 p-5 rounded-2xl bg-gradient-to-r from-festive-blush/40 to-festive-lavender/30 border border-festive-rose/20">
                    <div class="flex flex-wrap items-center justify-between gap-2 pb-3 mb-3 border-b border-festive-plum/10">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $userReview->is_approved ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse' }}"></span>
                            <h3 class="font-serif font-bold text-festive-plum text-sm">Your Verified Product Review</h3>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $userReview->is_approved ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $userReview->is_approved ? '✓ Published & Live' : '⏳ In Moderation' }}
                            </span>
                        </div>
                        <button 
                            type="button" 
                            @click="editOpen = !editOpen"
                            class="text-xs font-bold text-festive-rose hover:text-festive-rose/80 underline flex items-center gap-1"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span x-text="editOpen ? 'Close Form' : 'Update Review / Photos'">Update Review / Photos</span>
                        </button>
                    </div>

                    {{-- Review Preview Card --}}
                    <div x-show="!editOpen" class="space-y-2">
                        <div class="flex items-center gap-3">
                            <div class="flex text-festive-gold text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <span>{{ $i <= $userReview->rating ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                            @if($userReview->title)
                                <span class="font-bold text-xs text-festive-plum">{{ $userReview->title }}</span>
                            @endif
                        </div>

                        {{-- Details badges --}}
                        <div class="flex flex-wrap items-center gap-2 text-[11px]">
                            @if($userReview->fit_feedback)
                                <span class="px-2 py-0.5 rounded-md bg-white/80 text-festive-plum/80 border border-festive-plum/10 font-medium">
                                    Fit: <strong>{{ ucwords(str_replace('_', ' ', $userReview->fit_feedback)) }}</strong>
                                </span>
                            @endif
                            @if($userReview->quality_rating)
                                <span class="px-2 py-0.5 rounded-md bg-white/80 text-festive-plum/80 border border-festive-plum/10 font-medium">
                                    Quality: <strong>{{ ucfirst($userReview->quality_rating) }}</strong>
                                </span>
                            @endif
                        </div>

                        <p class="text-xs text-festive-plum/80 leading-relaxed pt-1">{{ $userReview->comment }}</p>

                        {{-- Photos preview --}}
                        @if(!empty($userReview->images))
                            <div class="flex flex-wrap gap-2 pt-2">
                                @foreach($userReview->images as $img)
                                    <div 
                                        @click="modalPhotoUrl = '{{ $img['image_url'] }}'; photoModalOpen = true"
                                        class="w-16 h-16 rounded-xl overflow-hidden border-2 border-white shadow-sm cursor-pointer hover:scale-105 transition-transform"
                                    >
                                        <img src="{{ $img['image_url'] }}" alt="Review photo" class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Edit Form (collapsible) --}}
                    <div x-show="editOpen" x-cloak class="mt-4 pt-4 border-t border-festive-plum/10">
                        <form action="{{ route('reviews.store', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            
                            {{-- Star Rating Picker --}}
                            <div>
                                <label class="block text-xs font-bold text-festive-plum mb-1.5">Rating (1 to 5 Stars)</label>
                                <div class="flex items-center gap-2">
                                    <input type="hidden" name="rating" :value="selectedRating">
                                    <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                        <button 
                                            type="button" 
                                            @click="selectedRating = star"
                                            class="text-2xl transition-transform hover:scale-125 focus:outline-none"
                                            :class="star <= selectedRating ? 'text-festive-gold' : 'text-festive-plum/20'"
                                        >★</button>
                                    </template>
                                    <span class="text-xs font-bold text-festive-plum ml-2" x-text="selectedRating + ' / 5 Stars'"></span>
                                </div>
                            </div>

                            {{-- Title --}}
                            <div>
                                <label class="block text-xs font-bold text-festive-plum mb-1">Review Headline</label>
                                <input type="text" name="title" value="{{ old('title', $userReview->title) }}" placeholder="e.g. Gorgeous festive wear, loved the fabric!" class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-2.5 text-festive-plum focus:ring-festive-rose">
                            </div>

                            {{-- Product Details: Fit & Quality Feedback --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-festive-plum mb-1.5">Fit & Sizing Feedback</label>
                                    <div class="grid grid-cols-3 gap-1.5 text-center">
                                        <label class="cursor-pointer border rounded-xl py-2 px-1 text-[11px] font-semibold transition-all"
                                               :class="fitFeedback === 'runs_small' ? 'border-festive-rose bg-festive-rose/10 text-festive-rose' : 'border-festive-plum/20 bg-white text-festive-plum/70'">
                                            <input type="radio" name="fit_feedback" value="runs_small" x-model="fitFeedback" class="sr-only">
                                            Runs Small
                                        </label>
                                        <label class="cursor-pointer border rounded-xl py-2 px-1 text-[11px] font-semibold transition-all"
                                               :class="fitFeedback === 'true_to_size' ? 'border-festive-rose bg-festive-rose/10 text-festive-rose' : 'border-festive-plum/20 bg-white text-festive-plum/70'">
                                            <input type="radio" name="fit_feedback" value="true_to_size" x-model="fitFeedback" class="sr-only">
                                            True to Size
                                        </label>
                                        <label class="cursor-pointer border rounded-xl py-2 px-1 text-[11px] font-semibold transition-all"
                                               :class="fitFeedback === 'runs_large' ? 'border-festive-rose bg-festive-rose/10 text-festive-rose' : 'border-festive-plum/20 bg-white text-festive-plum/70'">
                                            <input type="radio" name="fit_feedback" value="runs_large" x-model="fitFeedback" class="sr-only">
                                            Runs Large
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-festive-plum mb-1.5">Fabric & Quality Rating</label>
                                    <div class="grid grid-cols-3 gap-1.5 text-center">
                                        <label class="cursor-pointer border rounded-xl py-2 px-1 text-[11px] font-semibold transition-all"
                                               :class="qualityRating === 'exceptional' ? 'border-festive-rose bg-festive-rose/10 text-festive-rose' : 'border-festive-plum/20 bg-white text-festive-plum/70'">
                                            <input type="radio" name="quality_rating" value="exceptional" x-model="qualityRating" class="sr-only">
                                            Exceptional
                                        </label>
                                        <label class="cursor-pointer border rounded-xl py-2 px-1 text-[11px] font-semibold transition-all"
                                               :class="qualityRating === 'good' ? 'border-festive-rose bg-festive-rose/10 text-festive-rose' : 'border-festive-plum/20 bg-white text-festive-plum/70'">
                                            <input type="radio" name="quality_rating" value="good" x-model="qualityRating" class="sr-only">
                                            Good Quality
                                        </label>
                                        <label class="cursor-pointer border rounded-xl py-2 px-1 text-[11px] font-semibold transition-all"
                                               :class="qualityRating === 'average' ? 'border-festive-rose bg-festive-rose/10 text-festive-rose' : 'border-festive-plum/20 bg-white text-festive-plum/70'">
                                            <input type="radio" name="quality_rating" value="average" x-model="qualityRating" class="sr-only">
                                            Standard
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Detailed Comment --}}
                            <div>
                                <label class="block text-xs font-bold text-festive-plum mb-1">Your Detailed Experience</label>
                                <textarea name="comment" rows="3" required placeholder="Describe the fabric texture, color accuracy, stitching, comfort, and how it looked..." class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose">{{ old('comment', $userReview->comment) }}</textarea>
                            </div>

                            {{-- Photo Upload --}}
                            <div>
                                <label class="block text-xs font-bold text-festive-plum mb-1">Upload Product Photos (Optional, max 3 photos)</label>
                                <input 
                                    type="file" 
                                    name="images[]" 
                                    multiple 
                                    accept="image/png,image/jpeg,image/webp" 
                                    @change="previewImages($event)"
                                    class="w-full text-xs file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-festive-rose/10 file:text-festive-rose hover:file:bg-festive-rose/20 text-festive-plum/70 cursor-pointer"
                                >
                                <p class="text-[10px] text-festive-plum/50 mt-1">Share genuine photos wearing the garment or showcasing the fabric weave & stitching.</p>
                                
                                {{-- Previews --}}
                                <div class="flex gap-2 mt-2" x-show="imagePreviews.length > 0">
                                    <template x-for="(imgSrc, i) in imagePreviews" :key="i">
                                        <div class="w-16 h-16 rounded-xl overflow-hidden border-2 border-festive-rose/40 shadow-xs">
                                            <img :src="imgSrc" class="w-full h-full object-cover">
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-festive-rose text-white text-xs font-bold shadow-md hover:bg-festive-rose/90 transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Save Updated Review & Photos</span>
                            </button>
                        </form>
                    </div>
                </div>

            {{-- 1B: Customer has delivered order & can write a new review --}}
            @elseif(!empty($canReview))
                <div class="mb-8 p-5 sm:p-6 rounded-2xl bg-gradient-to-r from-festive-blush/30 to-festive-lavender/20 border border-festive-rose/20">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="p-1 rounded-md bg-emerald-100 text-emerald-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <h3 class="font-serif font-bold text-festive-plum text-sm sm:text-base">You purchased this item! Share your review & photos</h3>
                    </div>
                    <p class="text-xs text-festive-plum/60 mb-5">Your order was delivered. Help shoppers in Amritsar by sharing your verified experience, sizing fit, and product photos.</p>

                    <form action="{{ route('reviews.store', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        {{-- Interactive Star Rating Picker --}}
                        <div>
                            <label class="block text-xs font-bold text-festive-plum mb-1.5">Rating (1 to 5 Stars) *</label>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="rating" :value="selectedRating">
                                <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                    <button 
                                        type="button" 
                                        @click="selectedRating = star"
                                        class="text-3xl transition-transform hover:scale-125 focus:outline-none"
                                        :class="star <= selectedRating ? 'text-festive-gold' : 'text-festive-plum/20'"
                                    >★</button>
                                </template>
                                <span class="text-xs font-bold text-festive-plum ml-2" x-text="selectedRating + ' / 5 Stars'"></span>
                            </div>
                        </div>

                        {{-- Headline --}}
                        <div>
                            <label class="block text-xs font-bold text-festive-plum mb-1">Review Headline (Summary)</label>
                            <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Pure soft linen, perfect collar fit!" class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-2.5 text-festive-plum focus:ring-festive-rose">
                        </div>

                        {{-- Details: Fit Feedback & Fabric Quality --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-festive-plum mb-1.5">Fit & Sizing</label>
                                <div class="grid grid-cols-3 gap-1.5 text-center">
                                    <label class="cursor-pointer border rounded-xl py-2 px-1 text-[11px] font-semibold transition-all"
                                           :class="fitFeedback === 'runs_small' ? 'border-festive-rose bg-festive-rose/10 text-festive-rose ring-1 ring-festive-rose' : 'border-festive-plum/20 bg-white text-festive-plum/70 hover:border-festive-rose/50'">
                                        <input type="radio" name="fit_feedback" value="runs_small" x-model="fitFeedback" class="sr-only">
                                        Runs Small
                                    </label>
                                    <label class="cursor-pointer border rounded-xl py-2 px-1 text-[11px] font-semibold transition-all"
                                           :class="fitFeedback === 'true_to_size' ? 'border-festive-rose bg-festive-rose/10 text-festive-rose ring-1 ring-festive-rose' : 'border-festive-plum/20 bg-white text-festive-plum/70 hover:border-festive-rose/50'">
                                        <input type="radio" name="fit_feedback" value="true_to_size" x-model="fitFeedback" class="sr-only">
                                        True to Size
                                    </label>
                                    <label class="cursor-pointer border rounded-xl py-2 px-1 text-[11px] font-semibold transition-all"
                                           :class="fitFeedback === 'runs_large' ? 'border-festive-rose bg-festive-rose/10 text-festive-rose ring-1 ring-festive-rose' : 'border-festive-plum/20 bg-white text-festive-plum/70 hover:border-festive-rose/50'">
                                        <input type="radio" name="fit_feedback" value="runs_large" x-model="fitFeedback" class="sr-only">
                                        Runs Large
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-festive-plum mb-1.5">Fabric & Stitching Quality</label>
                                <div class="grid grid-cols-3 gap-1.5 text-center">
                                    <label class="cursor-pointer border rounded-xl py-2 px-1 text-[11px] font-semibold transition-all"
                                           :class="qualityRating === 'exceptional' ? 'border-festive-rose bg-festive-rose/10 text-festive-rose ring-1 ring-festive-rose' : 'border-festive-plum/20 bg-white text-festive-plum/70 hover:border-festive-rose/50'">
                                        <input type="radio" name="quality_rating" value="exceptional" x-model="qualityRating" class="sr-only">
                                        Exceptional
                                    </label>
                                    <label class="cursor-pointer border rounded-xl py-2 px-1 text-[11px] font-semibold transition-all"
                                           :class="qualityRating === 'good' ? 'border-festive-rose bg-festive-rose/10 text-festive-rose ring-1 ring-festive-rose' : 'border-festive-plum/20 bg-white text-festive-plum/70 hover:border-festive-rose/50'">
                                        <input type="radio" name="quality_rating" value="good" x-model="qualityRating" class="sr-only">
                                        Good Quality
                                    </label>
                                    <label class="cursor-pointer border rounded-xl py-2 px-1 text-[11px] font-semibold transition-all"
                                           :class="qualityRating === 'average' ? 'border-festive-rose bg-festive-rose/10 text-festive-rose ring-1 ring-festive-rose' : 'border-festive-plum/20 bg-white text-festive-plum/70 hover:border-festive-rose/50'">
                                        <input type="radio" name="quality_rating" value="average" x-model="qualityRating" class="sr-only">
                                        Standard
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Detailed Comment --}}
                        <div>
                            <label class="block text-xs font-bold text-festive-plum mb-1">Your Detailed Feedback *</label>
                            <textarea name="comment" rows="3" required placeholder="Tell other customers about the fabric quality, stitching, color accuracy, and overall drape..." class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose">{{ old('comment') }}</textarea>
                        </div>

                        {{-- Photo Upload --}}
                        <div class="p-3.5 rounded-xl border border-dashed border-festive-rose/40 bg-white/60">
                            <label class="block text-xs font-bold text-festive-plum mb-1">Upload Product Photos (Optional, max 3 photos)</label>
                            <input 
                                type="file" 
                                name="images[]" 
                                multiple 
                                accept="image/png,image/jpeg,image/webp" 
                                @change="previewImages($event)"
                                class="w-full text-xs file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-festive-rose/10 file:text-festive-rose hover:file:bg-festive-rose/20 text-festive-plum/70 cursor-pointer"
                            >
                            <p class="text-[10px] text-festive-plum/50 mt-1">Upload real photos of the garment, collar, fabric close-up, or fitting.</p>
                            
                            {{-- Previews --}}
                            <div class="flex gap-2 mt-2" x-show="imagePreviews.length > 0">
                                <template x-for="(imgSrc, i) in imagePreviews" :key="i">
                                    <div class="w-16 h-16 rounded-xl overflow-hidden border-2 border-festive-rose/40 shadow-xs">
                                        <img :src="imgSrc" class="w-full h-full object-cover">
                                    </div>
                                </template>
                            </div>
                        </div>

                        <button type="submit" class="px-6 py-3 rounded-xl bg-festive-rose text-white text-xs font-bold shadow-lg shadow-festive-rose/20 hover:bg-festive-rose/90 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Submit Verified Review</span>
                        </button>
                    </form>
                </div>

            {{-- 1C: Customer ordered, but delivery is pending --}}
            @elseif(!empty($orderPendingDelivery))
                <div class="mb-8 p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-amber-900">
                    <div class="flex items-start sm:items-center gap-3">
                        <span class="text-xl">🚚</span>
                        <div>
                            <p class="font-bold">Your order is on the way!</p>
                            <p class="text-[11px] text-amber-800/80">You ordered this item. Once your package is marked as <strong>Delivered</strong>, you will be able to leave your verified review and upload photos here.</p>
                        </div>
                    </div>
                    <a href="{{ route('orders.index') }}" class="px-3.5 py-1.5 rounded-xl bg-amber-200 hover:bg-amber-300 font-semibold text-amber-900 shrink-0 text-center">
                        View Order Status &rarr;
                    </a>
                </div>

            {{-- 1D: Customer is logged in, but has not purchased this item --}}
            @elseif(auth()->check())
                <div class="mb-8 p-4 rounded-2xl bg-festive-ivory/80 border border-festive-plum/10 text-xs flex items-center gap-3 text-festive-plum/70">
                    <span class="p-2 rounded-xl bg-festive-blush text-festive-rose shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </span>
                    <p>
                        <strong class="text-festive-plum">Verified Buyer Review Policy:</strong> Only customers who have purchased and received this product from Bombay Fashion can submit a review and photos.
                    </p>
                </div>

            {{-- 1E: Guest shopper --}}
            @else
                <div class="mb-8 p-4 rounded-2xl bg-festive-blush/30 border border-festive-rose/20 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-festive-plum">
                    <div class="flex items-center gap-3">
                        <span class="p-2 rounded-xl bg-festive-rose/10 text-festive-rose shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        <div>
                            <p class="font-bold">Have you purchased and received this product?</p>
                            <p class="text-[11px] text-festive-plum/70">Sign in to your account to submit your verified customer review and photos.</p>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-full soft-btn-primary text-xs font-semibold text-center shrink-0">
                        Sign In to Review
                    </a>
                </div>
            @endif

            {{-- SECTION 2: APPROVED REVIEWS LIST --}}
            @if($product->approvedReviews->isEmpty())
                <div class="py-10 text-center text-festive-plum/50">
                    <div class="w-12 h-12 rounded-full bg-festive-blush mx-auto flex items-center justify-center text-festive-rose mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <p class="text-sm font-serif font-bold text-festive-plum">No Customer Reviews Yet</p>
                    <p class="text-xs text-festive-plum/60 mt-1">Be the first verified patron from Amritsar to share your feedback and photos!</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($product->approvedReviews as $review)
                        <div class="p-4 sm:p-5 rounded-2xl bg-festive-ivory/60 border border-festive-plum/5 space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-festive-rose/10 text-festive-rose font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($review->user->name ?? 'C', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-festive-plum">{{ $review->user->name ?? 'Customer' }}</span>
                                            <span class="inline-flex items-center gap-1 text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full font-bold border border-emerald-200">
                                                ✓ Verified Buyer
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-[11px] text-festive-plum/40">{{ $review->created_at->format('d M Y') }}</span>
                            </div>

                            {{-- Rating Stars & Title --}}
                            <div class="flex flex-wrap items-center gap-2.5">
                                <div class="flex text-festive-gold text-xs">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                    @endfor
                                </div>
                                @if($review->title)
                                    <span class="font-serif font-bold text-xs sm:text-sm text-festive-plum">{{ $review->title }}</span>
                                @endif
                            </div>

                            {{-- Fit & Quality feedback tags --}}
                            @if($review->fit_feedback || $review->quality_rating)
                                <div class="flex flex-wrap items-center gap-2 text-[10px] text-festive-plum/70">
                                    @if($review->fit_feedback)
                                        <span class="px-2 py-0.5 rounded-md bg-white border border-festive-plum/10">
                                            Fit: <strong>{{ ucwords(str_replace('_', ' ', $review->fit_feedback)) }}</strong>
                                        </span>
                                    @endif
                                    @if($review->quality_rating)
                                        <span class="px-2 py-0.5 rounded-md bg-white border border-festive-plum/10">
                                            Quality: <strong>{{ ucfirst($review->quality_rating) }}</strong>
                                        </span>
                                    @endif
                                </div>
                            @endif

                            {{-- Comment --}}
                            <p class="text-xs text-festive-plum/85 leading-relaxed">{{ $review->comment }}</p>

                            {{-- Attached Customer Photos --}}
                            @if(!empty($review->images))
                                <div class="pt-1">
                                    <span class="text-[10px] font-bold text-festive-plum/50 uppercase tracking-wider block mb-1.5">Customer Photos</span>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($review->images as $img)
                                            <div 
                                                @click="modalPhotoUrl = '{{ $img['image_url'] }}'; photoModalOpen = true"
                                                class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden border border-festive-plum/10 shadow-xs cursor-pointer hover:scale-105 transition-all group relative bg-white"
                                            >
                                                <img src="{{ $img['image_url'] }}" alt="Customer product photo" class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white text-[10px]">
                                                    🔍
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Lightbox Photo Modal --}}
            <div 
                x-show="photoModalOpen" 
                x-cloak 
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-festive-plum/70 backdrop-blur-sm"
                @click.self="photoModalOpen = false"
                @keydown.escape.window="photoModalOpen = false"
            >
                <div class="relative max-w-2xl max-h-[85vh] bg-white rounded-2xl overflow-hidden shadow-2xl p-2">
                    <button 
                        @click="photoModalOpen = false" 
                        class="absolute top-3 right-3 z-10 w-8 h-8 rounded-full bg-white/90 text-festive-plum hover:text-festive-rose font-bold flex items-center justify-center shadow"
                    >&times;</button>
                    <img :src="modalPhotoUrl" alt="Customer Photo Enlarged" class="max-h-[80vh] w-auto mx-auto object-contain rounded-xl">
                </div>
            </div>
        </div>

        {{-- Related Products --}}
        @if($relatedProducts->isNotEmpty())
            <div class="mb-12">
                <h2 class="text-2xl font-serif font-bold text-festive-plum mb-6">
                    You May Also Like
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($relatedProducts as $rel)
                        @include('products.partials.card', ['product' => $rel])
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    {{-- Size Guide Modal --}}
    <div 
        x-show="sizeGuideOpen" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-festive-plum/60 backdrop-blur-md"
        @click.self="sizeGuideOpen = false"
        @keydown.escape.window="sizeGuideOpen = false"
    >
        <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 max-w-2xl w-full bg-white shadow-2xl border border-white max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 sm:pb-4 border-b border-festive-plum/10 mb-4">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-festive-rose/10 text-festive-rose">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-base sm:text-lg font-serif font-bold text-festive-plum">Bombay Fashion Size & Fit Guide</h3>
                        <p class="text-xs text-festive-plum/60">Mahila College Road, Amritsar • Tailored fit guarantee</p>
                    </div>
                </div>
                <button 
                    @click="sizeGuideOpen = false" 
                    class="w-8 h-8 rounded-full bg-festive-blush text-festive-plum hover:text-festive-rose font-bold text-lg flex items-center justify-center transition-colors cursor-pointer"
                >&times;</button>
            </div>

            {{-- Tabs --}}
            <div class="flex items-center justify-between gap-2 border-b border-festive-plum/10 pb-3 mb-4">
                <div class="inline-flex rounded-xl p-1 bg-festive-blush/50 text-xs font-bold">
                    <button 
                        type="button" 
                        @click="guideTab = 'measurements'" 
                        :class="guideTab === 'measurements' ? 'bg-white text-festive-rose shadow-sm' : 'text-festive-plum/70 hover:text-festive-rose'"
                        class="px-3 py-1.5 rounded-lg transition-all cursor-pointer"
                    >
                        📏 Measurement Chart
                    </button>
                    <button 
                        type="button" 
                        @click="guideTab = 'how_to'" 
                        :class="guideTab === 'how_to' ? 'bg-white text-festive-rose shadow-sm' : 'text-festive-plum/70 hover:text-festive-rose'"
                        class="px-3 py-1.5 rounded-lg transition-all cursor-pointer"
                    >
                        📐 How to Measure Body
                    </button>
                </div>

                <div class="inline-flex rounded-lg p-0.5 bg-festive-blush/60 border border-festive-plum/15 text-[10px] font-bold">
                    <button 
                        type="button" 
                        @click="unit = 'in'" 
                        :class="unit === 'in' ? 'bg-festive-rose text-white shadow-xs' : 'text-festive-plum/70 hover:text-festive-rose'"
                        class="px-2 py-0.5 rounded transition-all cursor-pointer"
                    >
                        IN (″)
                    </button>
                    <button 
                        type="button" 
                        @click="unit = 'cm'" 
                        :class="unit === 'cm' ? 'bg-festive-rose text-white shadow-xs' : 'text-festive-plum/70 hover:text-festive-rose'"
                        class="px-2 py-0.5 rounded transition-all cursor-pointer"
                    >
                        CM
                    </button>
                </div>
            </div>

            {{-- Tab 1: Measurements Chart --}}
            <div x-show="guideTab === 'measurements'" class="space-y-4">
                <div class="flex items-center justify-between text-xs text-festive-plum/70">
                    <span class="font-bold text-festive-plum font-serif">{{ $chartTitle }}</span>
                    <span>Units: <strong class="text-festive-rose font-mono" x-text="unit === 'cm' ? 'Centimeters' : 'Inches'"></strong></span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-festive-plum/15 bg-white shadow-inner">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-festive-blush/60 text-festive-plum font-bold border-b border-festive-plum/15">
                                @foreach($chartColumns as $col)
                                    <th class="py-2.5 px-3 text-center first:text-left">{{ $col }}</th>
                                @endforeach
                                <th class="py-2.5 px-3 text-center">Fit Style</th>
                                <th class="py-2.5 px-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-festive-plum/10 text-festive-plum/80">
                            @foreach($chartRows as $row)
                                @php
                                    $rowSize = $row['size'] ?? '';
                                @endphp
                                <tr 
                                    @click="selectVariantBySize('{{ $rowSize }}')"
                                    class="cursor-pointer hover:bg-festive-blush/30 transition-colors"
                                    :class="{
                                        'bg-festive-rose/10 font-bold border-l-4 border-l-festive-rose text-festive-rose': currentVariant && (currentVariant.size === '{{ $rowSize }}' || '{{ $rowSize }}'.includes(currentVariant.size))
                                    }"
                                >
                                    <td class="py-2.5 px-3 text-left font-bold text-festive-plum whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $rowSize }}</span>
                                            <template x-if="currentVariant && (currentVariant.size === '{{ $rowSize }}' || '{{ $rowSize }}'.includes(currentVariant.size))">
                                                <span class="w-2 h-2 rounded-full bg-festive-rose"></span>
                                            </template>
                                        </div>
                                    </td>

                                    @if($sizeChartType === 'bottom')
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['waist'] }})"></td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['inseam'] }})"></td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['hip'] }})"></td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['thigh'] }})"></td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['length'] }})"></td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['rise'] }})"></td>
                                    @elseif($sizeChartType === 'drape')
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap">{{ $row['length_m'] }} (<span x-text="formatVal({{ $row['length_in'] }})"></span>)</td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['width_in'] }})"></td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap">{{ $row['blouse'] }}</td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap">{{ $row['pallu'] }}</td>
                                    @elseif($sizeChartType === 'kids')
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['chest'] }})"></td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['waist'] }})"></td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['length'] }})"></td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap">{{ $row['height'] }}</td>
                                    @else
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['chest'] }})"></td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['shoulder'] }})"></td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['length'] }})"></td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['sleeve'] }})"></td>
                                        <td class="py-2.5 px-3 text-center whitespace-nowrap" x-text="formatVal({{ $row['collar'] }})"></td>
                                    @endif

                                    <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-festive-lavender/40 text-festive-plum">
                                            {{ $row['fit'] ?? 'Standard Fit' }}
                                        </span>
                                    </td>

                                    <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                        <button 
                                            type="button" 
                                            @click.stop="selectVariantBySize('{{ $rowSize }}'); sizeGuideOpen = false;"
                                            class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all cursor-pointer"
                                            :class="currentVariant && (currentVariant.size === '{{ $rowSize }}' || '{{ $rowSize }}'.includes(currentVariant.size)) ? 'bg-festive-rose text-white shadow-xs' : 'bg-festive-blush text-festive-plum hover:bg-festive-rose hover:text-white'"
                                        >
                                            <span x-text="currentVariant && (currentVariant.size === '{{ $rowSize }}' || '{{ $rowSize }}'.includes(currentVariant.size)) ? '✓ Selected' : 'Choose Size'"></span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab 2: How to Measure Body --}}
            <div x-show="guideTab === 'how_to'" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3 rounded-xl bg-festive-blush/30 border border-festive-plum/10">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="w-6 h-6 rounded-full bg-festive-rose text-white text-xs font-bold flex items-center justify-center">1</span>
                            <span class="text-xs font-bold text-festive-plum">Waist / Natural Waist</span>
                        </div>
                        <p class="text-[11px] text-festive-plum/70 leading-relaxed">
                            Measure around your natural waistline, where you normally wear your trousers. Keep one finger between tape and body for breathing ease.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-festive-blush/30 border border-festive-plum/10">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="w-6 h-6 rounded-full bg-festive-rose text-white text-xs font-bold flex items-center justify-center">2</span>
                            <span class="text-xs font-bold text-festive-plum">Chest / Bust</span>
                        </div>
                        <p class="text-[11px] text-festive-plum/70 leading-relaxed">
                            Wrap tape around the fullest part of your chest, keeping the tape level under your armpits and across shoulder blades.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-festive-blush/30 border border-festive-plum/10">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="w-6 h-6 rounded-full bg-festive-rose text-white text-xs font-bold flex items-center justify-center">3</span>
                            <span class="text-xs font-bold text-festive-plum">Inseam / Pant Length</span>
                        </div>
                        <p class="text-[11px] text-festive-plum/70 leading-relaxed">
                            Measure along the inside seam of your leg from the crotch point down to your ankle bone or desired trouser hem.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-festive-blush/30 border border-festive-plum/10">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="w-6 h-6 rounded-full bg-festive-rose text-white text-xs font-bold flex items-center justify-center">4</span>
                            <span class="text-xs font-bold text-festive-plum">Hips & Seat</span>
                        </div>
                        <p class="text-[11px] text-festive-plum/70 leading-relaxed">
                            Stand with feet together and measure around the fullest circumference of your hips and rear for proper ease.
                        </p>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start gap-2">
                    <span class="text-base shrink-0">💡</span>
                    <div>
                        <strong>Pro-Tip from Bombay Fashion Amritsar:</strong>
                        <p class="text-[11px] text-amber-800 mt-0.5">
                            If you find yourself between two sizes, we suggest choosing the larger size. Our store at Mahila College Road offers free on-the-spot waist and length tailoring!
                        </p>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-4 mt-4 border-t border-festive-plum/10 text-xs">
                <div class="flex items-center gap-2 text-festive-plum/70">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Free custom hem & waist alteration in Amritsar</span>
                </div>
                <button 
                    type="button" 
                    @click="sizeGuideOpen = false" 
                    class="px-5 py-2 rounded-xl bg-festive-rose text-white font-bold hover:bg-festive-rose/90 transition-colors ml-auto cursor-pointer"
                >
                    Got It, Continue Shopping
                </button>
            </div>
        </div>
    </div>

    {{-- Full Image Zoom Modal --}}
    <div 
        x-show="zoomOpen" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-festive-plum/60 backdrop-blur-md"
        @click.self="zoomOpen = false"
    >
        <div class="relative max-w-4xl max-h-[90vh] bg-white rounded-2xl overflow-hidden shadow-2xl p-2">
            <button @click="zoomOpen = false" class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-white/80 backdrop-blur shadow flex items-center justify-center text-festive-plum hover:text-festive-rose font-bold">
                &times;
            </button>
            <img :src="activeImage" :alt="'Enlarged view'" class="max-h-[85vh] w-auto mx-auto object-contain rounded-xl">
        </div>
    </div>
</div>
@endsection
