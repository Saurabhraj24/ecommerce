@extends('layouts.admin')

@section('title', 'Showroom POS Cash Register History - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                    Daily Cash Register
                </span>
                <span class="text-xs text-plum/50 font-medium">Bombay Fashion Amritsar</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-plum mt-1">
                Showroom Counter Sales History
            </h1>
            <p class="text-xs text-plum/70 mt-1">
                Audit walk-in customer receipts, daily cash drawer collections vs UPI / QR counter payments.
            </p>
        </div>
        <a 
            href="{{ route('admin.pos.index') }}" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold bg-primary text-white shadow-md hover:bg-primary-700 transition-all self-start sm:self-auto"
        >
            &larr; Open POS Billing Terminal
        </a>
    </div>

    {{-- Date Filter Bar --}}
    <div class="glass-card rounded-2xl p-4 sm:p-5 border border-white/80 bg-white/80 shadow-xs">
        <form method="GET" action="{{ route('admin.pos.history') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end text-xs">
            <div>
                <label class="block font-semibold text-plum mb-1">Select Register Date</label>
                <input 
                    type="date" 
                    name="date" 
                    value="{{ $date }}" 
                    class="w-full rounded-xl border-plum/20 bg-white/70 p-2.5 text-plum focus:ring-primary"
                >
            </div>
            <div>
                <button type="submit" class="w-full py-2.5 rounded-xl font-bold bg-primary text-white hover:bg-primary-700 shadow-xs transition-all">
                    Load Counter Register
                </button>
            </div>
        </form>
    </div>

    {{-- Metrics Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Total Counter Turnover</span>
            <span class="text-2xl sm:text-3xl font-serif font-bold text-primary">₹{{ number_format($totalSales, 2) }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">{{ $totalOrders }} walk-in customers</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Cash in Drawer</span>
            <span class="text-2xl sm:text-3xl font-serif font-bold text-emerald-700">₹{{ number_format($cashSales, 2) }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">Physical cash tendered</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Counter UPI / QR</span>
            <span class="text-2xl sm:text-3xl font-serif font-bold text-indigo-700">₹{{ number_format($upiSales, 2) }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">Direct bank settlements</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Card Terminal Swipe</span>
            <span class="text-2xl sm:text-3xl font-serif font-bold text-gold-700">₹{{ number_format($cardSales, 2) }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">POS debit/credit card</span>
        </div>
    </div>

    {{-- Counter Orders List --}}
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/90 shadow-sm">
        <h2 class="text-lg font-serif font-bold text-plum pb-3 border-b border-plum/10 mb-4">
            Showroom Counter Receipts ({{ Carbon\Carbon::parse($date)->format('d M Y') }})
        </h2>

        @if($orders->isEmpty())
            <div class="py-10 text-center text-xs text-plum/50 italic">
                No walk-in POS orders recorded on this date.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3">Receipt / Bill #</th>
                            <th class="py-3 px-3">Time</th>
                            <th class="py-3 px-3">Customer</th>
                            <th class="py-3 px-3">Billed By (Cashier)</th>
                            <th class="py-3 px-3">Items Purchased</th>
                            <th class="py-3 px-3">Payment Mode</th>
                            <th class="py-3 px-3 text-right">Bill Total</th>
                            <th class="py-3 px-3 text-center">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($orders as $ord)
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3 px-3 font-mono font-bold text-plum">
                                    {{ $ord->order_number }}
                                </td>
                                <td class="py-3 px-3 text-plum/70 font-mono">
                                    {{ $ord->created_at->format('h:i A') }}
                                </td>
                                <td class="py-3 px-3 font-bold text-plum">
                                    {{ $ord->customer_name }}
                                    <span class="text-[10px] text-plum/60 block">{{ $ord->customer_phone }}</span>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-[10px]">
                                            {{ strtoupper(substr($ord->cashier_name ?? $ord->billedBy?->name ?? 'A', 0, 1)) }}
                                        </span>
                                        <div>
                                            <div class="font-bold text-plum text-[11px] leading-tight">
                                                {{ $ord->cashier_name ?? $ord->billedBy?->name ?? 'Admin Staff' }}
                                            </div>
                                            <div class="text-[10px] text-plum/60 leading-tight">
                                                {{ $ord->cashier_email ?? $ord->billedBy?->email ?? 'walkin@bombayfashion.com' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-plum/5 text-plum">
                                        {{ $ord->items->sum('quantity') }} items
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $ord->payment_method === 'cash' ? 'bg-emerald-100 text-emerald-800' : 'bg-indigo-100 text-indigo-800' }}">
                                        {{ $ord->payment_method }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right font-serif font-bold text-primary text-sm">
                                    ₹{{ number_format($ord->grand_total, 2) }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <a 
                                        href="{{ route('admin.pos.receipt', $ord->id) }}" 
                                        target="_blank" 
                                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-plum/10 text-plum hover:bg-plum hover:text-white transition"
                                    >
                                        🖨️ Reprint
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
