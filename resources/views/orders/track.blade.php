@extends('layouts.app')

@section('title', 'Track Your Order - Bombay Fashion Amritsar')

@section('content')
<div class="py-12 bg-festive-ivory/50 min-h-[65vh]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        
        <div class="text-center mb-8">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-festive-blush text-festive-rose mb-3">
                Live Shipment Tracking
            </span>
            <h1 class="text-3xl font-serif font-bold text-festive-plum mb-2">Track Your Order</h1>
            <p class="text-xs text-festive-plum/70">Enter your order number and registered phone number to check current packing and delivery status.</p>
        </div>

        {{-- Tracking Input Form --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/90 shadow-md mb-8">
            <form method="GET" action="{{ route('orders.track') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="block text-xs font-semibold text-festive-plum mb-1">Order Number *</label>
                    <input 
                        type="text" 
                        name="order_number" 
                        value="{{ request('order_number', $orderNumber ?? '') }}" 
                        placeholder="e.g. BF-20261024-XXXX" 
                        required
                        class="w-full text-xs font-mono rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                    >
                </div>
                <div class="sm:col-span-4">
                    <label class="block text-xs font-semibold text-festive-plum mb-1">Phone Number (optional)</label>
                    <input 
                        type="tel" 
                        name="phone" 
                        value="{{ request('phone', request('contact', $phone ?? '')) }}" 
                        placeholder="10-digit mobile" 
                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                    >
                </div>
                <div class="sm:col-span-3">
                    <button type="submit" class="w-full py-3 px-4 rounded-xl text-xs font-bold bg-festive-rose text-white shadow hover:bg-festive-rose/90 transition-all text-center">
                        Track Order
                    </button>
                </div>
            </form>
        </div>

        {{-- Results --}}
        @if($order)
            <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/90 shadow-lg space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-festive-plum/10 gap-3">
                    <div>
                        <span class="text-xs text-festive-plum/50">Order Number</span>
                        <h2 class="text-xl font-mono font-bold text-festive-plum">#{{ $order->order_number }}</h2>
                        <span class="text-[11px] text-festive-plum/60">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</span>
                    </div>

                    <div>
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-festive-blush text-festive-rose border border-festive-rose/30">
                            Status: {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>

                {{-- Status Tracker Steps --}}
                @php
                    $steps = ['pending', 'confirmed', 'packed', 'shipped', 'delivered'];
                    $curIdx = array_search($order->status, $steps);
                @endphp
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-festive-plum/70 mb-4">Shipment Progress</h3>
                    <div class="grid grid-cols-5 gap-2 text-center text-xs">
                        @foreach($steps as $i => $s)
                            @php
                                $done = $curIdx !== false && $i <= $curIdx;
                            @endphp
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs mb-1 {{ $done ? 'bg-festive-rose text-white ring-2 ring-festive-rose/30' : 'bg-festive-plum/10 text-festive-plum/40' }}">
                                    {{ $done ? '✓' : ($i+1) }}
                                </div>
                                <span class="font-bold text-[11px] {{ $done ? 'text-festive-plum' : 'text-festive-plum/40' }}">{{ ucfirst($s) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-festive-plum/10 flex justify-between items-center text-xs">
                    <div>
                        <span class="text-festive-plum/60">Delivery to:</span>
                        <span class="font-bold text-festive-plum ml-1">{{ $order->shipping_city }}, {{ $order->shipping_state }}</span>
                    </div>
                    <a href="{{ route('orders.invoice', $order->order_number) }}" class="font-bold text-festive-rose hover:underline">
                        Download PDF Invoice &rarr;
                    </a>
                </div>
            </div>
        @elseif(!empty($orderNumber))
            <div class="glass-card rounded-3xl p-8 text-center border border-white/80 bg-white/90 shadow">
                <p class="text-sm text-festive-plum font-semibold">No order found with number "{{ $orderNumber }}".</p>
                <p class="text-xs text-festive-plum/60 mt-1">Please double-check the order number from your invoice or confirmation message.</p>
            </div>
        @endif

    </div>
</div>
@endsection
