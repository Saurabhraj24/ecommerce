@extends('layouts.admin')

@section('title', 'Inventory & Stock Management - Bombay Fashion Admin')

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
            <h1 class="text-2xl font-serif font-bold text-plum">Inventory & Stock Tracking</h1>
            <p class="text-xs text-plum/70 mt-1">Audit clothing counts, adjust inventory with reason tracking, and monitor restock alerts.</p>
        </div>
        <a 
            href="{{ route('admin.inventory.logs') }}" 
            class="px-4 py-2.5 rounded-xl text-xs font-semibold border border-plum/20 bg-white text-plum hover:bg-plum/5 shadow-sm transition-all flex items-center gap-1.5 self-start sm:self-auto"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            View Stock History Logs
        </a>
    </div>

    {{-- Filter Bar --}}
    <div class="glass-card rounded-2xl p-4 sm:p-5 border border-white/80 bg-white/80 shadow-sm">
        <form method="GET" action="{{ route('admin.inventory.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end text-xs">
            <div class="sm:col-span-6">
                <label class="block font-semibold text-plum mb-1">Search Product / SKU</label>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Search by product name or SKU..." 
                    class="w-full rounded-xl border-plum/20 bg-white/70 p-2.5 text-plum focus:ring-primary"
                >
            </div>
            <div class="sm:col-span-4">
                <label class="block font-semibold text-plum mb-1">Stock Level Alert</label>
                <select name="filter" class="w-full rounded-xl border-plum/20 bg-white/70 p-2.5 text-plum focus:ring-primary">
                    <option value="">All Stock Levels</option>
                    <option value="low" {{ request('filter') === 'low' ? 'selected' : '' }}>Low Stock (<= 5 units)</option>
                    <option value="out" {{ request('filter') === 'out' ? 'selected' : '' }}>Out of Stock (0 units)</option>
                </select>
            </div>
            <div class="sm:col-span-2 flex gap-2">
                <button type="submit" class="w-full py-2.5 rounded-xl font-bold bg-primary text-white hover:bg-primary-700 shadow-sm">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Inventory Table --}}
    <div class="glass-card rounded-3xl border border-white/80 bg-white/85 shadow-md overflow-hidden">
        @if($products->isEmpty())
            <div class="p-12 text-center text-plum/60 text-xs italic">
                No inventory records found matching your filters.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px] bg-plum/5">
                            <th class="py-3 px-4">Item & SKU</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Variants Count</th>
                            <th class="py-3 px-4 text-center">Base Stock</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Quick Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($products as $prod)
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3 px-4">
                                    <span class="font-bold text-plum block">{{ $prod->name }}</span>
                                    <span class="font-mono text-[10px] text-plum/50">SKU: {{ $prod->sku }}</span>
                                </td>
                                <td class="py-3 px-4 text-plum/70">
                                    {{ $prod->category?->name ?? 'None' }}
                                </td>
                                <td class="py-3 px-4 text-plum/70">
                                    @if($prod->allVariants->isNotEmpty())
                                        <div class="space-y-1">
                                            @foreach($prod->allVariants as $var)
                                                <div class="flex items-center justify-between text-[11px] bg-plum/5 px-2 py-0.5 rounded">
                                                    <span>{{ $var->size ?? 'Size' }} {{ $var->color ? "({$var->color})" : '' }}</span>
                                                    <span class="font-mono font-bold {{ $var->stock <= 0 ? 'text-rose-600' : ($var->stock <= 5 ? 'text-amber-600' : 'text-emerald-700') }}">
                                                        {{ $var->stock }} left
                                                    </span>
                                                    <button 
                                                        type="button" 
                                                        @click="openAdjust({{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $var->id }}, '{{ addslashes($var->size . ' ' . $var->color) }}', {{ $var->stock }})"
                                                        class="text-primary hover:underline text-[10px] font-bold ml-2"
                                                    >
                                                        Adjust
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-plum/40 italic">Single Item</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-sm font-mono">
                                    {{ $prod->stock }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($prod->stock <= 0)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Out of Stock</span>
                                    @elseif($prod->stock <= 5)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Low Stock</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-800">Healthy</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button 
                                        type="button" 
                                        @click="openAdjust({{ $prod->id }}, '{{ addslashes($prod->name) }}', null, '', {{ $prod->stock }})"
                                        class="px-3 py-1 rounded-xl text-xs font-bold border border-primary text-primary hover:bg-primary-50 transition-colors"
                                    >
                                        Adjust Stock
                                    </button>
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

    {{-- Stock Adjustment Modal --}}
    <div 
        x-show="adjustModal" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-plum/40 backdrop-blur-sm"
        @click.self="adjustModal = false"
    >
        <div class="glass-card rounded-3xl p-6 sm:p-8 max-w-md w-full bg-white shadow-2xl border border-white text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-plum/10 mb-4">
                <h3 class="text-base font-serif font-bold text-plum">Manual Stock Adjustment</h3>
                <button @click="adjustModal = false" class="text-plum/50 hover:text-primary font-bold text-lg">&times;</button>
            </div>

            <div class="p-3 rounded-xl bg-plum/5 mb-4 space-y-1">
                <div class="font-bold text-plum" x-text="productName"></div>
                <template x-if="variantName">
                    <div class="text-primary font-medium" x-text="'Variant: ' + variantName"></div>
                </template>
                <div class="text-plum/60" x-text="'Current Stock: ' + currentStock + ' units'"></div>
            </div>

            <form action="{{ route('admin.inventory.adjust') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" :value="productId">
                <input type="hidden" name="product_variant_id" :value="variantId">

                <div>
                    <label class="block font-semibold text-plum mb-1">Adjustment Type *</label>
                    <select name="adjustment_type" class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum font-bold">
                        <option value="add">Add Units (New Shipment / Restock)</option>
                        <option value="subtract">Subtract Units (Damage / Store Walk-in Sale)</option>
                        <option value="set">Set Exact Count (Physical Inventory Recount)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Quantity *</label>
                    <input type="number" name="quantity" min="0" required class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum font-bold">
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Reason for Audit Log *</label>
                    <input type="text" name="reason" required placeholder="e.g. Received shipment from Surat weaver, or physical stocktake" class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum">
                </div>

                <div class="pt-2 flex justify-end gap-2 border-t border-plum/10">
                    <button type="button" @click="adjustModal = false" class="px-4 py-2 rounded-xl border border-plum/20 text-plum hover:bg-plum/5">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-primary text-white font-bold shadow hover:bg-primary-700">Save Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
