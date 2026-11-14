@extends('layouts.admin')

@section('title', 'Manage Order #' . $order->order_number . ' - Bombay Fashion Admin')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    
    {{-- Top Header & Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <nav class="flex text-xs text-plum/60 mb-1">
                <a href="{{ route('admin.orders.index') }}" class="hover:text-primary">&larr; Back to Orders</a>
            </nav>
            <h1 class="text-2xl font-serif font-bold text-plum flex items-center gap-3">
                <span>Order #{{ $order->order_number }}</span>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-primary-100 text-primary-800">
                    {{ ucfirst($order->order_status) }}
                </span>
            </h1>
            <p class="text-xs text-plum/70 mt-1">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a 
                href="{{ route('admin.orders.invoice', $order->id) }}" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold bg-primary text-white hover:bg-primary-700 shadow-sm transition-all flex items-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Download Invoice (PDF)
            </a>

            <a 
                href="{{ route('admin.orders.packingSlip', $order->id) }}" 
                class="px-3.5 py-2 rounded-xl text-xs font-semibold border border-plum/20 bg-white text-plum hover:bg-plum/5 shadow-sm transition-all flex items-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Packing Slip
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Left: Status & Fulfillment Actions (5 cols) --}}
        <div class="lg:col-span-5 space-y-6">
            
            {{-- Order Status Updater --}}
            <div class="glass-card rounded-3xl p-6 border border-white/80 bg-white/85 shadow-sm space-y-4">
                <h2 class="text-sm font-serif font-bold text-plum pb-3 border-b border-plum/10">
                    Update Order Status
                </h2>

                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-semibold text-plum mb-1">Status</label>
                        <select name="order_status" class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary font-bold">
                            @foreach(['pending' => 'Pending Confirmation', 'confirmed' => 'Confirmed by Store', 'packed' => 'Packed ready for dispatch', 'shipped' => 'Shipped / Out for Delivery', 'delivered' => 'Delivered to Customer', 'cancelled' => 'Cancelled'] as $stKey => $stLbl)
                                <option value="{{ $stKey }}" {{ $order->order_status == $stKey ? 'selected' : '' }}>
                                    {{ $stLbl }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-plum mb-1">Courier / Delivery Staff</label>
                            <input 
                                type="text" 
                                name="courier_name" 
                                value="{{ old('courier_name', $order->courier_name) }}" 
                                placeholder="e.g. Store Rider / DTDC"
                                class="w-full rounded-xl border-plum/20 bg-white p-2 text-plum"
                            >
                        </div>
                        <div>
                            <label class="block font-semibold text-plum mb-1">Tracking Number</label>
                            <input 
                                type="text" 
                                name="tracking_number" 
                                value="{{ old('tracking_number', $order->tracking_number) }}" 
                                placeholder="Tracking / Waybill"
                                class="w-full font-mono rounded-xl border-plum/20 bg-white p-2 text-plum"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-plum mb-1">Internal Note (optional)</label>
                        <textarea 
                            name="notes" 
                            rows="2" 
                            placeholder="Add notes for staff..."
                            class="w-full rounded-xl border-plum/20 bg-white p-2 text-plum"
                        >{{ old('notes', $order->notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-xl font-bold bg-primary text-white hover:bg-primary-700 shadow-sm transition-all">
                        Update Status
                    </button>
                </form>
            </div>

            {{-- Cash on Delivery Confirmation Card --}}
            @if($order->payment_method === 'cod')
                <div class="glass-card rounded-3xl p-6 border border-white/80 bg-white/85 shadow-sm space-y-3">
                    <h2 class="text-sm font-serif font-bold text-plum pb-2 border-b border-plum/10 flex items-center justify-between">
                        <span>Cash on Delivery</span>
                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </h2>

                    @if($order->payment_status !== 'paid')
                        <p class="text-xs text-plum/70">
                            Collect ₹{{ number_format($order->grand_total, 2) }} in cash from customer upon delivery.
                        </p>
                        <form action="{{ route('admin.orders.markCodPaid', $order->id) }}" method="POST" onsubmit="return confirm('Confirm that cash payment has been received in full?')">
                            @csrf
                            <button type="submit" class="w-full py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow transition-all">
                                ✓ Mark COD as Paid on Delivery
                            </button>
                        </form>
                    @else
                        <div class="text-xs text-emerald-800 bg-emerald-50 p-3 rounded-xl border border-emerald-200">
                            ✓ Payment of ₹{{ number_format($order->grand_total, 2) }} was marked as paid on delivery.
                        </div>
                    @endif
                </div>
            @endif

            {{-- Customer Return Request (if requested) --}}
            @if($order->return_status !== 'none')
                <div class="glass-card rounded-3xl p-6 border border-white/80 bg-white/85 shadow-sm space-y-3">
                    <h2 class="text-sm font-serif font-bold text-plum pb-2 border-b border-plum/10">
                        Customer Return / Exchange Request
                    </h2>
                    <div class="text-xs text-plum/80 bg-plum/5 p-3 rounded-xl">
                        <strong>Reason:</strong> {{ $order->return_reason ?? 'Not specified' }}
                    </div>

                    <form action="{{ route('admin.orders.updateReturn', $order->id) }}" method="POST" class="space-y-3 text-xs">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block font-semibold text-plum mb-1">Return Decision</label>
                            <select name="return_status" class="w-full rounded-xl border-plum/20 bg-white p-2 text-plum">
                                <option value="approved" {{ $order->return_status == 'approved' ? 'selected' : '' }}>Approve Return / Exchange</option>
                                <option value="rejected" {{ $order->return_status == 'rejected' ? 'selected' : '' }}>Reject Return Request</option>
                                <option value="completed" {{ $order->return_status == 'completed' ? 'selected' : '' }}>Complete & Restock Inventory</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full py-2 rounded-xl font-bold bg-primary text-white hover:bg-primary-700">
                            Save Return Decision
                        </button>
                    </form>
                </div>
            @endif

            {{-- Billed By / POS Counter Cashier Info --}}
            @if($order->isPos())
                <div class="glass-card rounded-3xl p-5 border border-emerald-200 bg-emerald-50/60 shadow-sm text-xs space-y-2">
                    <div class="flex items-center justify-between pb-2 border-b border-emerald-200">
                        <span class="font-serif font-bold text-emerald-950 text-sm flex items-center gap-1.5">
                            <span>🏬</span>
                            <span>Showroom Counter Sale</span>
                        </span>
                        <a 
                            href="{{ route('admin.pos.receipt', $order->id) }}" 
                            target="_blank"
                            class="px-2.5 py-1 rounded-lg bg-emerald-700 text-white font-bold text-[10px] hover:bg-emerald-800 transition"
                        >
                            Thermal Receipt &rarr;
                        </a>
                    </div>
                    <div>
                        <span class="text-emerald-800/70 block text-[10px] uppercase font-bold">Billed By Staff / Admin</span>
                        <p class="font-bold text-plum text-sm">
                            {{ $order->cashier_name ?: ($order->billedBy?->name ?? 'Showroom Cashier') }}
                        </p>
                        @if($order->cashier_email)
                            <p class="text-[11px] text-plum/70 font-mono">{{ $order->cashier_email }}</p>
                        @endif
                        @if($order->cashier_role)
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-white border border-emerald-200 text-emerald-900">
                                Role: {{ $order->cashier_role }}
                            </span>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Customer Information Card --}}
            <div class="glass-card rounded-3xl p-6 border border-white/80 bg-white/85 shadow-sm text-xs space-y-2.5">
                <h3 class="font-serif font-bold text-plum text-sm pb-2 border-b border-plum/10">
                    Customer &amp; Delivery Address
                </h3>
                <div>
                    <span class="text-plum/50 block text-[11px]">Customer Name</span>
                    <span class="font-bold text-plum text-sm">{{ $order->shipping_name }}</span>
                </div>
                <div>
                    <span class="text-plum/50 block text-[11px]">Contact</span>
                    <span class="text-plum">{{ $order->shipping_phone }}</span> • 
                    <span class="text-plum">{{ $order->shipping_email }}</span>
                </div>
                <div>
                    <span class="text-plum/50 block text-[11px]">Shipping Destination</span>
                    <p class="text-plum/80 leading-relaxed mt-0.5">
                        {{ $order->shipping_address_line1 }}<br>
                        @if($order->shipping_address_line2){{ $order->shipping_address_line2 }}<br>@endif
                        {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}
                    </p>
                </div>
            </div>

        </div>

        {{-- Right: Items Table & Price Summary (7 cols) --}}
        <div class="lg:col-span-7 space-y-6">
            
            <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-md">
                <h2 class="text-base font-serif font-bold text-plum pb-3 border-b border-plum/10 mb-4">
                    Ordered Clothing & Items
                </h2>

                <div class="divide-y divide-plum/10">
                    @foreach($order->items as $item)
                        <div class="py-4 flex items-center justify-between gap-4 text-xs">
                            <div class="flex-1">
                                <h3 class="font-bold text-plum text-sm">
                                    {{ $item->product_name }}
                                </h3>
                                <div class="text-[11px] text-plum/60 mt-0.5">
                                    SKU: <span class="font-mono">{{ $item->product_sku }}</span>
                                    @if($item->variant_details)
                                        <span class="ml-2 font-medium text-primary">• {{ $item->variant_details }}</span>
                                    @endif
                                </div>
                                <span class="text-plum/70 block mt-1">
                                    Qty: <strong>{{ $item->quantity }}</strong> × ₹{{ number_format($item->unit_price, 2) }}
                                </span>
                            </div>

                            <div class="font-serif font-bold text-sm text-plum text-right">
                                ₹{{ number_format($item->total_price, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Price Math Summary --}}
                <div class="pt-6 border-t border-plum/10 flex flex-col items-end gap-2 text-xs text-plum/80">
                    <div class="flex justify-between w-64">
                        <span>Items Subtotal:</span>
                        <span class="font-bold text-plum">₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between w-64 text-emerald-700">
                            <span>Coupon Discount:</span>
                            <span class="font-bold">-₹{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between w-64">
                        <span>Tax (GST):</span>
                        <span class="font-bold text-plum">₹{{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between w-64">
                        <span>Shipping Fee:</span>
                        <span class="font-bold text-plum">₹{{ number_format($order->shipping_charge, 2) }}</span>
                    </div>
                    <div class="flex justify-between w-64 pt-2 border-t border-plum/10 text-sm font-bold text-primary">
                        <span>Total Paid / Payable:</span>
                        <span class="font-serif text-lg">₹{{ number_format($order->grand_total, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Payments Record Card --}}
            <div class="glass-card rounded-3xl p-6 border border-white/80 bg-white/85 shadow-sm text-xs">
                <h3 class="font-serif font-bold text-plum text-sm pb-2 border-b border-plum/10 mb-3">
                    Payment Transactions Log
                </h3>

                @if($order->payments->isEmpty())
                    <p class="text-plum/50 italic">No payment transactions recorded yet.</p>
                @else
                    <div class="space-y-2">
                        @foreach($order->payments as $pmt)
                            <div class="p-3 rounded-xl bg-plum/5 flex items-center justify-between">
                                <div>
                                    <span class="font-bold uppercase text-plum">{{ $pmt->payment_method }}</span>
                                    <span class="ml-2 font-mono text-[10px] text-plum/60">{{ $pmt->razorpay_payment_id ?? 'COD' }}</span>
                                    <span class="block text-[10px] text-plum/40">{{ $pmt->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-serif font-bold text-plum">₹{{ number_format($pmt->amount, 2) }}</span>
                                    <span class="block text-[10px] font-bold uppercase text-emerald-700">{{ $pmt->status }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
