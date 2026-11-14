@extends('layouts.admin')

@section('title', 'Low Stock & Reorder Procurement Alerts - Bombay Fashion Admin')

@section('content')
<div 
    class="space-y-6"
    x-data="{
        adjustModal: false,
        productId: null,
        productName: '',
        variantId: null,
        variantName: '',
        currentStock: 0,
        openAdjust(pid, pname, vid, vname, stock) {
            this.productId = pid;
            this.productName = pname;
            this.variantId = vid;
            this.variantName = vname;
            this.currentStock = stock;
            this.adjustModal = true;
        }
    }"
>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-800">
                    Inventory Replenishment
                </span>
                <span class="text-xs text-plum/50 font-medium">Bombay Fashion Amritsar</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-plum mt-1">
                Low Stock & Reorder Procurement Alerts
            </h1>
            <p class="text-xs text-plum/70 mt-1">
                Urgent re-order warning for clothes & fabrics running out of stock (Stock &le; 5 units). Order fresh lots from Surat/Ludhiana suppliers before selling out!
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('admin.inventory.index') }}" 
                class="px-4 py-2.5 rounded-xl text-xs font-semibold border border-plum/20 bg-white text-plum hover:bg-plum/5 shadow-xs transition-all"
            >
                All Inventory Audit
            </a>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Completely Out of Stock</span>
            <span class="text-3xl font-serif font-bold text-rose-600">{{ $outOfStockCount }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">0 pieces left in showroom</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Low Stock Warning (1 - 5 Units)</span>
            <span class="text-3xl font-serif font-bold text-amber-600">{{ $lowStockCount }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">Require immediate supplier replenishment</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Supplier Restock Policy</span>
            <span class="text-base font-bold text-plum mt-1 block">Lead Time: 3-5 Business Days</span>
            <span class="text-[11px] text-plum/60 block mt-1">Surat Silk & Ludhiana Knitwear hubs</span>
        </div>
    </div>

    {{-- Urgent Procurement List Table --}}
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/90 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-plum/10 mb-4">
            <h2 class="text-lg font-serif font-bold text-plum">
                Garments Requiring Urgent Re-order
            </h2>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-rose-50 text-rose-700">
                {{ $products->total() }} Styles At Risk
            </span>
        </div>

        @if($products->isEmpty())
            <div class="py-12 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-sm font-bold text-plum">Great news! All garments have healthy stock levels.</h3>
                <p class="text-xs text-plum/60 mt-1">No items have stock &le; 5 pieces right now.</p>
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
                            <th class="py-3 px-3 text-center">Current Stock</th>
                            <th class="py-3 px-3 text-center">Suggested Re-order</th>
                            <th class="py-3 px-3 text-right">Retail Price</th>
                            <th class="py-3 px-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($products as $prod)
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
                                    @if($prod->stock <= 0)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 inline-block">
                                            0 Units (Sold Out)
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 inline-block">
                                            {{ $prod->stock }} Units Left
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-indigo-50 text-indigo-700">
                                        +25 pcs recommended
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-right font-serif font-bold text-primary">
                                    ₹{{ number_format($prod->effective_price ?? $prod->price, 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <button 
                                        @click="openAdjust({{ $prod->id }}, '{{ addslashes($prod->name) }}', null, '', {{ $prod->stock }})"
                                        class="px-3 py-1.5 rounded-xl font-bold bg-primary text-white hover:bg-primary-700 shadow-xs transition cursor-pointer text-[11px]"
                                    >
                                        + Receive Stock
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    {{-- Quick Stock Adjustment Modal --}}
    <div 
        x-show="adjustModal" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-festive-plum/60 backdrop-blur-xs"
        style="display: none;"
    >
        <div 
            @click.away="adjustModal = false"
            class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-white/80 space-y-4 text-xs"
        >
            <div class="flex items-center justify-between pb-3 border-b border-plum/10">
                <h3 class="text-base font-serif font-bold text-plum">Receive Fresh Supplier Stock</h3>
                <button @click="adjustModal = false" class="text-plum/50 hover:text-plum font-bold text-lg">&times;</button>
            </div>

            <form method="POST" action="{{ route('admin.inventory.adjust') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" :value="productId">
                <input type="hidden" name="product_variant_id" :value="variantId">
                <input type="hidden" name="adjustment_type" value="add">

                <div>
                    <label class="block font-semibold text-plum/70 mb-1">Product</label>
                    <div class="p-2.5 rounded-xl bg-plum/5 text-plum font-bold" x-text="productName"></div>
                </div>

                <div>
                    <label class="block font-semibold text-plum/70 mb-1">Current Balance in Showroom</label>
                    <div class="p-2.5 rounded-xl bg-amber-50 text-amber-800 font-bold" x-text="currentStock + ' units remaining'"></div>
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Quantity Received from Supplier (+)</label>
                    <input 
                        type="number" 
                        name="quantity" 
                        min="1" 
                        value="25" 
                        required
                        class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum font-bold text-sm focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Lot / Invoice Reference Reason</label>
                    <input 
                        type="text" 
                        name="reason" 
                        value="Fresh stock replenishment lot from supplier" 
                        required
                        class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                    >
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="adjustModal = false" class="px-4 py-2.5 rounded-xl border border-plum/20 text-plum font-semibold hover:bg-plum/5">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-white font-bold hover:bg-primary-700 shadow-md transition">
                        Update & Restock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
