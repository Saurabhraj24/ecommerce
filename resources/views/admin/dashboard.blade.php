@extends('layouts.admin')

@section('title', 'Admin Dashboard - Bombay Fashion Amritsar')

@section('content')
<div class="space-y-8">
    
    {{-- Header Banner --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-primary-100 text-primary-800 mb-2">
                Store Operations Desk
            </span>
            <h1 class="text-3xl font-serif font-bold text-plum">
                Bombay Fashion Dashboard
            </h1>
            <p class="text-xs text-plum/70 mt-1">
                Real-time sales, order statuses, and inventory metrics from your Amritsar store.
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if(auth()->user()->hasPermission('products.manage'))
                <a 
                    href="{{ route('admin.products.create') }}" 
                    class="px-4 py-2.5 rounded-xl text-xs font-bold bg-primary text-white shadow-md hover:bg-primary-700 transition-all flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add New Product
                </a>
            @endif
            @if(auth()->user()->hasPermission('orders.manage'))
                <a 
                    href="{{ route('admin.orders.index') }}" 
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold border border-plum/20 bg-white/80 text-plum hover:bg-white shadow-sm transition-all"
                >
                    Manage Orders
                </a>
            @endif
        </div>
    </div>

    {{-- Metrics Cards Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        
        {{-- Today's Sales (Admin Only) --}}
        @if($canViewRevenue)
            <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-plum/60 mb-2">
                    <span class="font-bold uppercase tracking-wider">Today's Revenue</span>
                    <span class="p-2 rounded-xl bg-primary-100 text-primary-800">
                        ₹
                    </span>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-serif font-bold text-primary">
                        ₹{{ number_format($todaySales, 2) }}
                    </span>
                    <span class="text-[11px] text-plum/60 block mt-1">{{ $todayOrdersCount }} {{ Str::plural('order', $todayOrdersCount) }} placed today</span>
                </div>
            </div>

            {{-- Monthly Sales --}}
            <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-plum/60 mb-2">
                    <span class="font-bold uppercase tracking-wider">This Month's Sales</span>
                    <span class="p-2 rounded-xl bg-gold-100 text-gold-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </span>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-serif font-bold text-gold-700">
                        ₹{{ number_format($monthSales, 2) }}
                    </span>
                    <span class="text-[11px] text-plum/60 block mt-1">{{ $monthOrdersCount }} orders this month</span>
                </div>
            </div>
        @else
            {{-- Staff view: Today's Orders --}}
            <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-plum/60 mb-2">
                    <span class="font-bold uppercase tracking-wider">Today's Orders</span>
                    <span class="p-2 rounded-xl bg-primary-100 text-primary-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </span>
                </div>
                <div>
                    <span class="text-3xl font-serif font-bold text-plum">{{ $todayOrdersCount }}</span>
                    <span class="text-[11px] text-plum/60 block mt-1">{{ $totalClothesSoldToday }} garment pieces sold today</span>
                </div>
            </div>

            {{-- Staff view: Today's Shift & Attendance Status --}}
            <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-plum/60 mb-2">
                    <span class="font-bold uppercase tracking-wider">My Attendance</span>
                    <span class="p-2 rounded-xl {{ $todayAttendance ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div>
                    @if($todayAttendance)
                        <span class="text-xl sm:text-2xl font-serif font-bold text-emerald-700 capitalize">
                            {{ $todayAttendance->status }}
                        </span>
                        <span class="text-[11px] text-plum/60 block mt-1 font-mono">
                            In: {{ $todayAttendance->punch_in_at ? \Carbon\Carbon::parse($todayAttendance->punch_in_at)->format('h:i A') : ($todayAttendance->check_in ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') : 'Recorded') }}
                        </span>
                    @else
                        <span class="text-lg font-serif font-bold text-amber-600">
                            Not Punched Yet
                        </span>
                        <a href="{{ route('admin.attendance.mark') }}" class="text-[11px] font-bold text-primary hover:underline block mt-1">
                            Scan QR to Punch &rarr;
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- Pending Orders Action Alert (Admin/Manager only) --}}
        @if(auth()->user()->hasPermission('orders.manage'))
            <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-plum/60 mb-2">
                    <span class="font-bold uppercase tracking-wider text-amber-700">Pending Orders</span>
                    <span class="p-2 rounded-xl bg-amber-100 text-amber-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div>
                    <span class="text-2xl sm:text-3xl font-serif font-bold text-amber-600">
                        {{ $pendingOrdersCount }}
                    </span>
                    <span class="text-[11px] text-plum/60 block mt-1">Requires packing / confirmation</span>
                </div>
            </div>
        @else
            {{-- Staff view: My Purchases --}}
            <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between text-xs text-plum/60 mb-2">
                    <span class="font-bold uppercase tracking-wider">My Purchases</span>
                    <span class="p-2 rounded-xl bg-primary-100 text-primary-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </span>
                </div>
                <div>
                    <span class="text-3xl font-serif font-bold text-primary">{{ $staffPurchasesCount }}</span>
                    <span class="text-[11px] text-plum/60 block mt-1">
                        <a href="{{ route('admin.profile.purchases') }}" class="underline hover:text-primary">View My Garments &rarr;</a>
                    </span>
                </div>
            </div>
        @endif

        {{-- Low Stock Alerts --}}
        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs text-plum/60 mb-2">
                <span class="font-bold uppercase tracking-wider {{ $lowStockTotal > 0 ? 'text-rose-700' : 'text-plum/60' }}">
                    Low Stock Alerts
                </span>
                <span class="p-2 rounded-xl {{ $lowStockTotal > 0 ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
            </div>
            <div>
                <span class="text-2xl sm:text-3xl font-serif font-bold {{ $lowStockTotal > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    {{ $lowStockTotal }}
                </span>
                <span class="text-[11px] text-plum/60 block mt-1">
                    <a href="{{ route('admin.inventory.index') }}" class="underline hover:text-primary">View in Inventory</a>
                </span>
            </div>
        </div>

    </div>

    {{-- Retail Cloth Sales & Volume Hub Banner ("Kitna & Kon Sa Kapda Bikha") --}}
    <div class="glass-card rounded-3xl p-5 sm:p-6 border border-primary-200/60 bg-gradient-to-r from-blush/40 via-white/90 to-cream-100/60 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-primary-100 text-primary-800">
                    Retail Garments Intelligence
                </span>
                <span class="text-xs text-plum/60 font-semibold">Amritsar Showroom</span>
            </div>
            <h3 class="text-lg font-serif font-bold text-plum">
                Kitna & Kon Sa Kapda Bikha — Retail Volume Tracking
            </h3>
            <p class="text-xs text-plum/70">
                Total <strong class="text-primary font-bold">{{ number_format($totalClothesSoldMonth) }} cloth pieces</strong> sold this month ({{ number_format($totalClothesSoldToday) }} today). View itemized garment sales, fabric performance, and low-stock replenishment alerts.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <a 
                href="{{ route('admin.reports.cloth-sales') }}" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold bg-primary text-white hover:bg-primary-700 shadow-md shadow-primary-500/20 transition flex items-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span>Itemized Cloth Sales &rarr;</span>
            </a>
            <a 
                href="{{ route('admin.pos.index') }}" 
                class="px-3.5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5"
            >
                <span>🧾 Open POS Biller</span>
            </a>
            <a 
                href="{{ route('admin.inventory.reorder') }}" 
                class="px-3.5 py-2.5 rounded-xl text-xs font-bold border border-rose-300 bg-rose-50 text-rose-700 hover:bg-rose-100 transition flex items-center gap-1.5"
            >
                <span>⚠️ Reorder Alerts</span>
                @if($lowStockTotal > 0)
                    <span class="px-1.5 py-0.2 rounded-full bg-rose-600 text-white text-[10px]">{{ $lowStockTotal }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- 7-Day Real Sales Trend Chart (Admin Only) --}}
    @if($canViewRevenue)
        @php
            $maxTrendAmount = max(1, collect($salesTrend)->max('amount'));
        @endphp
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/80 shadow-md">
            <div class="flex items-center justify-between pb-4 border-b border-plum/10 mb-6">
                <div>
                    <h2 class="text-lg font-serif font-bold text-plum">Recent 7-Day Sales Trend</h2>
                    <p class="text-xs text-plum/60 mt-0.5">Real revenue generated from paid orders</p>
                </div>
                <span class="text-xs font-bold text-primary font-mono">
                    Max: ₹{{ number_format($maxTrendAmount, 2) }}
                </span>
            </div>

            <div class="grid grid-cols-7 gap-3 sm:gap-6 items-end h-48 pt-6">
                @foreach($salesTrend as $day)
                    @php
                        $barHeight = round(($day['amount'] / $maxTrendAmount) * 100);
                    @endphp
                    <div class="flex flex-col items-center h-full justify-end group">
                        <span class="text-[10px] font-bold text-plum/70 opacity-0 group-hover:opacity-100 transition-opacity mb-1 font-mono">
                            ₹{{ number_format($day['amount'], 0) }}
                        </span>
                        <div class="w-full max-w-[2.5rem] bg-plum/10 rounded-t-xl overflow-hidden h-36 flex items-end">
                            <div 
                                class="w-full bg-gradient-to-t from-primary to-primary-400 rounded-t-xl transition-all duration-700 group-hover:brightness-110" 
                                style="height: {{ max(4, $barHeight) }}%"
                            ></div>
                        </div>
                        <span class="text-[11px] font-medium text-plum/70 mt-2 whitespace-nowrap">{{ $day['date'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Order Status Quick Breakdown --}}
    @if(auth()->user()->hasPermission('orders.manage'))
        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/80 shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-plum/70 mb-3">Order Status Pipeline</h3>
            <div class="flex flex-wrap gap-2">
                @foreach(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'packed' => 'Packed', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled', 'returned' => 'Returned'] as $stKey => $stLabel)
                    @php
                        $count = $ordersByStatus[$stKey] ?? 0;
                    @endphp
                    <a 
                        href="{{ route('admin.orders.index', ['status' => $stKey]) }}" 
                        class="px-3 py-1.5 rounded-xl border border-plum/10 bg-white/70 hover:bg-white text-xs font-medium flex items-center gap-2 transition-all shadow-sm"
                    >
                        <span class="text-plum">{{ $stLabel }}:</span>
                        <span class="font-bold text-primary">{{ $count }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Clean Layout Separation: Admin (Recent Orders 7-col + Top Products 5-col) vs Staff (Staff Desk 6-col + Top Products 6-col) --}}
    @if(auth()->user()->hasPermission('orders.manage'))
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- Recent Orders Table --}}
            <div class="lg:col-span-7">
                <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/80 shadow-md">
                    <div class="flex items-center justify-between pb-4 border-b border-plum/10 mb-4">
                        <h2 class="text-lg font-serif font-bold text-plum">Recent Orders</h2>
                        <a href="{{ route('admin.orders.index') }}" class="text-xs text-primary font-bold hover:underline">
                            View All Orders &rarr;
                        </a>
                    </div>

                    @if($recentOrders->isEmpty())
                        <div class="py-8 text-center text-plum/50 text-xs italic">
                            No orders recorded in the system yet.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px]">
                                        <th class="py-2.5">Order</th>
                                        <th class="py-2.5">Customer</th>
                                        <th class="py-2.5">Status</th>
                                        <th class="py-2.5 text-right">Amount</th>
                                        <th class="py-2.5 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-plum/5">
                                    @foreach($recentOrders as $ro)
                                        <tr class="hover:bg-plum/5 transition-colors">
                                            <td class="py-3 font-mono font-bold text-plum">
                                                #{{ $ro->order_number }}
                                            </td>
                                            <td class="py-3 text-plum">
                                                <span class="font-medium block">{{ $ro->shipping_name }}</span>
                                                <span class="text-[10px] text-plum/50">{{ $ro->created_at->format('d M, h:i A') }}</span>
                                            </td>
                                            <td class="py-3">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-primary-100 text-primary-800">
                                                    {{ ucfirst($ro->status) }}
                                                </span>
                                            </td>
                                            <td class="py-3 text-right font-serif font-bold text-plum">
                                                ₹{{ number_format($ro->grand_total, 2) }}
                                            </td>
                                            <td class="py-3 text-right">
                                                <a href="{{ route('admin.orders.show', $ro->id) }}" class="text-primary hover:underline font-bold">
                                                    Manage
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Top Selling Products --}}
            <div class="lg:col-span-5">
                <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/80 shadow-md">
                    <div class="flex items-center justify-between pb-4 border-b border-plum/10 mb-4">
                        <h2 class="text-lg font-serif font-bold text-plum">Top Products</h2>
                        <span class="text-xs text-plum/50">By items sold</span>
                    </div>

                    @if($topProducts->isEmpty())
                        <div class="py-8 text-center text-plum/50 text-xs italic">
                            No product sales recorded yet. Once customers place orders, top performers will appear here.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($topProducts as $tp)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-plum/5 text-xs">
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-bold text-plum truncate">{{ $tp->product_name }}</h4>
                                        <span class="text-[11px] text-plum/60">{{ $tp->total_sold }} units sold</span>
                                    </div>
                                    <span class="font-serif font-bold text-primary ml-3">
                                        ₹{{ number_format($tp->total_revenue, 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    @else
        {{-- Staff Operations Hub & Top Products Balanced 2-Column Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            {{-- Staff Desk & Quick Launchpad --}}
            <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/80 shadow-md space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-plum/10">
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-primary-100 text-primary-800">
                            Staff Operations
                        </span>
                        <h2 class="text-lg font-serif font-bold text-plum mt-1">My Work Desk & Shortcuts</h2>
                    </div>
                    <span class="text-xs font-semibold text-plum/60">{{ auth()->user()->role?->label ?? 'Store Staff' }}</span>
                </div>

                {{-- Shift & Attendance Card --}}
                <div class="p-4 rounded-2xl bg-gradient-to-br from-cream-100 to-blush/30 border border-primary-200/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $todayAttendance ? 'bg-emerald-500 animate-pulse' : 'bg-amber-400' }}"></span>
                            <span class="text-xs font-bold text-plum">
                                Today's Attendance: {{ $todayAttendance ? ucfirst($todayAttendance->status) : 'Not Punched' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-plum/70 mt-1">
                            @if($todayAttendance)
                                Punch-In: {{ $todayAttendance->punch_in_at ? \Carbon\Carbon::parse($todayAttendance->punch_in_at)->format('h:i A') : ($todayAttendance->check_in ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') : 'Recorded') }}
                            @else
                                Scan showroom admin counter QR code to mark your attendance today.
                            @endif
                        </p>
                    </div>
                    <a 
                        href="{{ route('admin.attendance.mark') }}" 
                        class="px-4 py-2.5 rounded-xl text-xs font-bold bg-primary text-white hover:bg-primary-700 shadow-md shadow-primary-500/20 transition flex items-center justify-center gap-1.5 shrink-0"
                    >
                        <span>📷 Mark Attendance (QR)</span>
                    </a>
                </div>

                {{-- Quick Nav Links for Staff --}}
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <a 
                        href="{{ route('admin.pos.index') }}" 
                        class="p-4 rounded-2xl border border-plum/10 bg-white/70 hover:bg-white hover:border-emerald-500/40 hover:shadow-md transition-all flex flex-col justify-between group"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-xl">🧾</span>
                            <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider bg-emerald-50 px-2 py-0.5 rounded-full">Biller</span>
                        </div>
                        <div class="mt-3">
                            <h4 class="text-xs font-bold text-plum group-hover:text-emerald-700 transition">Showroom POS Counter</h4>
                            <p class="text-[10px] text-plum/50 mt-0.5">Quick retail billing</p>
                        </div>
                    </a>

                    <a 
                        href="{{ route('admin.profile.show') }}" 
                        class="p-4 rounded-2xl border border-plum/10 bg-white/70 hover:bg-white hover:border-primary-500/40 hover:shadow-md transition-all flex flex-col justify-between group"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-xl">👤</span>
                            <span class="text-[10px] font-bold text-primary uppercase tracking-wider bg-primary-50 px-2 py-0.5 rounded-full">Profile</span>
                        </div>
                        <div class="mt-3">
                            <h4 class="text-xs font-bold text-plum group-hover:text-primary transition">My Profile & Shift</h4>
                            <p class="text-[10px] text-plum/50 mt-0.5">Personal info & device</p>
                        </div>
                    </a>

                    <a 
                        href="{{ route('admin.profile.salary') }}" 
                        class="p-4 rounded-2xl border border-plum/10 bg-white/70 hover:bg-white hover:border-gold-500/40 hover:shadow-md transition-all flex flex-col justify-between group"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-xl">💰</span>
                            <span class="text-[10px] font-bold text-gold-800 uppercase tracking-wider bg-gold-50 px-2 py-0.5 rounded-full">Payslips</span>
                        </div>
                        <div class="mt-3">
                            <h4 class="text-xs font-bold text-plum group-hover:text-gold-700 transition">My Salary Register</h4>
                            <p class="text-[10px] text-plum/50 mt-0.5">View salary & slips</p>
                        </div>
                    </a>

                    <a 
                        href="{{ route('admin.profile.purchases') }}" 
                        class="p-4 rounded-2xl border border-plum/10 bg-white/70 hover:bg-white hover:border-primary-500/40 hover:shadow-md transition-all flex flex-col justify-between group"
                    >
                        <div class="flex items-center justify-between">
                            <span class="text-xl">🛍️</span>
                            <span class="text-[10px] font-bold text-primary uppercase tracking-wider bg-primary-50 px-2 py-0.5 rounded-full">{{ $staffPurchasesCount }} Orders</span>
                        </div>
                        <div class="mt-3">
                            <h4 class="text-xs font-bold text-plum group-hover:text-primary transition">My Garments</h4>
                            <p class="text-[10px] text-plum/50 mt-0.5">Purchases & receipts</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Top Selling Garments for Staff (Balanced 6-col width) --}}
            <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/80 shadow-md">
                <div class="flex items-center justify-between pb-4 border-b border-plum/10 mb-6">
                    <div>
                        <h2 class="text-lg font-serif font-bold text-plum">Top Selling Garments</h2>
                        <p class="text-xs text-plum/50 mt-0.5">Best performing showroom & online stock</p>
                    </div>
                    <span class="text-xs font-bold text-primary bg-primary-50 px-3 py-1 rounded-full">By items sold</span>
                </div>

                @if($topProducts->isEmpty())
                    <div class="py-12 text-center text-plum/50 text-xs italic">
                        No product sales recorded yet. Once garments sell on POS counter or online store, top performers will appear here.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($topProducts as $tp)
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-white/60 border border-plum/10 hover:border-primary-300 hover:shadow-sm transition-all text-xs">
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-bold text-plum text-sm truncate">{{ $tp->product_name }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary-100 text-primary-800">
                                            {{ $tp->total_sold }} units sold
                                        </span>
                                    </div>
                                </div>
                                <span class="font-serif font-bold text-primary text-sm ml-4 shrink-0">
                                    ₹{{ number_format($tp->total_revenue, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    @endif

</div>
@endsection
