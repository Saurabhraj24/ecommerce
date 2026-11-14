@extends('layouts.admin')

@section('title', 'Cloth & Garment Sales Breakdown - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-primary-100 text-primary-800">
                    Kapda Bikri Audit
                </span>
                <span class="text-xs text-plum/50 font-medium">Bombay Fashion Amritsar</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-plum mt-1">
                Cloth & Garment Sales Breakdown
            </h1>
            <p class="text-xs text-plum/70 mt-1">
                Real-time volume audit: <strong class="text-primary font-bold">Kitna cloth bikha</strong> (total units sold) aur <strong class="text-primary font-bold">kon sa bikha</strong> (item-wise fabric & variant sales).
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('admin.reports.export-cloth-sales', request()->all()) }}" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold bg-primary-600 text-white shadow-md shadow-primary-500/20 hover:bg-primary-700 transition-all flex items-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export Itemized CSV
            </a>
        </div>
    </div>

    {{-- Quick Date Range Presets Bar --}}
    <div class="flex flex-wrap gap-2 items-center bg-white/70 backdrop-blur-md p-2 rounded-2xl border border-primary-100/60 shadow-xs">
        <span class="text-[11px] font-bold uppercase tracking-wider text-plum/50 px-2">Quick Filter:</span>
        @php
            $presets = [
                'today' => 'Today',
                'yesterday' => 'Yesterday',
                'this_week' => 'This Week',
                'this_month' => 'This Month',
                'last_month' => 'Last Month',
                'this_year' => 'This Year',
                'all_time' => 'All Time',
            ];
            $currentPeriod = request('period', 'this_month');
        @endphp
        @foreach ($presets as $key => $label)
            <a 
                href="{{ route('admin.reports.cloth-sales', array_merge(request()->except(['period', 'page']), ['period' => $key])) }}"
                class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $currentPeriod === $key ? 'bg-primary text-white shadow-xs' : 'text-plum/70 hover:bg-white hover:text-primary' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Filter Bar --}}
    <div class="glass-card rounded-2xl p-4 sm:p-5 border border-white/80 bg-white/80 shadow-xs">
        <form method="GET" action="{{ route('admin.reports.cloth-sales') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end text-xs">
            <input type="hidden" name="period" value="custom">

            <div>
                <label class="block font-semibold text-plum mb-1">From Date</label>
                <input 
                    type="date" 
                    name="start_date" 
                    value="{{ $startDate }}" 
                    class="w-full rounded-xl border-plum/20 bg-white/70 p-2 text-plum focus:ring-primary"
                >
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">To Date</label>
                <input 
                    type="date" 
                    name="end_date" 
                    value="{{ $endDate }}" 
                    class="w-full rounded-xl border-plum/20 bg-white/70 p-2 text-plum focus:ring-primary"
                >
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Cloth Category</label>
                <select name="category_id" class="w-full rounded-xl border-plum/20 bg-white/70 p-2 text-plum focus:ring-primary">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Fabric Type</label>
                <select name="fabric" class="w-full rounded-xl border-plum/20 bg-white/70 p-2 text-plum focus:ring-primary">
                    <option value="">All Fabrics</option>
                    @foreach($fabrics as $fab)
                        <option value="{{ $fab }}" {{ request('fabric') == $fab ? 'selected' : '' }}>
                            {{ $fab }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Search Product / SKU</label>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Saree, suit, SKU..." 
                    class="w-full rounded-xl border-plum/20 bg-white/70 p-2 text-plum focus:ring-primary"
                >
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 rounded-xl font-bold bg-primary text-white hover:bg-primary-700 shadow-xs transition-all">
                    Filter
                </button>
                <a href="{{ route('admin.reports.cloth-sales') }}" class="px-3 py-2 rounded-xl font-semibold border border-plum/20 text-plum/70 hover:bg-plum/5 transition-all text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Top Metric Summary Cards ("Kitna Cloth Bikha") --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs relative overflow-hidden">
            <div class="absolute -right-3 -top-3 w-16 h-16 rounded-full bg-primary-100/40 -z-10"></div>
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Total Clothes Sold</span>
            <span class="text-2xl sm:text-3xl font-serif font-bold text-primary">{{ number_format($totalPiecesSold) }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">Garments & pieces in period</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs relative overflow-hidden">
            <div class="absolute -right-3 -top-3 w-16 h-16 rounded-full bg-emerald-100/40 -z-10"></div>
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Gross Cloth Revenue</span>
            <span class="text-2xl sm:text-3xl font-serif font-bold text-emerald-700">₹{{ number_format($totalRevenue, 2) }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">Average ₹{{ number_format($avgSellingPrice, 2) }} / piece</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs relative overflow-hidden">
            <div class="absolute -right-3 -top-3 w-16 h-16 rounded-full bg-gold-100/40 -z-10"></div>
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Today's Garments Sold</span>
            <span class="text-2xl sm:text-3xl font-serif font-bold text-gold-700">{{ number_format($todayPiecesSold) }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">{{ number_format($monthPiecesSold) }} this month total</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs relative overflow-hidden">
            <div class="absolute -right-3 -top-3 w-16 h-16 rounded-full bg-indigo-100/40 -z-10"></div>
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Active Sold Styles</span>
            <span class="text-2xl sm:text-3xl font-serif font-bold text-indigo-700">{{ $uniqueProductsCount }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">Distinct designs ordered</span>
        </div>
    </div>

    {{-- Itemized Cloth Sales Table ("Kon Sa Bikha") --}}
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/90 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-plum/10 gap-2 mb-4">
            <div>
                <h2 class="text-lg font-serif font-bold text-plum">
                    Itemized Garment Breakdown (Kon Sa Bikha)
                </h2>
                <p class="text-xs text-plum/60">
                    Showing sales volume, size/color variant breakdown, revenue, and remaining stock in Amritsar showroom.
                </p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-primary-50 text-primary-700 self-start sm:self-auto">
                {{ $clothSales->total() }} Product Variations
            </span>
        </div>

        @if($clothSales->isEmpty())
            <div class="py-12 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-primary-50 flex items-center justify-center text-primary-400 mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <h3 class="text-sm font-bold text-plum">No garment sales recorded in this date range</h3>
                <p class="text-xs text-plum/60 mt-1">Try broadening the date range filter or clearing search keywords.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-2">Garment Style</th>
                            <th class="py-3 px-2">SKU</th>
                            <th class="py-3 px-2">Category</th>
                            <th class="py-3 px-2">Fabric</th>
                            <th class="py-3 px-2">Variant / Size</th>
                            <th class="py-3 px-2 text-center">Units Sold</th>
                            <th class="py-3 px-2 text-right">Avg Price</th>
                            <th class="py-3 px-2 text-right">Gross Sales</th>
                            <th class="py-3 px-2 text-center">Store Stock</th>
                            <th class="py-3 px-2 text-center">Velocity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($clothSales as $item)
                            @php
                                $product = $item->product;
                                $stock = $product?->stock ?? 0;
                            @endphp
                            <tr class="hover:bg-plum/5 transition-colors">
                                {{-- Product Name & Thumbnail --}}
                                <td class="py-3 px-2">
                                    <div class="flex items-center space-x-3">
                                        @if($product && $product->primaryImage)
                                            <img 
                                                src="{{ $product->primaryImage->image_url }}" 
                                                alt="{{ $item->product_name }}" 
                                                class="w-10 h-10 object-cover rounded-lg border border-plum/10 shrink-0"
                                            >
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center text-primary-600 font-bold text-xs shrink-0">
                                                BF
                                            </div>
                                        @endif
                                        <div>
                                            <span class="font-bold text-plum block leading-tight">{{ $item->product_name }}</span>
                                            @if($product)
                                                <a href="{{ route('admin.products.edit', $product->id) }}" class="text-[10px] text-primary hover:underline">
                                                    Edit Product &rarr;
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- SKU --}}
                                <td class="py-3 px-2 font-mono text-plum/70 font-semibold text-[11px]">
                                    {{ $item->product_sku }}
                                </td>

                                {{-- Category --}}
                                <td class="py-3 px-2">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-primary-50 text-primary-700">
                                        {{ $product?->category?->name ?? 'Standard' }}
                                    </span>
                                </td>

                                {{-- Fabric --}}
                                <td class="py-3 px-2 font-semibold text-plum/80">
                                    {{ $product?->fabric ?? '—' }}
                                </td>

                                {{-- Variant Details --}}
                                <td class="py-3 px-2 font-medium text-plum/70">
                                    {{ $item->variant_details ?: 'Standard Size' }}
                                </td>

                                {{-- Units Sold --}}
                                <td class="py-3 px-2 text-center">
                                    <span class="inline-block px-2.5 py-1 rounded-lg bg-plum/5 font-mono font-bold text-plum text-xs">
                                        {{ $item->total_units_sold }} pcs
                                    </span>
                                </td>

                                {{-- Avg Price --}}
                                <td class="py-3 px-2 text-right font-medium text-plum/70">
                                    ₹{{ number_format($item->avg_unit_price, 2) }}
                                </td>

                                {{-- Gross Sales --}}
                                <td class="py-3 px-2 text-right font-serif font-bold text-primary text-sm">
                                    ₹{{ number_format($item->total_gross_sales, 2) }}
                                </td>

                                {{-- Stock Remaining --}}
                                <td class="py-3 px-2 text-center">
                                    @if($stock <= 0)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">
                                            Out of Stock
                                        </span>
                                    @elseif($stock <= 5)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                            {{ $stock }} left
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            {{ $stock }} in stock
                                        </span>
                                    @endif
                                </td>

                                {{-- Velocity Badge --}}
                                <td class="py-3 px-2 text-center">
                                    @if($item->total_units_sold >= 10)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                            🔥 High Demand
                                        </span>
                                    @elseif($item->total_units_sold >= 3)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            ⚡ Steady
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                            📦 Regular
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $clothSales->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
