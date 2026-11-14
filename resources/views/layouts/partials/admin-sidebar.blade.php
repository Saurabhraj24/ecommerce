@php
    $user = Auth::user();
    $unreadMessages = \App\Models\ContactMessage::unread()->count();
    $pendingReviews = \App\Models\Review::where('is_approved', false)->count();
    $pendingOrders = \App\Models\Order::where('order_status', 'pending')->count();
    $lowStockCount = \App\Models\Product::where('stock', '<=', 5)->count();
@endphp

<!-- Mobile Drawer Backdrop Overlay (Mobile only: md:hidden) -->
<div 
    x-show="sidebarOpen" 
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false" 
    class="fixed inset-0 bg-festive-plum/60 backdrop-blur-xs z-40 md:hidden"
    style="display: none;"
></div>

<aside 
    x-show="sidebarOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:leave="transition ease-in duration-150"
    class="w-64 shrink-0 sticky top-0 h-screen bg-white/95 backdrop-blur-xl border-r border-primary-100/70 flex flex-col z-30 max-md:fixed max-md:inset-y-0 max-md:left-0 max-md:z-50 max-md:w-72 max-md:shadow-2xl"
    style="display: none;"
>
    
    <!-- Admin Brand Header -->
    <div class="h-20 px-5 sm:px-6 flex items-center justify-between border-b border-primary-100/60 bg-gradient-to-r from-blush/30 to-cream-100/40 shrink-0">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-primary-600 to-primary-400 flex items-center justify-center text-white shadow-md shadow-primary-500/20">
                <span class="font-serif font-bold text-lg">BF</span>
            </div>
            <div>
                <h1 class="font-serif font-bold text-base text-plum leading-tight">Bombay Fashion</h1>
                <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-primary-100 text-primary-700">
                    {{ $user->isAdmin() ? 'Owner Admin' : 'Staff Desk' }}
                </span>
            </div>
        </div>

        <!-- Mobile Close Drawer Button -->
        <button 
            @click="sidebarOpen = false" 
            type="button" 
            class="md:hidden p-2 rounded-xl text-plum/60 hover:text-primary hover:bg-primary-50 transition cursor-pointer"
            aria-label="Close Sidebar"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Navigation Menu Links -->
    <div 
        @click="if (window.innerWidth < 768) sidebarOpen = false"
        class="flex-1 overflow-y-auto px-4 py-5 space-y-6 text-xs font-semibold"
    >
        
        <!-- SECTION 1: Store Operations (Staff & Admin) -->
        <div class="space-y-1">
            <span class="px-3 text-[10px] uppercase font-bold tracking-wider text-plum/40 block mb-1">
                Store Operations
            </span>

            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Dashboard</span>
            </a>

            {{-- Operations Hub / Launchpad with quick-access cards --}}
            <a href="{{ route('admin.hub') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.hub') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>All Cards Hub</span>
                </div>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-primary-100 text-primary-800">
                    All In One
                </span>
            </a>
        </div>

        <!-- SECTION 2: My Staff Account & Self-Service (Staff & Admin) -->
        <div class="space-y-1 pt-2 border-t border-primary-100/60">
            <span class="px-3 text-[10px] uppercase font-bold tracking-wider text-primary-700 block mb-1">
                My Staff Account
            </span>

            {{-- 1. Mark Attendance (Camera QR Scanner) --}}
            <a href="{{ route('admin.attendance.mark') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.attendance.mark') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>Mark Attendance (QR)</span>
                </div>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">
                    Scan QR
                </span>
            </a>

            {{-- 2. My Profile & Shift --}}
            <a href="{{ route('admin.profile.show') }}" 
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.profile.show') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span>My Profile &amp; Shift</span>
            </a>

            {{-- 3. My Salary & Payslips --}}
            <a href="{{ route('admin.profile.salary') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.profile.salary') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>My Salary &amp; Payslips</span>
                </div>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">
                    ₹ Slips
                </span>
            </a>

            {{-- 4. My Personal Purchases --}}
            <a href="{{ route('admin.profile.purchases') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.profile.purchases') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span>My Purchases (Clothes)</span>
                </div>
                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-primary-100 text-primary-800">
                    Orders
                </span>
            </a>
        </div>

        <!-- SECTION 3: Store Management Operations -->
        <div class="space-y-1 pt-2 border-t border-primary-100/60">
            <span class="px-3 text-[10px] uppercase font-bold tracking-wider text-plum/40 block mb-1">
                Store Operations
            </span>

            {{-- Showroom Walk-in Counter POS Billing --}}
            @if($user->hasPermission('pos.access'))
                <a href="{{ route('admin.pos.index') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.pos.*') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <span>Showroom POS Counter</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">
                        Biller
                    </span>
                </a>
            @endif

            @if($user->hasPermission('orders.manage'))
                <a href="{{ route('admin.orders.index') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.orders.*') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span>Orders & Returns</span>
                    </div>
                    @if ($pendingOrders > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ request()->routeIs('admin.orders.*') ? 'bg-white text-primary-600' : 'bg-primary-100 text-primary-700' }}">
                            {{ $pendingOrders }}
                        </span>
                    @endif
                </a>
            @endif

            @if($user->hasPermission('products.manage'))
                <a href="{{ route('admin.products.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.products.*') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span>Products & Stock</span>
                </a>
            @endif

            @if($user->hasPermission('inventory.manage'))
                <a href="{{ route('admin.inventory.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.inventory.index') || request()->routeIs('admin.inventory.logs') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span>Inventory & Audit Logs</span>
                </a>
            @endif

            @if($user->isAdmin())
                <a href="{{ route('admin.attendance.index') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.attendance.*') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        <span>Staff Attendance & QR</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">
                        Live
                    </span>
                </a>
            @endif

            @if($user->hasPermission('reviews.moderate'))
                <a href="{{ route('admin.reviews.index') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reviews.*') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                        <span>Customer Reviews</span>
                    </div>
                    @if ($pendingReviews > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gold-100 text-gold-800">
                            {{ $pendingReviews }}
                        </span>
                    @endif
                </a>
            @endif

            @if($user->hasPermission('messages.manage'))
                <a href="{{ route('admin.messages.index') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.messages.*') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span>Contact Inquiries</span>
                    </div>
                    @if ($unreadMessages > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                            {{ $unreadMessages }}
                        </span>
                    @endif
                </a>
            @endif
        </div>

        <!-- SECTION 2: Cloth Sales & Retail Analytics (Admin) -->
        @if ($user->isAdmin())
            <div class="space-y-1 pt-2 border-t border-primary-100/60">
                <span class="px-3 text-[10px] uppercase font-bold tracking-wider text-primary-700 block mb-1">
                    Cloth Sales & Analytics
                </span>

                {{-- Kitna & Kon Sa Kapda Bikha --}}
                <a href="{{ route('admin.reports.cloth-sales') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reports.cloth-sales') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span>Cloth Sales Breakdown</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-primary-100 text-primary-700">
                        Units
                    </span>
                </a>

                {{-- Category Performance --}}
                <a href="{{ route('admin.reports.categories') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reports.categories') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span>Category Share & Volume</span>
                </a>

                {{-- Fabric & Textile Performance --}}
                <a href="{{ route('admin.reports.fabrics') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reports.fabrics') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Fabric Material Sales</span>
                </a>

                {{-- Low Stock & Reorder Alerts --}}
                <a href="{{ route('admin.inventory.reorder') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.inventory.reorder') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Low Stock Reorders</span>
                    </div>
                    @if($lowStockCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                            {{ $lowStockCount }}
                        </span>
                    @endif
                </a>

                {{-- Slow Moving & Dead Stock --}}
                <a href="{{ route('admin.inventory.dead-stock') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.inventory.dead-stock') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Dead Stock Analyzer</span>
                </a>

                {{-- Customer Wishlist Demand Signals --}}
                <a href="{{ route('admin.reports.wishlist') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reports.wishlist') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span>Wishlist Demand Signals</span>
                </a>

                {{-- Abandoned Carts Tracker --}}
                <a href="{{ route('admin.reports.abandoned-carts') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reports.abandoned-carts') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Abandoned Carts Recovery</span>
                </a>

                {{-- GST & Invoicing Tax Reports --}}
                <a href="{{ route('admin.reports.tax') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reports.tax') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>
                    <span>GST & Tax Compliance</span>
                </a>

                {{-- VIP Top Customers --}}
                <a href="{{ route('admin.reports.customers') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reports.customers') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                    <span>VIP Customer Insights</span>
                </a>

                {{-- General Financial Reports & CSV --}}
                <a href="{{ route('admin.reports.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reports.index') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span>General Financial Reports</span>
                </a>
            </div>
        @endif

        <!-- SECTION 3: Store Configuration (Admin Only) -->
        @if ($user->isAdmin())
            <div class="space-y-1 pt-2 border-t border-primary-100/60">
                <span class="px-3 text-[10px] uppercase font-bold tracking-wider text-plum/40 block mb-1">
                    Store Configuration
                </span>

                <a href="{{ route('admin.categories.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.categories.*') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    <span>Categories Hierarchy</span>
                </a>

                <a href="{{ route('admin.banners.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.banners.*') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Banners (Cloudinary)</span>
                </a>

                <a href="{{ route('admin.coupons.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.coupons.*') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <span>Discount Coupons</span>
                </a>

                <a href="{{ route('admin.users.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.users.*') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>Registered Customers</span>
                </a>

                <a href="{{ route('admin.staff.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.staff.index') || request()->routeIs('admin.staff.create') || request()->routeIs('admin.staff.edit') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>Staff Accounts &amp; Roles</span>
                </a>

                {{-- Staff Salary & Payroll --}}
                <a href="{{ route('admin.staff.salaries.index') }}" 
                   class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.staff.salaries.*') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <div class="flex items-center space-x-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Staff Salary &amp; Payroll</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">
                        ₹ Slips
                    </span>
                </a>

                {{-- Staff Vacation & Leaves --}}
                <a href="{{ route('admin.staff.leaves.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.staff.leaves.*') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Staff Leaves &amp; Vacation</span>
                </a>

                {{-- Staff Advances & Peshgi --}}
                <a href="{{ route('admin.staff.advances.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.staff.advances.*') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Staff Advance (Peshgi)</span>
                </a>

                <a href="{{ route('admin.pages.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.pages.*') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>CMS Static Pages</span>
                </a>

                <a href="{{ route('admin.settings.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.settings.*') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>Store Settings & COD</span>
                </a>

                <a href="{{ route('admin.activity.index') }}" 
                   class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.activity.*') ? 'bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'text-plum/80 hover:bg-white hover:text-primary-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Staff Activity Audit Log</span>
                </a>
            </div>
        @endif

    </div>

    <!-- Sidebar Bottom Customer Store Link -->
    <div class="p-4 border-t border-primary-100/60 bg-cream-100/30 shrink-0">
        <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-between p-2 rounded-xl text-primary-700 bg-primary-50/80 hover:bg-primary-100/80 transition text-xs font-semibold">
            <span>Visit Customer Store</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
        </a>
    </div>

</aside>
