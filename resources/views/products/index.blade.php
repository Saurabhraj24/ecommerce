@extends('layouts.app')

@section('title', 'All Collections - Bombay Fashion Amritsar')
@section('meta_description', 'Browse the curated collection of ethnic wear, sarees, school uniforms, and casual attire at Bombay Fashion, Amritsar.')

@section('content')
@php
    $activeFilterCount = 0;
    if(request('category')) $activeFilterCount++;
    if(request('subcategory')) $activeFilterCount++;
    if(request('season')) $activeFilterCount++;
    if(request('size')) $activeFilterCount += is_array(request('size')) ? count(request('size')) : 1;
    if(request('min_price') || request('max_price')) $activeFilterCount++;
    if(request('discount')) $activeFilterCount++;
    if(request('in_stock')) $activeFilterCount++;
@endphp

<div class="py-8 bg-festive-ivory/50" x-data="{ mobileFiltersOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Breadcrumb & Title --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <nav class="flex text-xs text-festive-plum/60 mb-2 overflow-hidden" aria-label="Breadcrumb">
                    <ol class="flex flex-wrap items-center gap-1 text-[11px] sm:text-xs">
                        <li><a href="{{ route('home') }}" class="hover:text-festive-rose transition-colors">Home</a></li>
                        <li><span class="text-festive-plum/30">/</span></li>
                        <li class="text-festive-plum font-semibold">Collections</li>
                        @if(request('category'))
                            <li><span class="text-festive-plum/30">/</span></li>
                            <li class="text-festive-rose font-medium truncate max-w-[160px] sm:max-w-xs">{{ request('category') }}</li>
                        @endif
                    </ol>
                </nav>
                <h1 class="text-3xl sm:text-4xl font-serif font-bold text-festive-plum">
                    @if(request('category'))
                        {{ ucwords(str_replace('-', ' ', request('category'))) }}
                    @elseif(request('search'))
                        Search results for "{{ request('search') }}"
                    @elseif(request('season'))
                        {{ ucfirst(request('season')) }} Collection
                    @else
                        Explore Our Collections
                    @endif
                </h1>
                <p class="text-sm text-festive-plum/70 mt-1">
                    Authentic Punjabi wear, bridal sarees, school uniforms & seasonal attire in Amritsar.
                </p>
            </div>

            {{-- Actions Bar: Mobile Filter Toggle + Sorting Bar --}}
            <div class="flex items-center justify-between sm:justify-end gap-3 w-full md:w-auto">
                {{-- Mobile Filter Toggle Button --}}
                <button 
                    type="button" 
                    @click="mobileFiltersOpen = true"
                    class="lg:hidden inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-white/90 border border-festive-plum/15 text-festive-plum hover:text-festive-rose hover:border-festive-rose/40 shadow-sm transition-all text-xs font-semibold"
                    aria-label="Open filter sidebar"
                >
                    <svg class="w-4 h-4 text-festive-rose" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span>Filters</span>
                    @if($activeFilterCount > 0)
                        <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 rounded-full bg-festive-rose text-white text-[10px] font-bold">
                            {{ $activeFilterCount }}
                        </span>
                    @endif
                </button>

                {{-- Sorting Bar --}}
                <form method="GET" action="{{ route('products.index') }}" id="sortForm" class="flex items-center gap-2">
                    @foreach(request()->except(['sort', 'page']) as $key => $val)
                        @if(is_array($val))
                            @foreach($val as $item)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endif
                    @endforeach
                    <label for="sort" class="text-xs font-medium text-festive-plum/70 whitespace-nowrap">Sort By:</label>
                    <select 
                        name="sort" 
                        id="sort" 
                        onchange="document.getElementById('sortForm').submit()"
                        class="text-xs bg-white/80 border border-festive-plum/15 rounded-xl px-3 py-2 text-festive-plum focus:ring-2 focus:ring-festive-rose/40 focus:border-festive-rose shadow-sm"
                    >
                        <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>Newest Arrivals</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popularity</option>
                    </select>
                </form>
            </div>
        </div>

        {{-- Mobile Filter Drawer (Slide-over) --}}
        <div 
            x-show="mobileFiltersOpen" 
            x-cloak 
            class="fixed inset-0 z-50 lg:hidden overflow-hidden" 
            role="dialog" 
            aria-modal="true"
            @keydown.window.escape="mobileFiltersOpen = false"
        >
            {{-- Backdrop --}}
            <div 
                x-show="mobileFiltersOpen"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="mobileFiltersOpen = false"
                class="fixed inset-0 bg-festive-plum/50 backdrop-blur-xs"
            ></div>

            {{-- Slide-over Panel --}}
            <div class="fixed inset-y-0 left-0 max-w-xs w-full flex">
                <div 
                    x-show="mobileFiltersOpen"
                    x-transition:enter="transition ease-in-out duration-300 transform"
                    x-transition:enter-start="-translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in-out duration-300 transform"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="-translate-x-full"
                    class="relative w-full max-w-xs bg-[#FFF9F3] flex flex-col h-full shadow-2xl overflow-hidden"
                >
                    {{-- Drawer Header --}}
                    <div class="flex items-center justify-between p-4 px-5 border-b border-festive-plum/10 bg-white/95 backdrop-blur-md">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-festive-rose" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            <h2 class="text-base font-serif font-bold text-festive-plum">Filters</h2>
                            @if($activeFilterCount > 0)
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-festive-blush text-festive-rose">
                                    {{ $activeFilterCount }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            @if($activeFilterCount > 0)
                                <a href="{{ route('products.index') }}" class="text-xs text-festive-rose hover:underline font-semibold">
                                    Clear All
                                </a>
                            @endif
                            <button 
                                type="button" 
                                @click="mobileFiltersOpen = false" 
                                class="p-1.5 rounded-lg text-festive-plum/60 hover:text-festive-rose hover:bg-festive-blush transition-colors"
                                aria-label="Close filters"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Scrollable Drawer Form --}}
                    <div class="flex-1 overflow-y-auto p-5">
                        <form method="GET" action="{{ route('products.index') }}" class="space-y-6">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if(request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif

                            @include('products.partials.filters-content')
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Layout: Filters Sidebar (Desktop) + Products Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            {{-- Filter Sidebar (Hidden on mobile, visible on desktop lg+) --}}
            <aside class="hidden lg:block lg:col-span-1">
                <div class="glass-card rounded-2xl p-6 border border-white/80 sticky top-24 bg-white/80">
                    <div class="flex items-center justify-between pb-4 border-b border-festive-plum/10">
                        <h2 class="text-base font-serif font-bold text-festive-plum flex items-center gap-2">
                            <svg class="w-4 h-4 text-festive-rose" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filters
                        </h2>
                        @if($activeFilterCount > 0)
                            <a href="{{ route('products.index') }}" class="text-xs text-festive-rose hover:underline font-medium">
                                Clear All
                            </a>
                        @endif
                    </div>

                    <form method="GET" action="{{ route('products.index') }}" class="space-y-6 pt-4">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if(request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif

                        @include('products.partials.filters-content')
                    </form>
                </div>
            </aside>

            {{-- Products Grid --}}
            <main class="lg:col-span-3">
                {{-- Active filter tags --}}
                @if(request()->hasAny(['category', 'subcategory', 'season', 'size', 'in_stock', 'discount']))
                    <div class="flex flex-wrap items-center gap-2 mb-6">
                        <span class="text-xs text-festive-plum/60">Active Filters:</span>
                        @if(request('category'))
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs bg-festive-blush text-festive-rose font-medium">
                                Category: {{ request('category') }}
                            </span>
                        @endif
                        @if(request('season'))
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs bg-festive-blush text-festive-rose font-medium">
                                Season: {{ request('season') }}
                            </span>
                        @endif
                        @if(request('in_stock'))
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs bg-festive-blush text-festive-rose font-medium">
                                In Stock
                            </span>
                        @endif
                        <a href="{{ route('products.index') }}" class="text-xs text-festive-rose underline font-semibold ml-2">Clear all</a>
                    </div>
                @endif

                @if($products->isEmpty())
                    {{-- Clean Empty State - STRICT NO FAKE DATA RULE --}}
                    <div class="glass-card rounded-3xl p-12 text-center border border-white/80 bg-white/70 max-w-lg mx-auto my-12">
                        <div class="w-16 h-16 rounded-full bg-festive-blush mx-auto flex items-center justify-center text-festive-rose mb-4">
                            <svg class="w-8 h-8 stroke-current" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-serif font-bold text-festive-plum mb-2">No Products Found</h3>
                        <p class="text-sm text-festive-plum/70 mb-6">
                            @if(request()->query())
                                We couldn't find any products matching your current filters. Try adjusting or clearing your filters.
                            @else
                                Bombay Fashion inventory is being updated directly by store management. Check back shortly for our new arrivals!
                            @endif
                        </p>
                        @if(request()->query())
                            <a href="{{ route('products.index') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl bg-festive-rose text-white text-xs font-bold shadow-md hover:bg-festive-rose/90 transition-all">
                                Reset All Filters
                            </a>
                        @else
                            <a href="{{ route('home') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl bg-festive-rose text-white text-xs font-bold shadow-md hover:bg-festive-rose/90 transition-all">
                                Return to Home
                            </a>
                        @endif
                    </div>
                @else
                    {{-- Grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
                        @foreach($products as $product)
                            @include('products.partials.card', ['product' => $product])
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-10">
                        {{ $products->links() }}
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>
@endsection
