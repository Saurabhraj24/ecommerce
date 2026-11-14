@extends('layouts.admin')

@section('title', 'Operations Hub & Quick Launchpad - Bombay Fashion Admin')

@section('content')
@php
    $user = auth()->user();

    $allCards = [
        // Operations
        [
            'id' => 'pos',
            'title' => 'Showroom POS Biller',
            'category' => 'operations',
            'route' => route('admin.pos.index'),
            'badge' => '⚡ Live POS',
            'badge_class' => 'bg-emerald-100 text-emerald-800',
            'icon_bg' => 'bg-emerald-100 text-emerald-800',
            'hover_border' => 'hover:border-emerald-400/50 hover:bg-emerald-50/50',
            'hover_text' => 'group-hover:text-emerald-700',
            'desc' => 'Walk-in counter billing & receipts',
            'tags' => 'pos bill billing cashier walk-in counter receipt cash upi payment',
            'permission' => 'pos.access',
            'icon' => 'pos',
        ],
        [
            'id' => 'pos_history',
            'title' => 'Cash Register & Shift',
            'category' => 'operations',
            'route' => route('admin.pos.history'),
            'badge' => 'Cash Drawer',
            'badge_class' => 'bg-teal-100 text-teal-800',
            'icon_bg' => 'bg-teal-100 text-teal-800',
            'hover_border' => 'hover:border-teal-400/50 hover:bg-teal-50/40',
            'hover_text' => 'group-hover:text-teal-700',
            'desc' => 'Daily cash & UPI collections',
            'tags' => 'register cash counter upi drawer balance cashier shift pos',
            'permission' => 'pos.access',
            'icon' => 'clock',
        ],
        [
            'id' => 'orders',
            'title' => 'Orders & Returns',
            'category' => 'operations',
            'route' => route('admin.orders.index'),
            'badge' => ($pendingOrders > 0 ? "{$pendingOrders} pending" : 'Orders'),
            'badge_class' => ($pendingOrders > 0 ? 'bg-primary-100 text-primary-700 font-bold' : 'bg-slate-100 text-slate-700'),
            'icon_bg' => 'bg-primary-100 text-primary-700',
            'hover_border' => 'hover:border-primary-400/50 hover:bg-primary-50/40',
            'hover_text' => 'group-hover:text-primary-600',
            'desc' => 'Dispatch, tracking & returns',
            'tags' => 'orders online tracking delivery shipping dispatch returns refund invoice',
            'permission' => 'orders.manage',
            'icon' => 'bag',
        ],
        [
            'id' => 'products',
            'title' => 'Products & Stock',
            'category' => 'operations',
            'route' => route('admin.products.index'),
            'badge' => "{$totalProducts} Styles",
            'badge_class' => 'bg-primary-100 text-primary-800',
            'icon_bg' => 'bg-primary-100 text-primary-700',
            'hover_border' => 'hover:border-primary-400/50 hover:bg-primary-50/40',
            'hover_text' => 'group-hover:text-primary-600',
            'desc' => 'Catalog, variants & photos',
            'tags' => 'product item saree suit uniform create add edit variant photo',
            'permission' => 'products.manage',
            'icon' => 'cube',
        ],
        [
            'id' => 'reorder',
            'title' => 'Low Stock Reorders',
            'category' => 'operations',
            'route' => route('admin.inventory.reorder'),
            'badge' => "{$lowStockCount} alerts",
            'badge_class' => ($lowStockCount > 0 ? 'bg-rose-100 text-rose-700 font-extrabold' : 'bg-slate-100 text-slate-700'),
            'icon_bg' => 'bg-rose-100 text-rose-700',
            'hover_border' => 'hover:border-rose-400/50 hover:bg-rose-50/40',
            'hover_text' => 'group-hover:text-rose-600',
            'desc' => 'Stock < 5 pcs supplier shortage',
            'tags' => 'low stock reorder supplier out of stock shortage replenish',
            'permission' => 'inventory.manage',
            'icon' => 'alert',
        ],
        [
            'id' => 'inventory_logs',
            'title' => 'Stock Audit Logs',
            'category' => 'operations',
            'route' => route('admin.inventory.index'),
            'badge' => 'Audit',
            'badge_class' => 'bg-slate-100 text-slate-700',
            'icon_bg' => 'bg-slate-100 text-slate-800',
            'hover_border' => 'hover:border-slate-400/50 hover:bg-slate-50',
            'hover_text' => 'group-hover:text-primary-600',
            'desc' => 'Manual adjustments history',
            'tags' => 'inventory audit stock logs manual adjust changes track',
            'permission' => 'inventory.manage',
            'icon' => 'clipboard',
        ],
        [
            'id' => 'reviews',
            'title' => 'Reviews & Photos',
            'category' => 'operations',
            'route' => route('admin.reviews.index'),
            'badge' => ($pendingReviews > 0 ? "{$pendingReviews} pending" : 'Reviews'),
            'badge_class' => ($pendingReviews > 0 ? 'bg-amber-100 text-amber-800 font-bold' : 'bg-slate-100 text-slate-700'),
            'icon_bg' => 'bg-amber-100 text-amber-800',
            'hover_border' => 'hover:border-amber-400/50 hover:bg-amber-50/40',
            'hover_text' => 'group-hover:text-amber-800',
            'desc' => 'Customer ratings moderation',
            'tags' => 'reviews ratings photos customer feedback approval verified buyer',
            'permission' => 'reviews.moderate',
            'icon' => 'star',
        ],
        [
            'id' => 'messages',
            'title' => 'Customer Messages',
            'category' => 'operations',
            'route' => route('admin.messages.index'),
            'badge' => ($unreadMessages > 0 ? "{$unreadMessages} unread" : 'Inquiries'),
            'badge_class' => ($unreadMessages > 0 ? 'bg-rose-100 text-rose-700 font-bold' : 'bg-slate-100 text-slate-700'),
            'icon_bg' => 'bg-rose-100 text-rose-700',
            'hover_border' => 'hover:border-rose-400/50 hover:bg-rose-50/40',
            'hover_text' => 'group-hover:text-rose-600',
            'desc' => 'Inquiries & appointments',
            'tags' => 'messages contact inquiries bridal customize appointment help',
            'permission' => 'messages.manage',
            'icon' => 'mail',
        ],
        [
            'id' => 'mark_attendance',
            'title' => 'Mark Attendance (QR)',
            'category' => 'operations',
            'route' => route('admin.attendance.mark'),
            'badge' => 'Camera Scan',
            'badge_class' => 'bg-emerald-100 text-emerald-800 font-bold',
            'icon_bg' => 'bg-emerald-100 text-emerald-800',
            'hover_border' => 'hover:border-emerald-400/50 hover:bg-emerald-50/40',
            'hover_text' => 'group-hover:text-emerald-700',
            'desc' => 'Scan admin showroom QR & punch in/out',
            'tags' => 'attendance mark scan qr camera present punch in out shift',
            'permission' => null,
            'icon' => 'qrcode',
        ],
        [
            'id' => 'my_profile',
            'title' => 'My Profile & Shift',
            'category' => 'operations',
            'route' => route('admin.profile.show'),
            'badge' => 'Personal ID',
            'badge_class' => 'bg-primary-100 text-primary-800',
            'icon_bg' => 'bg-primary-100 text-primary-700',
            'hover_border' => 'hover:border-primary-400/50 hover:bg-primary-50/40',
            'hover_text' => 'group-hover:text-primary-600',
            'desc' => 'Shift timings, emergency & personal info',
            'tags' => 'profile me account staff shift password details emergency bank',
            'permission' => null,
            'icon' => 'users',
        ],
        [
            'id' => 'my_salary',
            'title' => 'My Salary & Payslips',
            'category' => 'operations',
            'route' => route('admin.profile.salary'),
            'badge' => '₹ Slips',
            'badge_class' => 'bg-emerald-100 text-emerald-800',
            'icon_bg' => 'bg-emerald-100 text-emerald-800',
            'hover_border' => 'hover:border-emerald-400/50 hover:bg-emerald-50/40',
            'hover_text' => 'group-hover:text-emerald-700',
            'desc' => 'View monthly earnings & download slips',
            'tags' => 'salary my salary slip payslip pay earnings deduction advances',
            'permission' => null,
            'icon' => 'cash',
        ],
        [
            'id' => 'my_purchases',
            'title' => 'My Purchases (Clothes)',
            'category' => 'operations',
            'route' => route('admin.profile.purchases'),
            'badge' => 'My Orders',
            'badge_class' => 'bg-amber-100 text-amber-800',
            'icon_bg' => 'bg-amber-100 text-amber-800',
            'hover_border' => 'hover:border-amber-400/50 hover:bg-amber-50/40',
            'hover_text' => 'group-hover:text-amber-800',
            'desc' => 'Clothes & garments bought by you',
            'tags' => 'my purchases orders bought clothes shopping invoice receipt bill',
            'permission' => null,
            'icon' => 'bag',
        ],
        [
            'id' => 'attendance',
            'title' => 'Staff Attendance Kiosk',
            'category' => 'operations',
            'route' => route('admin.attendance.index'),
            'badge' => '20s QR',
            'badge_class' => 'bg-indigo-100 text-indigo-800',
            'icon_bg' => 'bg-indigo-100 text-indigo-700',
            'hover_border' => 'hover:border-indigo-400/50 hover:bg-indigo-50/40',
            'hover_text' => 'group-hover:text-indigo-700',
            'desc' => 'Dynamic QR anti-proxy scanner',
            'tags' => 'attendance qr scan staff punch anti-proxy kiosk camera',
            'admin_only' => true,
            'icon' => 'qrcode',
        ],

        // Analytics (Cloth Sales & Reports - Admin Only)
        [
            'id' => 'cloth_sales',
            'title' => 'Kitna & Kon Sa Cloth Bikha',
            'category' => 'analytics',
            'route' => route('admin.reports.cloth-sales'),
            'badge' => number_format($totalGarmentsSoldMonth) . ' pcs',
            'badge_class' => 'bg-primary-100 text-primary-800',
            'icon_bg' => 'bg-primary-100 text-primary-700',
            'hover_border' => 'hover:border-primary-400/50 hover:bg-primary-50/40',
            'hover_text' => 'group-hover:text-primary-600',
            'desc' => 'Garments sold, fabric sales & stock',
            'tags' => 'cloth sales kitna bikha kon sa bikha kapda saree suit fabric sales units report',
            'admin_only' => true,
            'icon' => 'chart',
        ],
        [
            'id' => 'cat_sales',
            'title' => 'Category Sales Share',
            'category' => 'analytics',
            'route' => route('admin.reports.categories'),
            'badge' => 'Dept %',
            'badge_class' => 'bg-purple-100 text-purple-800',
            'icon_bg' => 'bg-purple-100 text-purple-700',
            'hover_border' => 'hover:border-purple-400/50 hover:bg-purple-50/40',
            'hover_text' => 'group-hover:text-purple-700',
            'desc' => 'Sarees vs Suits vs Uniforms',
            'tags' => 'category volume department share bridal sarees punjabi suits kurtas',
            'admin_only' => true,
            'icon' => 'pie',
        ],
        [
            'id' => 'fabrics',
            'title' => 'Fabric Material Sales',
            'category' => 'analytics',
            'route' => route('admin.reports.fabrics'),
            'badge' => 'Textile',
            'badge_class' => 'bg-amber-100 text-amber-800',
            'icon_bg' => 'bg-amber-100 text-amber-800',
            'hover_border' => 'hover:border-amber-400/50 hover:bg-amber-50/40',
            'hover_text' => 'group-hover:text-amber-800',
            'desc' => 'Silk, Chanderi, Georgette, Cotton',
            'tags' => 'fabric pure silk cotton banarasi chanderi georgette velvet linen textile',
            'admin_only' => true,
            'icon' => 'scissors',
        ],
        [
            'id' => 'dead_stock',
            'title' => 'Dead Stock Analyzer',
            'category' => 'analytics',
            'route' => route('admin.inventory.dead-stock'),
            'badge' => 'Stagnant',
            'badge_class' => 'bg-amber-100 text-amber-800',
            'icon_bg' => 'bg-amber-100 text-amber-800',
            'hover_border' => 'hover:border-amber-400/50 hover:bg-amber-50/40',
            'hover_text' => 'group-hover:text-amber-800',
            'desc' => '30/60/90+ days slow inventory',
            'tags' => 'dead stock slow moving stagnant inventory capital trapped',
            'admin_only' => true,
            'icon' => 'archive',
        ],
        [
            'id' => 'wishlist',
            'title' => 'Wishlist Demand',
            'category' => 'analytics',
            'route' => route('admin.reports.wishlist'),
            'badge' => "{$wishlistsCount} Signals",
            'badge_class' => 'bg-pink-100 text-pink-700',
            'icon_bg' => 'bg-pink-100 text-pink-700',
            'hover_border' => 'hover:border-pink-400/50 hover:bg-pink-50/40',
            'hover_text' => 'group-hover:text-pink-600',
            'desc' => 'Customer favorite trends',
            'tags' => 'wishlist favorites demand customer wedding festival interest',
            'admin_only' => true,
            'icon' => 'heart',
        ],
        [
            'id' => 'abandoned_carts',
            'title' => 'Abandoned Carts',
            'category' => 'analytics',
            'route' => route('admin.reports.abandoned-carts'),
            'badge' => "{$abandonedCartsCount} Carts",
            'badge_class' => 'bg-emerald-100 text-emerald-800',
            'icon_bg' => 'bg-emerald-100 text-emerald-800',
            'hover_border' => 'hover:border-emerald-400/50 hover:bg-emerald-50/40',
            'hover_text' => 'group-hover:text-emerald-700',
            'desc' => 'Unchecked carts & recovery',
            'tags' => 'abandoned carts recovery follow up whatsapp pending cart checkout',
            'admin_only' => true,
            'icon' => 'cart',
        ],
        [
            'id' => 'tax',
            'title' => 'GST & Invoicing Tax',
            'category' => 'analytics',
            'route' => route('admin.reports.tax'),
            'badge' => '5% GST',
            'badge_class' => 'bg-indigo-100 text-indigo-800',
            'icon_bg' => 'bg-indigo-100 text-indigo-700',
            'hover_border' => 'hover:border-indigo-400/50 hover:bg-indigo-50/40',
            'hover_text' => 'group-hover:text-indigo-700',
            'desc' => 'CGST/SGST monthly CSV export',
            'tags' => 'gst tax invoice ca cgst sgst accounts punjab billing',
            'admin_only' => true,
            'icon' => 'receipt',
        ],
        [
            'id' => 'vip',
            'title' => 'Top VIP Customers',
            'category' => 'analytics',
            'route' => route('admin.reports.customers'),
            'badge' => '👑 VIP',
            'badge_class' => 'bg-amber-100 text-amber-800',
            'icon_bg' => 'bg-amber-100 text-amber-800',
            'hover_border' => 'hover:border-amber-400/50 hover:bg-amber-50/40',
            'hover_text' => 'group-hover:text-amber-800',
            'desc' => 'Top spenders & wedding buyers',
            'tags' => 'vip top customers high spend buyers repeat wedding trousseau',
            'admin_only' => true,
            'icon' => 'crown',
        ],

        // Config & HR (Admin Only)
        [
            'id' => 'categories_tree',
            'title' => 'Categories Tree',
            'category' => 'config',
            'route' => route('admin.categories.index'),
            'badge' => 'Tree',
            'badge_class' => 'bg-primary-100 text-primary-800',
            'icon_bg' => 'bg-primary-100 text-primary-700',
            'hover_border' => 'hover:border-primary-400/50 hover:bg-primary-50/40',
            'hover_text' => 'group-hover:text-primary-600',
            'desc' => 'Department structure & icons',
            'tags' => 'categories hierarchy subcategory organize sarees suits uniforms',
            'admin_only' => true,
            'icon' => 'layers',
        ],
        [
            'id' => 'banners',
            'title' => 'Banners & Sliders',
            'category' => 'config',
            'route' => route('admin.banners.index'),
            'badge' => 'Hero',
            'badge_class' => 'bg-indigo-100 text-indigo-800',
            'icon_bg' => 'bg-indigo-100 text-indigo-700',
            'hover_border' => 'hover:border-indigo-400/50 hover:bg-indigo-50/40',
            'hover_text' => 'group-hover:text-indigo-700',
            'desc' => 'Home carousel & offers',
            'tags' => 'banners slider hero graphics home promotion diwali eid festive',
            'admin_only' => true,
            'icon' => 'photo',
        ],
        [
            'id' => 'coupons',
            'title' => 'Discount Coupons',
            'category' => 'config',
            'route' => route('admin.coupons.index'),
            'badge' => 'Promo',
            'badge_class' => 'bg-amber-100 text-amber-800',
            'icon_bg' => 'bg-amber-100 text-amber-800',
            'hover_border' => 'hover:border-amber-400/50 hover:bg-amber-50/40',
            'hover_text' => 'group-hover:text-amber-800',
            'desc' => 'Festive promo codes',
            'tags' => 'coupons discount promo code festive sale voucher offer',
            'admin_only' => true,
            'icon' => 'tag',
        ],
        [
            'id' => 'staff_users',
            'title' => 'Staff Accounts & Roles',
            'category' => 'config',
            'route' => route('admin.staff.index'),
            'badge' => 'Users',
            'badge_class' => 'bg-primary-100 text-primary-700',
            'icon_bg' => 'bg-primary-100 text-primary-700',
            'hover_border' => 'hover:border-primary-400/50 hover:bg-primary-50/40',
            'hover_text' => 'group-hover:text-primary-600',
            'desc' => 'Roles & cashier logins',
            'tags' => 'staff accounts roles permissions employees cashier desk',
            'admin_only' => true,
            'icon' => 'users',
        ],
        [
            'id' => 'staff_salaries',
            'title' => 'Staff Salary & Payroll',
            'category' => 'config',
            'route' => route('admin.staff.salaries.index'),
            'badge' => '₹ Payroll',
            'badge_class' => 'bg-emerald-100 text-emerald-800',
            'icon_bg' => 'bg-emerald-100 text-emerald-800',
            'hover_border' => 'hover:border-emerald-400/50 hover:bg-emerald-50/40',
            'hover_text' => 'group-hover:text-emerald-700',
            'desc' => 'Attendance pay slips & bank NEFT',
            'tags' => 'salary payroll payslip pay staff attendance bonus deduction slip',
            'admin_only' => true,
            'icon' => 'cash',
        ],
        [
            'id' => 'staff_leaves',
            'title' => 'Staff Leaves & Vacation',
            'category' => 'config',
            'route' => route('admin.staff.leaves.index'),
            'badge' => 'Chutti',
            'badge_class' => 'bg-pink-100 text-pink-800',
            'icon_bg' => 'bg-pink-100 text-pink-800',
            'hover_border' => 'hover:border-pink-400/50 hover:bg-pink-50/40',
            'hover_text' => 'group-hover:text-pink-700',
            'desc' => 'Casual, sick & wedding chutti',
            'tags' => 'vacation leave chutti holiday casual sick festival wedding staff',
            'admin_only' => true,
            'icon' => 'calendar',
        ],
        [
            'id' => 'staff_advances',
            'title' => 'Staff Advance (Peshgi)',
            'category' => 'config',
            'route' => route('admin.staff.advances.index'),
            'badge' => 'Peshgi',
            'badge_class' => 'bg-amber-100 text-amber-800',
            'icon_bg' => 'bg-amber-100 text-amber-800',
            'hover_border' => 'hover:border-amber-400/50 hover:bg-amber-50/40',
            'hover_text' => 'group-hover:text-amber-800',
            'desc' => 'Festival & emergency advance khata',
            'tags' => 'advance peshgi khata credit loan borrow emergency festival staff',
            'admin_only' => true,
            'icon' => 'credit-card',
        ],
        [
            'id' => 'pages',
            'title' => 'CMS Policy Pages',
            'category' => 'config',
            'route' => route('admin.pages.index'),
            'badge' => 'CMS',
            'badge_class' => 'bg-slate-100 text-slate-700',
            'icon_bg' => 'bg-slate-100 text-slate-800',
            'hover_border' => 'hover:border-slate-400/50 hover:bg-slate-50',
            'hover_text' => 'group-hover:text-primary-600',
            'desc' => 'About, 7-day return, terms',
            'tags' => 'cms pages policy about us terms return 7 days shipping content',
            'admin_only' => true,
            'icon' => 'document',
        ],
        [
            'id' => 'settings',
            'title' => 'Store Settings & COD',
            'category' => 'config',
            'route' => route('admin.settings.index'),
            'badge' => 'Config',
            'badge_class' => 'bg-primary-100 text-primary-700',
            'icon_bg' => 'bg-primary-100 text-primary-700',
            'hover_border' => 'hover:border-primary-400/50 hover:bg-primary-50/40',
            'hover_text' => 'group-hover:text-primary-600',
            'desc' => 'Address, COD limits & contact',
            'tags' => 'settings store cod rules shipping address contact phone gst threshold',
            'admin_only' => true,
            'icon' => 'cog',
        ],
        [
            'id' => 'activity',
            'title' => 'Staff Activity Audit',
            'category' => 'config',
            'route' => route('admin.activity.index'),
            'badge' => 'Security',
            'badge_class' => 'bg-slate-100 text-slate-700',
            'icon_bg' => 'bg-slate-100 text-slate-800',
            'hover_border' => 'hover:border-slate-400/50 hover:bg-slate-50',
            'hover_text' => 'group-hover:text-primary-600',
            'desc' => 'IP & change history log',
            'tags' => 'activity audit log staff security track changes ip history',
            'admin_only' => true,
            'icon' => 'shield',
        ],
    ];

    $visibleCards = collect($allCards)->filter(function($c) use ($user) {
        if ($user->isAdmin()) {
            return true;
        }
        if (!empty($c['admin_only'])) {
            return false;
        }
        if (!empty($c['permission'])) {
            return $user->hasPermission($c['permission']);
        }
        return false;
    })->values();

    $analyticsCount = $visibleCards->where('category', 'analytics')->count();
    $operationsCount = $visibleCards->where('category', 'operations')->count();
    $configCount = $visibleCards->where('category', 'config')->count();
    $totalCount = $visibleCards->count();
@endphp

<div 
    class="space-y-4"
    x-data="{
        search: '',
        activeTab: 'all',
        viewMode: 'grid', // 'grid' or 'dense'
        matches(title, category, tags) {
            if (this.activeTab !== 'all' && this.activeTab !== category) {
                return false;
            }
            if (!this.search.trim()) {
                return true;
            }
            const q = this.search.toLowerCase().trim();
            return title.toLowerCase().includes(q) || tags.toLowerCase().includes(q);
        }
    }"
>
    {{-- Compact Header & Controls Bar --}}
    <div class="glass-card rounded-2xl p-4 sm:p-5 border border-white/80 bg-gradient-to-r from-blush/40 via-white to-cream-100/60 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-primary-100 text-primary-800">
                    Control Center
                </span>
                <span class="text-[11px] text-plum/50 font-medium">Bombay Fashion Amritsar</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-serif font-bold text-plum mt-0.5">
                Store Operations Hub
            </h1>
            <p class="text-[11px] text-plum/70">
                Showroom aur online store ke fast 1-click launchpad.
            </p>
        </div>

        {{-- Search & Density Switcher --}}
        <div class="flex items-center gap-2 w-full md:w-auto">
            <div class="relative flex-1 md:w-72">
                <svg class="w-3.5 h-3.5 absolute left-3 top-2.5 text-plum/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input 
                    type="text" 
                    x-model="search" 
                    placeholder="Search saree, pos, bill, tax, attendance..." 
                    class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-plum/20 bg-white text-xs text-plum shadow-inner focus:ring-primary focus:border-primary placeholder:text-plum/40"
                >
                <button 
                    type="button"
                    x-show="search.length > 0"
                    @click="search = ''"
                    class="absolute right-2.5 top-2 text-plum/40 hover:text-plum text-xs"
                >
                    &times;
                </button>
            </div>

            {{-- Grid vs Dense List Toggle --}}
            <div class="flex items-center bg-white rounded-xl border border-plum/15 p-0.5 shrink-0 shadow-xs">
                <button 
                    type="button" 
                    @click="viewMode = 'grid'" 
                    :class="viewMode === 'grid' ? 'bg-primary-500 text-white shadow-xs' : 'text-plum/60 hover:text-plum'"
                    class="p-1.5 rounded-lg transition"
                    title="Compact Grid"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </button>
                <button 
                    type="button" 
                    @click="viewMode = 'dense'" 
                    :class="viewMode === 'dense' ? 'bg-primary-500 text-white shadow-xs' : 'text-plum/60 hover:text-plum'"
                    class="p-1.5 rounded-lg transition"
                    title="Ultra-compact List"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Filter Category Tabs (Compact & Dynamic based on visible items) --}}
    <div class="flex flex-wrap gap-1.5 items-center bg-white/80 backdrop-blur-md p-1.5 rounded-xl border border-primary-100/60 shadow-xs text-xs">
        <button 
            type="button"
            @click="activeTab = 'all'"
            :class="activeTab === 'all' ? 'bg-primary-500 text-white font-bold shadow-xs' : 'text-plum/70 hover:bg-white hover:text-primary-600'"
            class="px-3 py-1.5 rounded-lg transition cursor-pointer text-[11px]"
        >
            🌟 All ({{ $totalCount }})
        </button>

        @if($analyticsCount > 0)
            <button 
                type="button"
                @click="activeTab = 'analytics'"
                :class="activeTab === 'analytics' ? 'bg-primary-500 text-white font-bold shadow-xs' : 'text-plum/70 hover:bg-white hover:text-primary-600'"
                class="px-3 py-1.5 rounded-lg transition cursor-pointer text-[11px]"
            >
                📊 Cloth Sales &amp; Reports ({{ $analyticsCount }})
            </button>
        @endif

        @if($operationsCount > 0)
            <button 
                type="button"
                @click="activeTab = 'operations'"
                :class="activeTab === 'operations' ? 'bg-primary-500 text-white font-bold shadow-xs' : 'text-plum/70 hover:bg-white hover:text-primary-600'"
                class="px-3 py-1.5 rounded-lg transition cursor-pointer text-[11px]"
            >
                🛍️ POS &amp; Operations ({{ $operationsCount }})
            </button>
        @endif

        @if($configCount > 0)
            <button 
                type="button"
                @click="activeTab = 'config'"
                :class="activeTab === 'config' ? 'bg-primary-500 text-white font-bold shadow-xs' : 'text-plum/70 hover:bg-white hover:text-primary-600'"
                class="px-3 py-1.5 rounded-lg transition cursor-pointer text-[11px]"
            >
                ⚙️ Staff HR &amp; Config ({{ $configCount }})
            </button>
        @endif
    </div>

    {{-- VIEW 1: COMPACT GRID (Only Permitted Cards) --}}
    <div 
        x-show="viewMode === 'grid'"
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-2.5"
    >
        @foreach($visibleCards as $card)
            <a 
                href="{{ $card['route'] }}"
                x-show="matches('{{ addslashes($card['title']) }}', '{{ $card['category'] }}', '{{ addslashes($card['tags']) }}')"
                class="group bg-white rounded-xl p-3 border border-plum/10 shadow-xs hover:shadow-md transition duration-150 flex flex-col justify-between {{ $card['hover_border'] }}"
            >
                <div class="flex items-start justify-between gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $card['icon_bg'] }}">
                        @if($card['icon'] === 'pos')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        @elseif($card['icon'] === 'clock')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif($card['icon'] === 'bag' || $card['icon'] === 'chart')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        @elseif($card['icon'] === 'cube')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        @elseif($card['icon'] === 'alert')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        @elseif($card['icon'] === 'clipboard')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        @elseif($card['icon'] === 'star')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        @elseif($card['icon'] === 'mail')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        @elseif($card['icon'] === 'qrcode')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        @elseif($card['icon'] === 'pie')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        @elseif($card['icon'] === 'scissors')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        @elseif($card['icon'] === 'heart')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        @elseif($card['icon'] === 'cart')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        @elseif($card['icon'] === 'receipt')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>
                        @elseif($card['icon'] === 'crown')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        @elseif($card['icon'] === 'layers')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        @elseif($card['icon'] === 'photo')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @elseif($card['icon'] === 'tag')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        @elseif($card['icon'] === 'users')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        @elseif($card['icon'] === 'cash')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        @elseif($card['icon'] === 'calendar')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @elseif($card['icon'] === 'credit-card')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @elseif($card['icon'] === 'document')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        @elseif($card['icon'] === 'cog')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                    </div>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold shrink-0 {{ $card['badge_class'] }}">
                        {{ $card['badge'] }}
                    </span>
                </div>
                <div class="mt-2">
                    <h3 class="font-bold text-plum text-xs transition truncate {{ $card['hover_text'] }}">
                        {{ $card['title'] }}
                    </h3>
                    <p class="text-[10px] text-plum/60 truncate mt-0.5">
                        {{ $card['desc'] }}
                    </p>
                </div>
            </a>
        @endforeach
    </div>

    {{-- VIEW 2: ULTRA-COMPACT DENSE LIST (Only Permitted Items) --}}
    <div 
        x-show="viewMode === 'dense'"
        class="bg-white rounded-2xl border border-plum/10 shadow-xs divide-y divide-plum/5 overflow-hidden"
    >
        @foreach($visibleCards as $item)
            <a 
                href="{{ $item['route'] }}"
                x-show="matches('{{ addslashes($item['title']) }}', '{{ $item['category'] }}', '{{ addslashes($item['tags']) }}')"
                class="flex items-center justify-between px-4 py-2.5 hover:bg-primary-50/50 transition group"
            >
                <div class="flex items-center space-x-3 min-w-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary-500 shrink-0"></span>
                    <span class="text-xs font-bold text-plum group-hover:text-primary-600 transition truncate">{{ $item['title'] }}</span>
                    <span class="text-[11px] text-plum/50 truncate hidden md:inline">{{ $item['desc'] }}</span>
                </div>
                <div class="flex items-center space-x-2 shrink-0">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $item['badge_class'] }}">{{ $item['badge'] }}</span>
                    <span class="text-plum/30 group-hover:text-primary-600 group-hover:translate-x-1 transition text-xs font-bold">&rarr;</span>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
