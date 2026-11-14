<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Real DB metrics
        $todayOrders = Order::whereDate('created_at', $today);
        $todaySales = (clone $todayOrders)->where('payment_status', 'paid')->sum('grand_total');
        $todayOrdersCount = (clone $todayOrders)->count();

        $monthOrders = Order::where('created_at', '>=', $thisMonth);
        $monthSales = (clone $monthOrders)->where('payment_status', 'paid')->sum('grand_total');
        $monthOrdersCount = (clone $monthOrders)->count();

        $pendingOrdersCount = Order::where('order_status', 'pending')->count();
        $deliveredOrdersCount = Order::where('order_status', 'delivered')->count();

        // Low stock count (products or variants with stock <= 5)
        $lowStockProducts = Product::active()->where('stock', '<=', 5)->count();
        $lowStockVariants = ProductVariant::where('is_active', true)->where('stock', '<=', 5)->count();
        $lowStockTotal = $lowStockProducts + $lowStockVariants;

        // Total products and customers
        $totalProducts = Product::active()->count();
        $totalCustomers = User::whereHas('role', fn($q) => $q->where('name', 'user'))->count();

        // Orders by status breakdown
        $ordersByStatus = Order::select('order_status', DB::raw('count(*) as total'))
            ->groupBy('order_status')
            ->pluck('total', 'order_status')
            ->toArray();

        // Recent 8 orders
        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->take(8)
            ->get();

        // Top 5 selling products from real order items
        $topProducts = OrderItem::select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(total_price) as total_revenue'))
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // 7-day sales trend for real DB chart
        $salesTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $daySales = Order::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('grand_total');

            $salesTrend[] = [
                'date' => $date->format('d M'),
                'amount' => (float) $daySales,
            ];
        }

        // Total garments/pieces sold metrics ("Kitna cloth bikha")
        $totalClothesSoldMonth = OrderItem::whereHas('order', fn($q) => $q->where('created_at', '>=', $thisMonth)->where('payment_status', 'paid'))->sum('quantity');
        $totalClothesSoldToday = OrderItem::whereHas('order', fn($q) => $q->whereDate('created_at', $today)->where('payment_status', 'paid'))->sum('quantity');

        // Staff attendance & personal purchases for staff desk view
        $currentUser = Auth::user();
        $canViewRevenue = $currentUser->isAdmin();
        $todayAttendance = \App\Models\Attendance::where('user_id', $currentUser->id)
            ->where('date', $today->toDateString())
            ->first();

        $cleanPhone = preg_replace('/[^0-9]/', '', (string) ($currentUser->phone ?? ''));
        $staffPurchasesCount = Order::where(function ($q) use ($currentUser, $cleanPhone) {
            $q->where('user_id', $currentUser->id)
              ->orWhere('customer_email', $currentUser->email);
            if (!empty($currentUser->phone)) {
                $q->orWhere('customer_phone', $currentUser->phone);
            }
            if (!empty($cleanPhone) && strlen($cleanPhone) >= 10) {
                $q->orWhere('customer_phone', 'like', "%{$cleanPhone}%");
            }
        })
        ->where(function ($q) use ($currentUser) {
            $q->where('customer_email', '!=', 'walkin@bombayfashion.com')
              ->orWhere('user_id', $currentUser->id);
        })
        ->count();

        return view('admin.dashboard', compact(
            'todaySales',
            'todayOrdersCount',
            'monthSales',
            'monthOrdersCount',
            'pendingOrdersCount',
            'deliveredOrdersCount',
            'lowStockTotal',
            'totalProducts',
            'totalCustomers',
            'ordersByStatus',
            'recentOrders',
            'topProducts',
            'salesTrend',
            'canViewRevenue',
            'totalClothesSoldMonth',
            'totalClothesSoldToday',
            'todayAttendance',
            'staffPurchasesCount'
        ));
    }

    /**
     * All-in-One Operations Hub & Quick Launchpad.
     */
    public function hub(): View
    {
        $pendingOrders = Order::where('order_status', 'pending')->count();
        $lowStockCount = Product::where('stock', '<=', 5)->count();
        $unreadMessages = ContactMessage::unread()->count();
        $pendingReviews = Review::where('is_approved', false)->count();
        $totalProducts = Product::count();
        $totalCustomers = User::whereHas('role', fn($q) => $q->where('name', 'user'))->count();
        $totalGarmentsSoldMonth = OrderItem::whereHas('order', fn($q) => $q->where('created_at', '>=', Carbon::now()->startOfMonth())->where('payment_status', 'paid'))->sum('quantity');
        $abandonedCartsCount = Cart::has('items')->where('updated_at', '<', now()->subHours(1))->count();
        $wishlistsCount = Wishlist::count();

        return view('admin.hub', compact(
            'pendingOrders',
            'lowStockCount',
            'unreadMessages',
            'pendingReviews',
            'totalProducts',
            'totalCustomers',
            'totalGarmentsSoldMonth',
            'abandonedCartsCount',
            'wishlistsCount'
        ));
    }
}
