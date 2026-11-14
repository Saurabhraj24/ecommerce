@extends('layouts.admin')

@section('title', 'My Purchases & Garment Orders - Bombay Fashion')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    {{-- Header & Back Link --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <nav class="flex text-xs text-plum/60 mb-1">
                <a href="{{ route('admin.profile.show') }}" class="hover:text-primary">&larr; Back to My Profile</a>
            </nav>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-primary-100 text-primary-800">
                    Personal Orders
                </span>
                <span class="text-xs text-plum/50 font-medium">Showroom Counter &amp; Online Purchases</span>
            </div>
            <h1 class="text-2xl font-serif font-bold text-plum mt-0.5">
                My Purchases &amp; Purchased Clothes
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <a 
                href="{{ route('home') }}" 
                target="_blank"
                class="px-4 py-2 rounded-xl text-xs font-bold bg-primary text-white hover:bg-primary-700 shadow-sm transition flex items-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span>Browse Store Catalog</span>
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/90 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-plum/50 block mb-1">Total Garment Orders</span>
            <div class="text-2xl font-serif font-bold text-plum">
                {{ $orders->total() }} Orders
            </div>
            <span class="text-xs text-plum/60 mt-1 block">Showroom counter &amp; web orders</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/90 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700/70 block mb-1">Total Amount Spent</span>
            <div class="text-2xl font-serif font-bold text-emerald-700">
                ₹{{ number_format((float)$totalSpent, 2) }}
            </div>
            <span class="text-xs text-emerald-800/60 mt-1 block">Paid apparel purchases</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/90 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-primary-700/70 block mb-1">Latest Order</span>
            <div class="text-2xl font-serif font-bold text-primary">
                {{ $orders->first() ? $orders->first()->created_at->format('d M Y') : 'No Orders Yet' }}
            </div>
            <span class="text-xs text-plum/60 mt-1 block">{{ $orders->first() ? '#' . $orders->first()->order_number : 'Start shopping in store' }}</span>
        </div>

    </div>

    {{-- Orders List --}}
    <div class="space-y-4">
        @forelse($orders as $order)
            <div class="glass-card rounded-3xl p-5 sm:p-6 border border-white/80 bg-white/95 shadow-sm space-y-4 hover:shadow-md transition">
                
                {{-- Order Card Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-plum/10 gap-2">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-serif font-bold text-plum text-base">
                            #{{ $order->order_number }}
                        </span>
                        @if($order->isPos())
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                🏬 Showroom Counter POS
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-primary-100 text-primary-800 border border-primary-200">
                                🌐 Online Web Order
                            </span>
                        @endif

                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ in_array($order->order_status, ['delivered', 'confirmed']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ ucfirst($order->order_status) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-3 text-xs">
                        <span class="text-plum/60">
                            {{ $order->created_at->format('d M Y, h:i A') }}
                        </span>

                        {{-- Action Button: Receipt / Invoice --}}
                        @if($order->isPos())
                            <a 
                                href="{{ route('admin.pos.receipt', $order->id) }}" 
                                target="_blank"
                                class="px-3 py-1.5 rounded-xl bg-primary-50 hover:bg-primary-100 text-primary font-bold text-xs border border-primary-200 shadow-xs transition flex items-center gap-1.5"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Thermal Receipt</span>
                            </a>
                        @else
                            <a 
                                href="{{ route('admin.orders.invoice', $order->id) }}" 
                                target="_blank"
                                class="px-3 py-1.5 rounded-xl bg-primary-50 hover:bg-primary-100 text-primary font-bold text-xs border border-primary-200 shadow-xs transition flex items-center gap-1.5"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>PDF Invoice</span>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Purchased Clothes / Items List --}}
                <div class="divide-y divide-plum/5">
                    @foreach($order->items as $item)
                        <div class="py-2.5 flex items-center justify-between text-xs gap-3">
                            <div class="flex items-center space-x-3 min-w-0">
                                @if($item->product?->primaryImage)
                                    <img 
                                        src="{{ $item->product->primaryImage->image_url }}" 
                                        alt="{{ $item->product_name }}" 
                                        class="w-12 h-12 rounded-xl object-cover border border-plum/10 shrink-0 bg-plum/5"
                                    >
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-700 flex items-center justify-center font-bold text-[10px] shrink-0 border border-primary-100">
                                        BF
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <span class="font-bold text-plum block truncate leading-tight">
                                        {{ $item->product_name }}
                                    </span>
                                    <span class="text-[11px] text-plum/60 block mt-0.5">
                                        {{ $item->variant_details ?: 'Standard' }} &bull; Qty: {{ $item->quantity }}
                                    </span>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="font-serif font-bold text-plum block">
                                    ₹{{ number_format((float)$item->total_price, 2) }}
                                </span>
                                <span class="text-[10px] text-plum/50 font-mono">
                                    ₹{{ number_format((float)$item->unit_price, 2) }} each
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Order Footer: Billed By / Payment / Total --}}
                <div class="pt-3 border-t border-plum/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                    <div class="text-plum/60 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px]">
                        <span>Payment: <strong class="text-plum uppercase font-semibold">{{ $order->payment_method }}</strong></span>
                        <span>•</span>
                        <span>Payment Status: <strong class="text-emerald-700 uppercase font-semibold">{{ $order->payment_status }}</strong></span>
                        @if($order->cashier_name)
                            <span>•</span>
                            <span>Billed by Cashier: <strong class="text-plum font-semibold">{{ $order->cashier_name }}</strong></span>
                        @endif
                    </div>

                    <div class="flex items-center space-x-2 text-right">
                        <span class="text-xs text-plum/60">Grand Total:</span>
                        <span class="text-lg font-serif font-black text-plum">
                            ₹{{ number_format((float)$order->grand_total, 2) }}
                        </span>
                    </div>
                </div>

            </div>
        @empty
            <div class="glass-card rounded-3xl p-12 text-center text-plum/50 space-y-3 bg-white/80 border border-white">
                <div class="w-16 h-16 rounded-full bg-primary-50 text-primary mx-auto flex items-center justify-center text-2xl">
                    🛍️
                </div>
                <h3 class="font-serif font-bold text-base text-plum">Aapka koi garment purchase record nahi hai</h3>
                <p class="text-xs max-w-md mx-auto">
                    Jab aap showroom POS counter par apne phone/account se khareedari karenge ya online store se clothes order karenge, wo orders yahan dikhenge.
                </p>
                <div class="pt-2">
                    <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold shadow-md hover:bg-primary-700 transition">
                        <span>Visit Store &amp; Shop</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>
        @endforelse

        @if($orders->hasPages())
            <div class="pt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
