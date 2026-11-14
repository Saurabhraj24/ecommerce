@extends('layouts.admin')

@section('title', 'Sales & Revenue Reports - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-plum">Sales & Financial Reports</h1>
            <p class="text-xs text-plum/70 mt-1">Audit store performance, channel breakdown (Razorpay vs COD), and export CSV.</p>
        </div>
        <a 
            href="{{ route('admin.reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold bg-primary text-white shadow hover:bg-primary-700 transition-all flex items-center gap-1.5 self-start sm:self-auto"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Export Period to CSV
        </a>
    </div>

    {{-- Date Filter Bar --}}
    <div class="glass-card rounded-2xl p-4 sm:p-5 border border-white/80 bg-white/80 shadow-sm">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end text-xs">
            <div>
                <label class="block font-semibold text-plum mb-1">From Date</label>
                <input 
                    type="date" 
                    name="start_date" 
                    value="{{ $startDate }}" 
                    class="w-full rounded-xl border-plum/20 bg-white/70 p-2.5 text-plum focus:ring-primary"
                >
            </div>
            <div>
                <label class="block font-semibold text-plum mb-1">To Date</label>
                <input 
                    type="date" 
                    name="end_date" 
                    value="{{ $endDate }}" 
                    class="w-full rounded-xl border-plum/20 bg-white/70 p-2.5 text-plum focus:ring-primary"
                >
            </div>
            <div>
                <button type="submit" class="w-full py-2.5 rounded-xl font-bold bg-primary text-white hover:bg-primary-700 shadow-sm transition-all">
                    Generate Report
                </button>
            </div>
        </form>
    </div>

    {{-- Metrics Cards Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/80 shadow-sm">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Total Net Revenue</span>
            <span class="text-2xl sm:text-3xl font-serif font-bold text-primary">₹{{ number_format($totalRevenue, 2) }}</span>
            <span class="text-[11px] text-plum/50 block mt-1">{{ $totalOrders }} total orders placed</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/80 shadow-sm">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Online (Razorpay)</span>
            <span class="text-2xl sm:text-3xl font-serif font-bold text-emerald-700">₹{{ number_format($razorpaySales, 2) }}</span>
            <span class="text-[11px] text-plum/50 block mt-1">UPI, Cards & Netbanking</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/80 shadow-sm">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Cash on Delivery</span>
            <span class="text-2xl sm:text-3xl font-serif font-bold text-gold-700">₹{{ number_format($codSales, 2) }}</span>
            <span class="text-[11px] text-plum/50 block mt-1">Paid on delivery</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/80 shadow-sm">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Delivered Orders</span>
            <span class="text-2xl sm:text-3xl font-serif font-bold text-plum">{{ $deliveredCount }}</span>
            <span class="text-[11px] text-plum/50 block mt-1">Fulfilled successfully</span>
        </div>
    </div>

    {{-- Top Sold Products in Range --}}
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-md">
        <h2 class="text-lg font-serif font-bold text-plum pb-3 border-b border-plum/10 mb-4">
            Top Selling Products During Selected Period
        </h2>

        @if($topProducts->isEmpty())
            <div class="py-8 text-center text-plum/50 text-xs italic">
                No product sales recorded in the selected date range.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="py-2.5">Product Name</th>
                            <th class="py-2.5">SKU</th>
                            <th class="py-2.5 text-center">Units Sold</th>
                            <th class="py-2.5 text-right">Revenue Generated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($topProducts as $tp)
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3 font-bold text-plum">{{ $tp->product_name }}</td>
                                <td class="py-3 font-mono text-plum/60">{{ $tp->product_sku }}</td>
                                <td class="py-3 text-center font-bold font-mono">{{ $tp->total_qty }}</td>
                                <td class="py-3 text-right font-serif font-bold text-primary">₹{{ number_format($tp->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
