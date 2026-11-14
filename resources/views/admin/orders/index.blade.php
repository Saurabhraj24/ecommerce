@extends('layouts.admin')

@section('title', 'Manage Orders - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-plum">Customer Orders</h1>
            <p class="text-xs text-plum/70 mt-1">Track orders, pack shipments, mark cash on delivery, and handle customer returns.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="glass-card rounded-2xl p-4 sm:p-5 border border-white/80 bg-white/80 shadow-sm">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end text-xs">
            
            <div class="lg:col-span-2">
                <label class="block font-semibold text-plum mb-1">Search Orders</label>
                <input 
                    type="text" 
                    name="q" 
                    value="{{ request('q') }}" 
                    placeholder="Order #, name, phone, email..." 
                    class="w-full rounded-xl border-plum/20 bg-white/70 p-2.5 text-plum focus:ring-primary"
                >
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Order Status</label>
                <select name="status" class="w-full rounded-xl border-plum/20 bg-white/70 p-2.5 text-plum focus:ring-primary">
                    <option value="">All Statuses</option>
                    @foreach(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'packed' => 'Packed', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled', 'returned' => 'Returned'] as $val => $lbl)
                        <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Payment Method</label>
                <select name="payment_method" class="w-full rounded-xl border-plum/20 bg-white/70 p-2.5 text-plum focus:ring-primary">
                    <option value="">All Modes</option>
                    <option value="razorpay" {{ request('payment_method') == 'razorpay' ? 'selected' : '' }}>Razorpay Online</option>
                    <option value="cod" {{ request('payment_method') == 'cod' ? 'selected' : '' }}>Cash on Delivery</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Payment Status</label>
                <select name="payment_status" class="w-full rounded-xl border-plum/20 bg-white/70 p-2.5 text-plum focus:ring-primary">
                    <option value="">All</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl font-bold bg-primary text-white hover:bg-primary-700 shadow-sm text-center">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'status', 'payment_method', 'payment_status', 'date_from', 'date_to']))
                    <a href="{{ route('admin.orders.index') }}" class="py-2.5 px-3 rounded-xl border border-plum/20 text-plum hover:bg-plum/5 text-center">
                        Clear
                    </a>
                @endif
            </div>

        </form>
    </div>

    {{-- Orders Data Table --}}
    <div class="glass-card rounded-3xl border border-white/80 bg-white/85 shadow-md overflow-hidden">
        @if($orders->isEmpty())
            {{-- Clean Empty State - STRICT NO FAKE DATA --}}
            <div class="p-12 text-center max-w-md mx-auto">
                <div class="w-16 h-16 rounded-full bg-primary-100 text-primary-800 mx-auto flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <h3 class="text-lg font-serif font-bold text-plum mb-1">No Orders Found</h3>
                <p class="text-xs text-plum/60 mb-4">
                    @if(request()->query())
                        No orders match your filter criteria. Try resetting filters.
                    @else
                        Orders placed by customers will appear in this pipeline for packing and fulfillment.
                    @endif
                </p>
                @if(request()->query())
                    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-primary text-white shadow">
                        Clear Filters
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px] bg-plum/5">
                            <th class="py-3 px-4">Order #</th>
                            <th class="py-3 px-4">Customer</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Payment</th>
                            <th class="py-3 px-4">Status</th>
                            @if($canViewRevenue)
                                <th class="py-3 px-4 text-right">Total (₹)</th>
                            @endif
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($orders as $ord)
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3 px-4 font-mono font-bold text-plum">
                                    <a href="{{ route('admin.orders.show', $ord->id) }}" class="hover:text-primary">
                                        #{{ $ord->order_number }}
                                    </a>
                                    @if($ord->isPos() || $ord->cashier_name)
                                        <div class="mt-1">
                                            <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">
                                                ⚡ POS Counter
                                            </span>
                                            @if($ord->cashier_name)
                                                <span class="block text-[10px] text-plum/60 font-sans font-normal mt-0.5">
                                                    Billed by: <strong class="text-plum">{{ $ord->cashier_name }}</strong>
                                                    @if(auth()->user()->isAdmin() && $ord->cashier_email)
                                                        <span class="text-[9px] text-plum/50">({{ $ord->cashier_email }})</span>
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <td class="py-3 px-4 text-plum">
                                    <span class="font-bold block">{{ $ord->shipping_name }}</span>
                                    <span class="text-[10px] text-plum/60">{{ $ord->shipping_phone }}</span>
                                </td>

                                <td class="py-3 px-4 text-plum/70 text-[11px]">
                                    {{ $ord->created_at->format('d M Y') }}
                                    <span class="block text-[10px] text-plum/40">{{ $ord->created_at->format('h:i A') }}</span>
                                </td>

                                <td class="py-3 px-4">
                                    <span class="uppercase font-mono text-[10px] font-bold text-plum block">
                                        {{ $ord->payment_method }}
                                    </span>
                                    <span class="text-[10px] font-semibold {{ $ord->payment_status === 'paid' ? 'text-emerald-700' : 'text-amber-700' }}">
                                        {{ ucfirst($ord->payment_status) }}
                                    </span>
                                </td>

                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-primary-100 text-primary-800">
                                        {{ ucfirst($ord->order_status) }}
                                    </span>
                                    @if($ord->return_status !== 'none')
                                        <span class="block text-[9px] font-bold text-rose-600 mt-0.5">Return: {{ ucfirst($ord->return_status) }}</span>
                                    @endif
                                </td>

                                @if($canViewRevenue)
                                    <td class="py-3 px-4 text-right font-serif font-bold text-plum">
                                        ₹{{ number_format($ord->grand_total, 2) }}
                                    </td>
                                @endif

                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a 
                                            href="{{ route('admin.orders.show', $ord->id) }}" 
                                            class="px-2.5 py-1 rounded-lg bg-primary text-white font-bold hover:bg-primary-700 transition-colors text-[11px]"
                                        >
                                            View
                                        </a>
                                        <a 
                                            href="{{ route('admin.orders.packingSlip', $ord->id) }}" 
                                            class="p-1.5 rounded-lg border border-plum/20 text-plum hover:bg-plum/5"
                                            title="Print Packing Slip"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-plum/10">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
