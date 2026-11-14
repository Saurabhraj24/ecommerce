@extends('layouts.admin')

@section('title', 'Manage Products - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-plum">Products & Clothing Catalog</h1>
            <p class="text-xs text-plum/70 mt-1">Manage ethnic collections, sarees, uniforms, and variants.</p>
        </div>
        <a 
            href="{{ route('admin.products.create') }}" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold bg-primary text-white shadow-md hover:bg-primary-700 transition-all flex items-center gap-1.5 self-start sm:self-auto"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Product
        </a>
    </div>

    {{-- Filter Bar --}}
    <div class="glass-card rounded-2xl p-4 sm:p-5 border border-white/80 bg-white/80 shadow-sm">
        <form method="GET" action="{{ route('admin.products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end text-xs">
            
            {{-- Search --}}
            <div class="lg:col-span-2">
                <label class="block font-semibold text-plum mb-1">Search Products</label>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Search by name, SKU, or fabric..." 
                    class="w-full rounded-xl border-plum/20 bg-white/70 p-2.5 text-plum focus:ring-primary"
                >
            </div>

            {{-- Category Filter --}}
            <div>
                <label class="block font-semibold text-plum mb-1">Category</label>
                <select name="category_id" class="w-full rounded-xl border-plum/20 bg-white/70 p-2.5 text-plum focus:ring-primary">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Season Filter --}}
            <div>
                <label class="block font-semibold text-plum mb-1">Season</label>
                <select name="season" class="w-full rounded-xl border-plum/20 bg-white/70 p-2.5 text-plum focus:ring-primary">
                    <option value="">All Seasons</option>
                    <option value="winter" {{ request('season') == 'winter' ? 'selected' : '' }}>Winter Wear</option>
                    <option value="summer" {{ request('season') == 'summer' ? 'selected' : '' }}>Summer Wear</option>
                    <option value="all_season" {{ request('season') == 'all_season' ? 'selected' : '' }}>All Season</option>
                </select>
            </div>

            {{-- Submit & Reset --}}
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl font-bold bg-primary text-white hover:bg-primary-700 shadow-sm transition-all text-center">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'category_id', 'season', 'stock_status']))
                    <a href="{{ route('admin.products.index') }}" class="py-2.5 px-3 rounded-xl border border-plum/20 text-plum hover:bg-plum/5 text-center">
                        Clear
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- Products Table Card --}}
    <div class="glass-card rounded-3xl border border-white/80 bg-white/85 shadow-md overflow-hidden">
        @if($products->isEmpty())
            {{-- Clean Empty State - STRICT NO FAKE DATA --}}
            <div class="p-12 text-center max-w-md mx-auto">
                <div class="w-16 h-16 rounded-full bg-primary-100 text-primary-800 mx-auto flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <h3 class="text-lg font-serif font-bold text-plum mb-1">No Products In Catalog</h3>
                <p class="text-xs text-plum/60 mb-5">
                    @if(request()->query())
                        No products match your search filters. Try clearing your search parameters.
                    @else
                        Your catalog is currently empty. Click "Add New Product" to upload your first Amritsar store item with Cloudinary photos.
                    @endif
                </p>
                @if(request()->query())
                    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-primary text-white shadow">
                        Reset Filters
                    </a>
                @else
                    <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl text-xs font-bold bg-primary text-white shadow hover:bg-primary-700">
                        + Add First Product
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px] bg-plum/5">
                            <th class="py-3 px-4">Item</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Price (₹)</th>
                            <th class="py-3 px-4">Stock</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Featured</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($products as $prod)
                            @php
                                $thumb = $prod->primaryImage?->image_url ?? $prod->images->first()?->image_url ?? null;
                            @endphp
                            <tr class="hover:bg-plum/5 transition-colors">
                                {{-- Thumbnail & Info --}}
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-12 rounded-lg bg-plum/10 overflow-hidden flex-shrink-0 border border-plum/10">
                                            @if($thumb)
                                                <img src="{{ $thumb }}" alt="{{ $prod->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-[8px] text-plum/40">No Pic</div>
                                            @endif
                                        </div>
                                        <div class="min-w-0 max-w-xs">
                                            <a href="{{ route('admin.products.edit', $prod->id) }}" class="font-bold text-plum hover:text-primary transition-colors truncate block">
                                                {{ $prod->name }}
                                            </a>
                                            <span class="text-[10px] font-mono text-plum/50">SKU: {{ $prod->sku }}</span>
                                            @if($prod->is_school_uniform)
                                                <span class="inline-block ml-1 text-[9px] font-bold text-primary-800 bg-primary-100 px-1.5 py-0.2 rounded">Uniform</span>
                                            @endif
                                            @if($prod->creator_name)
                                                <span class="block text-[10px] text-plum/60 mt-0.5 truncate">
                                                    Uploaded by: <strong class="text-plum">{{ $prod->creator_name }}</strong>
                                                    @if(auth()->user()->isAdmin() && $prod->creator_email)
                                                        ({{ $prod->creator_email }})
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Category --}}
                                <td class="py-3 px-4 text-plum/80">
                                    {{ $prod->category?->name ?? 'Uncategorized' }}
                                    @if($prod->season && $prod->season !== 'all_season')
                                        <span class="block text-[10px] text-plum/50">{{ ucfirst($prod->season) }}</span>
                                    @endif
                                </td>

                                {{-- Price --}}
                                <td class="py-3 px-4 font-serif font-bold text-plum">
                                    ₹{{ number_format($prod->discount_price ?? $prod->price, 2) }}
                                    @if($prod->discount_price && $prod->discount_price < $prod->price)
                                        <span class="block text-[10px] text-plum/40 line-through font-sans">
                                            ₹{{ number_format($prod->price, 2) }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Stock Quantity --}}
                                <td class="py-3 px-4">
                                    @if($prod->stock <= 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                            Out of Stock
                                        </span>
                                    @elseif($prod->stock <= 5)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            Low: {{ $prod->stock }} left
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-800">
                                            {{ $prod->stock }} in stock
                                        </span>
                                    @endif
                                    @if($prod->variants->count() > 0)
                                        <span class="block text-[10px] text-plum/50 mt-0.5">{{ $prod->variants->count() }} variants</span>
                                    @endif
                                </td>

                                {{-- Active Toggle --}}
                                <td class="py-3 px-4 text-center">
                                    <form action="{{ route('admin.products.toggleActive', $prod->id) }}" method="POST">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold transition-all {{ $prod->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                                        >
                                            {{ $prod->is_active ? 'Active' : 'Draft' }}
                                        </button>
                                    </form>
                                </td>

                                {{-- Featured Toggle --}}
                                <td class="py-3 px-4 text-center">
                                    <form action="{{ route('admin.products.toggleFeatured', $prod->id) }}" method="POST">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="text-sm transition-transform hover:scale-125 {{ $prod->is_featured ? 'text-gold-500' : 'text-plum/20' }}"
                                            title="Toggle Featured"
                                        >
                                            ★
                                        </button>
                                    </form>
                                </td>

                                {{-- Actions --}}
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a 
                                            href="{{ route('admin.products.edit', $prod->id) }}" 
                                            class="p-1.5 rounded-lg border border-plum/20 text-plum hover:bg-plum/5 transition-colors"
                                            title="Edit Product"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        @if(auth()->user()->isAdmin())
                                            <form action="{{ route('admin.products.destroy', $prod->id) }}" method="POST" onsubmit="return confirm('Move product to trash?')">
                                                @csrf
                                                @method('DELETE')
                                                <button 
                                                    type="submit" 
                                                    class="p-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 transition-colors"
                                                    title="Delete Product (Admin Only)"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-plum/10">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
