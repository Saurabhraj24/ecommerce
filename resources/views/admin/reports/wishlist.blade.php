@extends('layouts.admin')

@section('title', 'Customer Wishlist & Anticipated Demand - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-pink-100 text-pink-800">
                    Shopper Interest
                </span>
                <span class="text-xs text-plum/50 font-medium">Bombay Fashion Amritsar</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-plum mt-1">
                Customer Wishlist Demand Insights
            </h1>
            <p class="text-xs text-plum/70 mt-1">
                Discover which bridal sarees, suits, and uniforms customers are bookmarking. Predict customer buying intent before weddings and festivals!
            </p>
        </div>
        <a 
            href="{{ route('admin.reports.cloth-sales') }}" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold border border-plum/20 bg-white text-plum hover:bg-plum/5 transition-all self-start sm:self-auto"
        >
            &larr; Back to Cloth Sales
        </a>
    </div>

    {{-- Wishlisted Garments Table --}}
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/90 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-plum/10 mb-4">
            <h2 class="text-lg font-serif font-bold text-plum">
                Most Favorited Garments by Customers
            </h2>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-pink-50 text-pink-700">
                {{ $wishlistedProducts->total() }} Demand Signals
            </span>
        </div>

        @if($wishlistedProducts->isEmpty())
            <div class="py-12 text-center text-xs text-plum/50 italic">
                No items have been wishlisted by customers yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3">Garment Style</th>
                            <th class="py-3 px-3">Category</th>
                            <th class="py-3 px-3">Fabric</th>
                            <th class="py-3 px-3 text-center">Wishlisted by Shoppers</th>
                            <th class="py-3 px-3 text-center">Current Showroom Stock</th>
                            <th class="py-3 px-3 text-right">Retail Price</th>
                            <th class="py-3 px-3 text-center">Restock Signal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($wishlistedProducts as $item)
                            @php
                                $prod = $item->product;
                                $stock = $prod?->stock ?? 0;
                            @endphp
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center space-x-3">
                                        @if($prod && $prod->primaryImage)
                                            <img src="{{ $prod->primaryImage->image_url }}" alt="" class="w-10 h-10 object-cover rounded-lg border border-plum/10 shrink-0">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-xs shrink-0">
                                                BF
                                            </div>
                                        @endif
                                        <div>
                                            <span class="font-bold text-plum block leading-tight">{{ $prod?->name ?? 'Deleted Item' }}</span>
                                            <span class="text-[10px] text-plum/60 font-mono">{{ $prod?->sku }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-primary-50 text-primary-700">
                                        {{ $prod?->category?->name ?? 'Standard' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 font-medium text-plum/80">
                                    {{ $prod?->fabric ?: '—' }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-pink-100 text-pink-700 inline-block">
                                        ❤️ {{ $item->wishlists_count }} shoppers
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="px-2.5 py-0.5 rounded text-[11px] font-bold {{ $stock <= 0 ? 'bg-rose-100 text-rose-700' : ($stock <= 5 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                                        {{ $stock }} units left
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-right font-serif font-bold text-primary">
                                    ₹{{ number_format($prod?->effective_price ?? $prod?->price ?? 0, 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    @if($item->wishlists_count > $stock && $stock <= 5)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            ⚠️ High Risk of Out-of-Stock
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                            🟢 Sufficient
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $wishlistedProducts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
