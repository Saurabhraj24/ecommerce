@extends('layouts.admin')

@section('title', 'Top VIP Customers & Retail Buyer Insights - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gold-100 text-gold-800">
                    VIP Client Management
                </span>
                <span class="text-xs text-plum/50 font-medium">Bombay Fashion Amritsar</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-plum mt-1">
                Top Customers &amp; Spending Insights
            </h1>
            <p class="text-xs text-plum/70 mt-1">
                Identify highest-spending retail customers, bridal trousseau shoppers, and repeat buyers for loyalty offers.
            </p>
        </div>
        <a 
            href="{{ route('admin.users.index') }}" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold border border-plum/20 bg-white text-plum hover:bg-plum/5 transition-all self-start sm:self-auto"
        >
            All Customers List
        </a>
    </div>

    {{-- Top Customers Table --}}
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/90 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-plum/10 mb-4">
            <h2 class="text-lg font-serif font-bold text-plum">
                Highest Spending Customers Ranking
            </h2>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-gold-50 text-gold-800">
                {{ $topCustomers->total() }} Active Buyers
            </span>
        </div>

        @if($topCustomers->isEmpty())
            <div class="py-12 text-center text-xs text-plum/50 italic">
                No customer orders found yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="py-3 px-3">Rank / Customer</th>
                            <th class="py-3 px-3">Contact Details</th>
                            <th class="py-3 px-3 text-center">Completed Orders</th>
                            <th class="py-3 px-3 text-right">Lifetime Total Spent</th>
                            <th class="py-3 px-3 text-right">Avg Basket Value</th>
                            <th class="py-3 px-3 text-center">Customer Since</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($topCustomers as $index => $cust)
                            @php
                                $avgSpend = $cust->orders_count > 0 ? ($cust->orders_sum_grand_total / $cust->orders_count) : 0;
                            @endphp
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3.5 px-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-gold-500 to-amber-400 flex items-center justify-center font-bold text-white text-xs shrink-0 shadow-xs">
                                            #{{ $index + 1 }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.users.show', $cust->id) }}" class="font-bold text-plum hover:text-primary transition block leading-tight">
                                                {{ $cust->name }}
                                            </a>
                                            @if($cust->orders_sum_grand_total >= 50000)
                                                <span class="text-[9px] font-bold uppercase tracking-wider text-gold-700 bg-gold-50 px-1.5 py-0.5 rounded">
                                                    👑 Platinum VIP
                                                </span>
                                            @elseif($cust->orders_sum_grand_total >= 20000)
                                                <span class="text-[9px] font-bold uppercase tracking-wider text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded">
                                                    ⭐ Gold VIP
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="font-mono text-plum/80 block">{{ $cust->phone ?: 'No phone' }}</span>
                                    <span class="text-[10px] text-plum/50">{{ $cust->email }}</span>
                                </td>
                                <td class="py-3.5 px-3 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold bg-plum/5 text-plum">
                                        {{ $cust->orders_count }} orders
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-right font-serif font-bold text-primary text-sm">
                                    ₹{{ number_format((float)$cust->orders_sum_grand_total, 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-right font-medium text-plum/70">
                                    ₹{{ number_format($avgSpend, 2) }}
                                </td>
                                <td class="py-3.5 px-3 text-center text-plum/60 font-mono text-[11px]">
                                    {{ $cust->created_at->format('M Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $topCustomers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
