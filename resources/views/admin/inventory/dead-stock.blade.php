@extends('layouts.admin')

@section('title', 'Slow-Moving & Dead Stock Analyzer - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800">
                    Stock Health Analyzer
                </span>
                <span class="text-xs text-plum/50 font-medium">Bombay Fashion Amritsar</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-plum mt-1">
                Slow-Moving & Dead Stock Analyzer
            </h1>
            <p class="text-xs text-plum/70 mt-1">
                Garments and fabrics that haven't generated sales in {{ $days }}+ days. Clear these slow-moving racks via festive discounts or clearance flash sales to free up locked capital!
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('admin.inventory.reorder') }}" 
                class="px-4 py-2.5 rounded-xl text-xs font-semibold border border-plum/20 bg-white text-plum hover:bg-plum/5 shadow-xs transition-all"
            >
                Low Stock Alerts
            </a>
        </div>
    </div>

    {{-- Filter Presets --}}
    <div class="flex flex-wrap gap-2 items-center bg-white/70 backdrop-blur-md p-2 rounded-2xl border border-primary-100/60 shadow-xs">
        <span class="text-[11px] font-bold uppercase tracking-wider text-plum/50 px-2">Days Without Sale:</span>
        @foreach([30 => '30+ Days', 60 => '60+ Days (Stagnant)', 90 => '90+ Days (Critical Dead Stock)'] as $d => $lbl)
            <a 
                href="{{ route('admin.inventory.dead-stock', ['days' => $d]) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $days == $d ? 'bg-primary text-white shadow-xs' : 'text-plum/70 hover:bg-white hover:text-primary' }}"
            >
                {{ $lbl }}
            </a>
        @endforeach
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Total Unsold Garments</span>
            <span class="text-3xl font-serif font-bold text-amber-700">{{ number_format($totalDeadUnits) }} pcs</span>
            <span class="text-[11px] text-plum/60 block mt-1">Sitting on showroom shelves</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Working Capital Trapped</span>
            <span class="text-3xl font-serif font-bold text-rose-600">₹{{ number_format($deadCapitalLocked, 2) }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">Inventory value not converted to cash</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Recommended Clearance Strategy</span>
            <span class="text-base font-bold text-plum mt-1 block">Offer 20% - 35% Clearance Discount</span>
            <span class="text-[11px] text-plum/60 block mt-1">Promote on homepage banner to clear inventory</span>
        </div>
    </div>

    {{-- Dead Stock Products Table --}}
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/90 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-plum/10 mb-4">
            <h2 class="text-lg font-serif font-bold text-plum">
                Garments With Zero Sales in Last {{ $days }} Days
            </h2>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-amber-50 text-amber-800">
                {{ $deadProducts->total() }} Slow-Moving Styles
            </span>
        </div>

        @if($deadProducts->isEmpty())
            <div class="py-12 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-sm font-bold text-plum">Fantastic! Zero dead stock found for {{ $days }}+ days.</h3>
                <p class="text-xs text-plum/60 mt-1">All in-stock garments have registered sales recently.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3">Garment Style</th>
                            <th class="py-3 px-3">SKU</th>
                            <th class="py-3 px-3">Category</th>
                            <th class="py-3 px-3">Fabric</th>
                            <th class="py-3 px-3 text-center">Shelf Stock</th>
                            <th class="py-3 px-3 text-right">Retail Price</th>
                            <th class="py-3 px-3 text-right">Trapped Capital</th>
                            <th class="py-3 px-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($deadProducts as $prod)
                            @php
                                $capital = $prod->stock * ($prod->price ?? 0);
                            @endphp
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center space-x-3">
                                        @if($prod->primaryImage)
                                            <img src="{{ $prod->primaryImage->image_url }}" alt="" class="w-10 h-10 object-cover rounded-lg border border-plum/10 shrink-0">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-xs shrink-0">
                                                BF
                                            </div>
                                        @endif
                                        <div>
                                            <span class="font-bold text-plum block leading-tight">{{ $prod->name }}</span>
                                            <span class="text-[10px] text-plum/60">{{ $prod->season }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 font-mono font-semibold text-plum/70">
                                    {{ $prod->sku }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-primary-50 text-primary-700">
                                        {{ $prod->category?->name ?? 'Standard' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 font-medium text-plum/80">
                                    {{ $prod->fabric ?: '—' }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 inline-block">
                                        {{ $prod->stock }} units
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-right font-serif font-bold text-plum/80">
                                    ₹{{ number_format($prod->effective_price ?? $prod->price, 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-right font-serif font-bold text-rose-600">
                                    ₹{{ number_format($capital, 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <a 
                                        href="{{ route('admin.products.edit', $prod->id) }}" 
                                        class="px-3 py-1.5 rounded-xl font-bold bg-gold-600 text-white hover:bg-gold-700 shadow-xs transition text-[11px]"
                                    >
                                        Apply Clearance Sale
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $deadProducts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
