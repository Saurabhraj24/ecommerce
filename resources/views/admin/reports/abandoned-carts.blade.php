@extends('layouts.admin')

@section('title', 'Abandoned Cart Recovery - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800">
                    Sales Recovery
                </span>
                <span class="text-xs text-plum/50 font-medium">Bombay Fashion Amritsar</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-plum mt-1">
                Abandoned Cart Recovery
            </h1>
            <p class="text-xs text-plum/70 mt-1">
                Customers who added clothes to cart but left without completing payment. Contact them via WhatsApp to offer a 5% discount and recover lost sales!
            </p>
        </div>
        <a 
            href="{{ route('admin.reports.cloth-sales') }}" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold border border-plum/20 bg-white text-plum hover:bg-plum/5 transition-all self-start sm:self-auto"
        >
            &larr; Back to Cloth Sales
        </a>
    </div>

    {{-- Abandoned Carts Table --}}
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/90 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-plum/10 mb-4">
            <h2 class="text-lg font-serif font-bold text-plum">
                Pending Carts Requiring Follow-up
            </h2>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-amber-50 text-amber-800">
                {{ $abandonedCarts->total() }} Potential Orders
            </span>
        </div>

        @if($abandonedCarts->isEmpty())
            <div class="py-12 text-center text-xs text-plum/50 italic">
                No abandoned carts found! Customers are completing checkouts smoothly.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3">Customer</th>
                            <th class="py-3 px-3">Contact</th>
                            <th class="py-3 px-3">Garments in Cart</th>
                            <th class="py-3 px-3 text-right">Cart Total</th>
                            <th class="py-3 px-3 text-center">Last Active</th>
                            <th class="py-3 px-3 text-center">Recovery Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($abandonedCarts as $cart)
                            @php
                                $total = $cart->items->sum(fn($i) => $i->quantity * $i->unit_price);
                                $phone = $cart->user?->phone ?? '';
                                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                                if ($cleanPhone && strlen($cleanPhone) === 10) {
                                    $cleanPhone = '91' . $cleanPhone;
                                }
                                $firstItemName = $cart->items->first()?->product?->name ?? 'garment';
                                $waMsg = urlencode("Namaste from Bombay Fashion Amritsar! We noticed you left \"{$firstItemName}\" in your cart. Would you like assistance or a special festive discount to complete your order?");
                            @endphp
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3.5 px-3 font-bold text-plum">
                                    {{ $cart->user?->name ?? 'Guest Shopper' }}
                                    <span class="text-[10px] text-plum/50 font-normal block">{{ $cart->session_id ? 'Session #' . substr($cart->session_id, 0, 10) : 'Registered User' }}</span>
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="font-mono text-plum/80 block">{{ $phone ?: 'No phone stored' }}</span>
                                    <span class="text-[10px] text-plum/50">{{ $cart->user?->email }}</span>
                                </td>
                                <td class="py-3.5 px-3">
                                    <div class="space-y-1">
                                        @foreach($cart->items->take(2) as $cItem)
                                            <div class="text-[11px] text-plum">
                                                <span class="font-bold">{{ $cItem->quantity }}x</span> {{ $cItem->product?->name }}
                                                @if($cItem->productVariant)
                                                    <span class="text-[10px] text-plum/50">({{ $cItem->productVariant->size }})</span>
                                                @endif
                                            </div>
                                        @endforeach
                                        @if($cart->items->count() > 2)
                                            <span class="text-[10px] text-primary font-semibold block">+{{ $cart->items->count() - 2 }} more garments</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-right font-serif font-bold text-primary text-sm">
                                    ₹{{ number_format($total, 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-center text-plum/60 font-medium">
                                    {{ $cart->updated_at->diffForHumans() }}
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    @if($cleanPhone)
                                        <a 
                                            href="https://wa.me/{{ $cleanPhone }}?text={{ $waMsg }}" 
                                            target="_blank" 
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-xs transition text-[11px]"
                                        >
                                            <span>💬 WhatsApp</span>
                                        </a>
                                    @else
                                        <span class="text-[10px] text-plum/40 italic">Guest Cart</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $abandonedCarts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
