@extends('layouts.admin')

@section('title', 'GST & Tax Compliance Report - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-800">
                    Indian GST Compliance
                </span>
                <span class="text-xs text-plum/50 font-medium">Bombay Fashion Amritsar (Punjab GST)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-plum mt-1">
                GST &amp; Invoicing Tax Report
            </h1>
            <p class="text-xs text-plum/70 mt-1">
                Summary of 5% Indian apparel GST (CGST 2.5% + SGST 2.5%) for monthly CA filing and tax audits.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('admin.reports.export-tax', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold bg-primary text-white shadow hover:bg-primary-700 transition-all flex items-center gap-1.5 self-start sm:self-auto"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export GST CSV for CA
            </a>
        </div>
    </div>

    {{-- Date Filter Bar --}}
    <div class="glass-card rounded-2xl p-4 sm:p-5 border border-white/80 bg-white/80 shadow-xs">
        <form method="GET" action="{{ route('admin.reports.tax') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end text-xs">
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
                <button type="submit" class="w-full py-2.5 rounded-xl font-bold bg-primary text-white hover:bg-primary-700 shadow-xs transition-all">
                    Generate Tax Report
                </button>
            </div>
        </form>
    </div>

    {{-- Metrics Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Net Taxable Value</span>
            <span class="text-2xl sm:text-3xl font-serif font-bold text-plum">₹{{ number_format($netTaxable, 2) }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">Excluding discounts</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Total GST Collected (5%)</span>
            <span class="text-2xl sm:text-3xl font-serif font-bold text-primary">₹{{ number_format($totalTax, 2) }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">CGST + SGST total</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">CGST (2.5%)</span>
            <span class="text-2xl sm:text-3xl font-serif font-bold text-indigo-700">₹{{ number_format($cgst, 2) }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">Central Government share</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">SGST (2.5%)</span>
            <span class="text-2xl sm:text-3xl font-serif font-bold text-emerald-700">₹{{ number_format($sgst, 2) }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">Punjab State share</span>
        </div>
    </div>

    {{-- Invoice Table --}}
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/90 shadow-sm">
        <h2 class="text-lg font-serif font-bold text-plum pb-3 border-b border-plum/10 mb-4">
            Invoices Breakdown
        </h2>

        @if($orders->isEmpty())
            <div class="py-10 text-center text-xs text-plum/50 italic">
                No paid orders recorded in this date range.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3">Invoice #</th>
                            <th class="py-3 px-3">Date</th>
                            <th class="py-3 px-3">Customer</th>
                            <th class="py-3 px-3 text-right">Taxable Subtotal</th>
                            <th class="py-3 px-3 text-right">CGST (2.5%)</th>
                            <th class="py-3 px-3 text-right">SGST (2.5%)</th>
                            <th class="py-3 px-3 text-right">Total GST</th>
                            <th class="py-3 px-3 text-right">Invoice Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($orders as $ord)
                            @php
                                $ordNetTaxable = max(0, $ord->subtotal - $ord->discount_amount);
                                $ordCgst = round($ord->tax_amount / 2, 2);
                                $ordSgst = round($ord->tax_amount / 2, 2);
                            @endphp
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3.5 px-3 font-mono font-bold text-plum">
                                    <a href="{{ route('admin.orders.show', $ord->id) }}" class="hover:underline text-primary">
                                        {{ $ord->order_number }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-3 font-mono text-plum/70">
                                    {{ $ord->created_at->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-3 font-semibold text-plum">
                                    {{ $ord->customer_name }}
                                </td>
                                <td class="py-3.5 px-3 text-right font-medium text-plum/80">
                                    ₹{{ number_format($ordNetTaxable, 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-right font-mono text-indigo-700">
                                    ₹{{ number_format($ordCgst, 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-right font-mono text-emerald-700">
                                    ₹{{ number_format($ordSgst, 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-right font-mono font-bold text-primary">
                                    ₹{{ number_format($ord->tax_amount, 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-right font-serif font-bold text-plum">
                                    ₹{{ number_format($ord->grand_total, 2) }}
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
