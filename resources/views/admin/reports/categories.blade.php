@extends('layouts.admin')

@section('title', 'Category & Department Sales Performance - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-primary-100 text-primary-800">
                    Department Analytics
                </span>
                <span class="text-xs text-plum/50 font-medium">Bombay Fashion Amritsar</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-plum mt-1">
                Category Sales Breakdown
            </h1>
            <p class="text-xs text-plum/70 mt-1">
                Analyze which clothing departments (Bridal Sarees, Punjabi Suits, Kurtas, School Uniforms) generate the highest sales volume and profit.
            </p>
        </div>
        <a 
            href="{{ route('admin.reports.cloth-sales') }}" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold border border-plum/20 bg-white text-plum hover:bg-plum/5 transition-all self-start sm:self-auto"
        >
            &larr; Back to Itemized Cloth Sales
        </a>
    </div>

    {{-- Date Filter Bar --}}
    <div class="glass-card rounded-2xl p-4 sm:p-5 border border-white/80 bg-white/80 shadow-xs">
        <form method="GET" action="{{ route('admin.reports.categories') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end text-xs">
            <div>
                <label class="block font-semibold text-plum mb-1">From Date</label>
                <input 
                    type="date" 
                    name="start_date" 
                    value="{{ $startDate }}" 
                    class="w-full rounded-xl border-plum/20 bg-white/70 p-2 text-plum focus:ring-primary"
                >
            </div>
            <div>
                <label class="block font-semibold text-plum mb-1">To Date</label>
                <input 
                    type="date" 
                    name="end_date" 
                    value="{{ $endDate }}" 
                    class="w-full rounded-xl border-plum/20 bg-white/70 p-2 text-plum focus:ring-primary"
                >
            </div>
            <div>
                <button type="submit" class="w-full py-2.5 rounded-xl font-bold bg-primary text-white hover:bg-primary-700 shadow-xs transition-all">
                    Generate Category Report
                </button>
            </div>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Total Pieces Sold Across Categories</span>
            <span class="text-3xl font-serif font-bold text-primary">{{ number_format($totalUnits) }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">Garments fulfilled in period</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Total Category Revenue</span>
            <span class="text-3xl font-serif font-bold text-emerald-700">₹{{ number_format($totalRevenue, 2) }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">Combined department turnover</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Top Selling Category</span>
            <span class="text-2xl font-serif font-bold text-gold-700">
                {{ $categories->first()?->name ?? 'None' }}
            </span>
            <span class="text-[11px] text-plum/60 block mt-1">
                {{ $categories->first()?->units_sold ?? 0 }} units sold
            </span>
        </div>
    </div>

    {{-- Category Performance Table --}}
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/90 shadow-sm">
        <h2 class="text-lg font-serif font-bold text-plum pb-3 border-b border-plum/10 mb-4">
            Department Performance & Market Share
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px]">
                        <th class="py-3 px-3">Category Name</th>
                        <th class="py-3 px-3 text-center">Active Styles</th>
                        <th class="py-3 px-3 text-center">Units Sold</th>
                        <th class="py-3 px-3">Volume Share (%)</th>
                        <th class="py-3 px-3 text-right">Gross Sales</th>
                        <th class="py-3 px-3 text-right">Avg Ticket Size</th>
                        <th class="py-3 px-3 text-center">Current Store Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-plum/5">
                    @foreach($categories as $cat)
                        @php
                            $sharePct = $totalUnits > 0 ? round(($cat->units_sold / $totalUnits) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-plum/5 transition-colors">
                            <td class="py-3.5 px-3 font-bold text-plum">
                                <div class="flex items-center space-x-2">
                                    <div class="w-2.5 h-2.5 rounded-full bg-primary-500"></div>
                                    <span>{{ $cat->name }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-3 text-center font-semibold text-plum/70">
                                {{ $cat->products_count }} designs
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <span class="px-2.5 py-1 rounded-lg bg-plum/5 font-mono font-bold text-plum">
                                    {{ $cat->units_sold }} pcs
                                </span>
                            </td>
                            <td class="py-3.5 px-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-24 bg-plum/10 rounded-full h-2 overflow-hidden">
                                        <div class="bg-primary-500 h-2 rounded-full" style="width: {{ $sharePct }}%"></div>
                                    </div>
                                    <span class="text-[11px] font-mono font-bold text-plum/80">{{ $sharePct }}%</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-3 text-right font-serif font-bold text-primary text-sm">
                                ₹{{ number_format($cat->gross_revenue, 2) }}
                            </td>
                            <td class="py-3.5 px-3 text-right font-medium text-plum/70">
                                ₹{{ number_format($cat->avg_ticket, 2) }}
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $cat->current_inventory <= 10 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ $cat->current_inventory }} in stock
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
