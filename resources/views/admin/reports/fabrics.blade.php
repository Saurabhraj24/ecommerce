@extends('layouts.admin')

@section('title', 'Fabric & Textile Performance Report - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gold-100 text-gold-800">
                    Textile & Fabric Audit
                </span>
                <span class="text-xs text-plum/50 font-medium">Bombay Fashion Amritsar</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-plum mt-1">
                Fabric Sales & Material Analytics
            </h1>
            <p class="text-xs text-plum/70 mt-1">
                Identify which fabrics (Pure Silk, Banarasi, Chanderi, Georgette, Cotton, Velvet, Linen) sell the most pieces and yield highest customer demand.
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
        <form method="GET" action="{{ route('admin.reports.fabrics') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end text-xs">
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
                    Generate Fabric Report
                </button>
            </div>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Total Fabric Garments Sold</span>
            <span class="text-3xl font-serif font-bold text-primary">{{ number_format($totalFabricPieces) }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">Pieces across all fabrics</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Total Fabric Sales Turnover</span>
            <span class="text-3xl font-serif font-bold text-emerald-700">₹{{ number_format($totalFabricRevenue, 2) }}</span>
            <span class="text-[11px] text-plum/60 block mt-1">Gross fabric revenue</span>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-xs">
            <span class="text-xs text-plum/60 font-bold uppercase tracking-wider block mb-1">Top Selling Fabric</span>
            <span class="text-2xl font-serif font-bold text-gold-700">
                {{ $fabricStats->first()?->fabric ?? 'No Data' }}
            </span>
            <span class="text-[11px] text-plum/60 block mt-1">
                {{ $fabricStats->first()?->units_sold ?? 0 }} units sold
            </span>
        </div>
    </div>

    {{-- Fabric Table --}}
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/90 shadow-sm">
        <h2 class="text-lg font-serif font-bold text-plum pb-3 border-b border-plum/10 mb-4">
            Fabric Materials Ranking
        </h2>

        @if($fabricStats->isEmpty())
            <div class="py-10 text-center text-xs text-plum/50 italic">
                No fabric sales recorded in this date range.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3">Fabric Material</th>
                            <th class="py-3 px-3 text-center">Active Designs</th>
                            <th class="py-3 px-3 text-center">Units Sold</th>
                            <th class="py-3 px-3">Volume Share (%)</th>
                            <th class="py-3 px-3 text-right">Gross Sales Turnover</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($fabricStats as $fs)
                            @php
                                $sharePct = $totalFabricPieces > 0 ? round(($fs->units_sold / $totalFabricPieces) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3.5 px-3 font-bold text-plum">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2 h-2 rounded-full bg-gold-600"></span>
                                        <span>{{ $fs->fabric }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-center font-semibold text-plum/70">
                                    {{ $fs->distinct_styles }} styles
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="px-2.5 py-1 rounded-lg bg-plum/5 font-mono font-bold text-plum">
                                        {{ $fs->units_sold }} pcs
                                    </span>
                                </td>
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24 bg-plum/10 rounded-full h-2 overflow-hidden">
                                            <div class="bg-gold-600 h-2 rounded-full" style="width: {{ $sharePct }}%"></div>
                                        </div>
                                        <span class="text-[11px] font-mono font-bold text-plum/80">{{ $sharePct }}%</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-right font-serif font-bold text-primary text-sm">
                                    ₹{{ number_format((float)$fs->gross_revenue, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
