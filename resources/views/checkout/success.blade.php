@extends('layouts.app')

@section('title', 'Order Confirmed! - Bombay Fashion Amritsar')

@section('content')
<div class="py-12 bg-festive-ivory/50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        
        <div class="glass-card rounded-3xl p-8 sm:p-10 border border-white/80 bg-white/90 shadow-xl text-center mb-8">
            {{-- Checkmark icon --}}
            <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center mb-5 shadow-inner">
                <svg class="w-10 h-10 stroke-current" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 mb-3">
                Order Confirmed
            </span>

            <h1 class="text-3xl font-serif font-bold text-festive-plum mb-2">
                Thank You For Your Order!
            </h1>
            <p class="text-sm text-festive-plum/70 max-w-md mx-auto mb-6">
                Your order has been received by Bombay Fashion (Mahila College Road, Amritsar) and is being prepared with care.
            </p>

            {{-- Order Meta Bar --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 rounded-2xl bg-festive-blush/30 border border-festive-plum/5 text-left mb-6 text-xs">
                <div>
                    <span class="text-festive-plum/50 text-[11px] block">Order Number</span>
                    <span class="font-mono font-bold text-festive-plum">#{{ $order->order_number }}</span>
                </div>
                <div>
                    <span class="text-festive-plum/50 text-[11px] block">Order Date</span>
                    <span class="font-medium text-festive-plum">{{ $order->created_at->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="text-festive-plum/50 text-[11px] block">Payment Mode</span>
                    <span class="font-semibold text-festive-plum uppercase">{{ $order->payment_method }}</span>
                </div>
                <div>
                    <span class="text-festive-plum/50 text-[11px] block">Total Amount</span>
                    <span class="font-serif font-bold text-festive-rose">₹{{ number_format($order->grand_total, 2) }}</span>
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a 
                    href="{{ route('orders.invoice', $order->order_number) }}" 
                    class="w-full sm:w-auto px-6 py-3 rounded-xl font-bold text-xs bg-festive-rose text-white shadow-md hover:bg-festive-rose/90 transition-all flex items-center justify-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Download Tax Invoice (PDF)</span>
                </a>

                @auth
                    <a 
                        href="{{ route('orders.show', $order->order_number) }}" 
                        class="w-full sm:w-auto px-6 py-3 rounded-xl font-medium text-xs border border-festive-plum/20 text-festive-plum hover:bg-festive-blush transition-all"
                    >
                        View Order Details
                    </a>
                @else
                    <a 
                        href="{{ route('orders.track', ['order_number' => $order->order_number]) }}" 
                        class="w-full sm:w-auto px-6 py-3 rounded-xl font-medium text-xs border border-festive-plum/20 text-festive-plum hover:bg-festive-blush transition-all"
                    >
                        Track Status
                    </a>
                @endauth
            </div>
        </div>

        {{-- Shipping and Items breakdown --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/80 shadow-md space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-festive-plum/10 text-xs text-festive-plum/80">
                <div>
                    <h3 class="font-serif font-bold text-festive-plum text-sm mb-2">Delivery Address</h3>
                    <p class="leading-relaxed">
                        <strong class="text-festive-plum">{{ $order->shipping_name }}</strong><br>
                        {{ $order->shipping_address_line1 }}<br>
                        @if($order->shipping_address_line2){{ $order->shipping_address_line2 }}<br>@endif
                        {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}<br>
                        Phone: {{ $order->shipping_phone }}
                    </p>
                </div>
                <div>
                    <h3 class="font-serif font-bold text-festive-plum text-sm mb-2">Store Pickup / Queries</h3>
                    <p class="leading-relaxed">
                        <strong class="text-festive-plum">Bombay Fashion</strong><br>
                        Mahila College Road, opposite Mahila College,<br>
                        Amritsar, Punjab, India<br>
                        Timings: Open daily until 9:30 PM
                    </p>
                </div>
            </div>

            {{-- Items --}}
            <div>
                <h3 class="font-serif font-bold text-festive-plum text-sm mb-4">Ordered Items</h3>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between text-xs py-2 border-b border-festive-plum/5">
                            <div>
                                <span class="font-bold text-festive-plum">{{ $item->product_name }}</span>
                                @if($item->variant_info)
                                    <span class="text-[11px] text-festive-plum/60 ml-2">({{ $item->variant_info }})</span>
                                @endif
                                <div class="text-[11px] text-festive-plum/50">Qty: {{ $item->quantity }} × ₹{{ number_format($item->unit_price, 2) }}</div>
                            </div>
                            <span class="font-serif font-bold text-festive-plum">₹{{ number_format($item->total_price, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
