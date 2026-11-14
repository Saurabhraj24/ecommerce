<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Wishlist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * General Financial & Orders Overview Report.
     */
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $ordersQuery = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $totalRevenue = (clone $ordersQuery)->where('payment_status', 'paid')->sum('grand_total');
        $totalOrders = (clone $ordersQuery)->count();
        $deliveredCount = (clone $ordersQuery)->where('order_status', 'delivered')->count();
        $codSales = (clone $ordersQuery)->where('payment_method', 'cod')->where('payment_status', 'paid')->sum('grand_total');
        $razorpaySales = (clone $ordersQuery)->whereIn('payment_method', ['razorpay', 'upi', 'card'])->where('payment_status', 'paid')->sum('grand_total');

        // Top products during period
        $topProducts = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
              ->where('payment_status', 'paid');
        })
        ->select('product_id', 'product_name', 'product_sku', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_price) as total_amount'))
        ->groupBy('product_id', 'product_name', 'product_sku')
        ->orderByDesc('total_qty')
        ->take(10)
        ->get();

        return view('admin.reports.index', compact(
            'startDate',
            'endDate',
            'totalRevenue',
            'totalOrders',
            'deliveredCount',
            'codSales',
            'razorpaySales',
            'topProducts'
        ));
    }

    /**
     * Dedicated Cloth & Garment Sales Breakdown ("Kitna Cloth Bikha or Kon Sa Bikha").
     */
    public function clothSales(Request $request): View
    {
        $period = $request->input('period', 'this_month');

        [$startDate, $endDate] = $this->resolveDateRange($period, $request->input('start_date'), $request->input('end_date'));

        $itemsQuery = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
              ->where('payment_status', 'paid');
        });

        if ($request->filled('category_id')) {
            $categoryId = $request->integer('category_id');
            $itemsQuery->whereHas('product', fn($pq) => $pq->where('category_id', $categoryId));
        }

        if ($request->filled('fabric')) {
            $fabric = $request->input('fabric');
            $itemsQuery->whereHas('product', fn($pq) => $pq->where('fabric', $fabric));
        }

        if ($request->filled('season')) {
            $season = $request->input('season');
            $itemsQuery->whereHas('product', fn($pq) => $pq->where('season', $season));
        }

        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $itemsQuery->where(function ($q) use ($keyword) {
                $q->where('product_name', 'like', "%{$keyword}%")
                  ->orWhere('product_sku', 'like', "%{$keyword}%");
            });
        }

        // Summary KPI Metrics ("Kitna cloth bikha")
        $totalPiecesSold = (clone $itemsQuery)->sum('quantity');
        $totalRevenue = (clone $itemsQuery)->sum('total_price');
        $avgSellingPrice = $totalPiecesSold > 0 ? round($totalRevenue / $totalPiecesSold, 2) : 0;
        $uniqueProductsCount = (clone $itemsQuery)->distinct('product_id')->count('product_id');

        // Today and This Month quick counter for reference
        $todayPiecesSold = OrderItem::whereHas('order', function ($q) {
            $q->whereDate('created_at', Carbon::today())->where('payment_status', 'paid');
        })->sum('quantity');

        $monthPiecesSold = OrderItem::whereHas('order', function ($q) {
            $q->where('created_at', '>=', Carbon::now()->startOfMonth())->where('payment_status', 'paid');
        })->sum('quantity');

        // Detailed itemized breakdown table ("Kon sa bikha")
        $clothSales = (clone $itemsQuery)
            ->with(['product.primaryImage', 'product.category', 'variant'])
            ->select(
                'product_id',
                'product_name',
                'product_sku',
                'variant_details',
                DB::raw('SUM(quantity) as total_units_sold'),
                DB::raw('SUM(total_price) as total_gross_sales'),
                DB::raw('AVG(unit_price) as avg_unit_price')
            )
            ->groupBy('product_id', 'product_name', 'product_sku', 'variant_details')
            ->orderByDesc('total_units_sold')
            ->paginate(20)
            ->withQueryString();

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $fabrics = Product::whereNotNull('fabric')->where('fabric', '!=', '')->distinct()->pluck('fabric');

        return view('admin.reports.cloth-sales', compact(
            'period',
            'startDate',
            'endDate',
            'totalPiecesSold',
            'totalRevenue',
            'avgSellingPrice',
            'uniqueProductsCount',
            'todayPiecesSold',
            'monthPiecesSold',
            'clothSales',
            'categories',
            'fabrics'
        ));
    }

    /**
     * Export Itemized Cloth Sales Report to CSV.
     */
    public function exportClothSalesCsv(Request $request): StreamedResponse
    {
        $period = $request->input('period', 'this_month');
        [$startDate, $endDate] = $this->resolveDateRange($period, $request->input('start_date'), $request->input('end_date'));

        $itemsQuery = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
              ->where('payment_status', 'paid');
        });

        if ($request->filled('category_id')) {
            $categoryId = $request->integer('category_id');
            $itemsQuery->whereHas('product', fn($pq) => $pq->where('category_id', $categoryId));
        }

        if ($request->filled('fabric')) {
            $fabric = $request->input('fabric');
            $itemsQuery->whereHas('product', fn($pq) => $pq->where('fabric', $fabric));
        }

        $items = $itemsQuery->with(['product.category'])
            ->select(
                'product_id',
                'product_name',
                'product_sku',
                'variant_details',
                DB::raw('SUM(quantity) as total_units_sold'),
                DB::raw('SUM(total_price) as total_gross_sales'),
                DB::raw('AVG(unit_price) as avg_unit_price')
            )
            ->groupBy('product_id', 'product_name', 'product_sku', 'variant_details')
            ->orderByDesc('total_units_sold')
            ->get();

        $filename = "bombay_fashion_cloth_sales_{$startDate}_to_{$endDate}.csv";

        return response()->streamDownload(function () use ($items) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Product Name',
                'SKU',
                'Category',
                'Fabric',
                'Variant Details',
                'Pieces Sold (Units)',
                'Gross Sales (INR)',
                'Average Selling Price (INR)',
                'Current Available Stock',
            ]);

            foreach ($items as $item) {
                $categoryName = $item->product?->category?->name ?? 'Uncategorized';
                $fabric = $item->product?->fabric ?? 'Standard';
                $stock = $item->product?->stock ?? 0;

                fputcsv($handle, [
                    $item->product_name,
                    $item->product_sku,
                    $categoryName,
                    $fabric,
                    $item->variant_details ?: 'Default',
                    $item->total_units_sold,
                    number_format((float)$item->total_gross_sales, 2, '.', ''),
                    number_format((float)$item->avg_unit_price, 2, '.', ''),
                    $stock,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Category & Department Sales Volume Analytics.
     */
    public function categorySales(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $categories = Category::withCount('products')
            ->get()
            ->map(function ($category) use ($startDate, $endDate) {
                $orderItems = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                      ->where('payment_status', 'paid');
                })->whereHas('product', function ($pq) use ($category) {
                    $pq->where('category_id', $category->id);
                });

                $category->units_sold = (int) (clone $orderItems)->sum('quantity');
                $category->gross_revenue = (float) (clone $orderItems)->sum('total_price');
                $category->avg_ticket = $category->units_sold > 0 ? round($category->gross_revenue / $category->units_sold, 2) : 0;
                $category->current_inventory = (int) Product::where('category_id', $category->id)->sum('stock');

                return $category;
            })
            ->sortByDesc('units_sold');

        $totalUnits = $categories->sum('units_sold');
        $totalRevenue = $categories->sum('gross_revenue');

        return view('admin.reports.categories', compact('categories', 'startDate', 'endDate', 'totalUnits', 'totalRevenue'));
    }

    /**
     * Fabric & Material Performance (Silk, Cotton, Georgette, Chanderi, etc.).
     */
    public function fabricSales(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $fabricStats = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('orders.payment_status', 'paid')
            ->whereNotNull('products.fabric')
            ->where('products.fabric', '!=', '')
            ->select(
                'products.fabric',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.total_price) as gross_revenue'),
                DB::raw('COUNT(DISTINCT products.id) as distinct_styles')
            )
            ->groupBy('products.fabric')
            ->orderByDesc('units_sold')
            ->get();

        $totalFabricPieces = $fabricStats->sum('units_sold');
        $totalFabricRevenue = $fabricStats->sum('gross_revenue');

        return view('admin.reports.fabrics', compact('fabricStats', 'startDate', 'endDate', 'totalFabricPieces', 'totalFabricRevenue'));
    }

    /**
     * Customer Wishlist & Anticipated Demand Insights.
     */
    public function wishlistInsights(): View
    {
        $wishlistedProducts = Wishlist::with(['product.category', 'product.primaryImage', 'product.variants'])
            ->select('product_id', DB::raw('COUNT(*) as wishlists_count'))
            ->groupBy('product_id')
            ->orderByDesc('wishlists_count')
            ->paginate(15);

        return view('admin.reports.wishlist', compact('wishlistedProducts'));
    }

    /**
     * Abandoned Cart Recovery & Unchecked Carts Tracker.
     */
    public function abandonedCarts(): View
    {
        $abandonedCarts = Cart::with(['user', 'items.product.primaryImage', 'items.variant'])
            ->has('items')
            ->where('updated_at', '<', now()->subHours(1))
            ->latest('updated_at')
            ->paginate(15);

        return view('admin.reports.abandoned-carts', compact('abandonedCarts'));
    }

    /**
     * GST 5% & Indian Apparel Tax Compliance Report.
     */
    public function taxReport(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $ordersQuery = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('payment_status', 'paid');

        $totalOrders = (clone $ordersQuery)->count();
        $totalTaxableSubtotal = (clone $ordersQuery)->sum('subtotal');
        $totalDiscount = (clone $ordersQuery)->sum('discount_amount');
        $netTaxable = max(0, $totalTaxableSubtotal - $totalDiscount);
        $totalTax = (clone $ordersQuery)->sum('tax_amount');
        $cgst = round($totalTax / 2, 2);
        $sgst = round($totalTax / 2, 2);
        $totalShipping = (clone $ordersQuery)->sum('shipping_charge');
        $grandTotal = (clone $ordersQuery)->sum('grand_total');

        $orders = (clone $ordersQuery)->latest()->paginate(25)->withQueryString();

        return view('admin.reports.tax', compact(
            'startDate',
            'endDate',
            'totalOrders',
            'totalTaxableSubtotal',
            'totalDiscount',
            'netTaxable',
            'totalTax',
            'cgst',
            'sgst',
            'totalShipping',
            'grandTotal',
            'orders'
        ));
    }

    /**
     * Export GST Tax Invoice Report to CSV.
     */
    public function exportTaxCsv(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $orders = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('payment_status', 'paid')
            ->latest()
            ->get();

        $filename = "bombay_fashion_gst_tax_{$startDate}_to_{$endDate}.csv";

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Invoice / Order Number',
                'Invoice Date',
                'Customer Name',
                'Customer Phone',
                'Taxable Subtotal (INR)',
                'Discount (INR)',
                'Net Taxable (INR)',
                'CGST 2.5% (INR)',
                'SGST 2.5% (INR)',
                'Total GST 5% (INR)',
                'Shipping Charge (INR)',
                'Invoice Grand Total (INR)',
                'Payment Mode',
            ]);

            foreach ($orders as $order) {
                $netTaxable = max(0, $order->subtotal - $order->discount_amount);
                $tax = (float)$order->tax_amount;
                $cgst = round($tax / 2, 2);
                $sgst = round($tax / 2, 2);

                fputcsv($handle, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d'),
                    $order->customer_name,
                    $order->customer_phone,
                    number_format((float)$order->subtotal, 2, '.', ''),
                    number_format((float)$order->discount_amount, 2, '.', ''),
                    number_format($netTaxable, 2, '.', ''),
                    number_format($cgst, 2, '.', ''),
                    number_format($sgst, 2, '.', ''),
                    number_format($tax, 2, '.', ''),
                    number_format((float)$order->shipping_charge, 2, '.', ''),
                    number_format((float)$order->grand_total, 2, '.', ''),
                    strtoupper($order->payment_method),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Top Retail Buyers & VIP Customer Spending Insights.
     */
    public function customerInsights(): View
    {
        $topCustomers = User::whereHas('role', fn($q) => $q->where('name', 'user'))
            ->whereHas('orders', fn($q) => $q->where('payment_status', 'paid'))
            ->withCount(['orders' => fn($q) => $q->where('payment_status', 'paid')])
            ->withSum(['orders' => fn($q) => $q->where('payment_status', 'paid')], 'grand_total')
            ->orderByDesc('orders_sum_grand_total')
            ->paginate(20);

        return view('admin.reports.customers', compact('topCustomers'));
    }

    /**
     * Standard Date Range Filter Helper.
     */
    private function resolveDateRange(string $period, ?string $start, ?string $end): array
    {
        return match ($period) {
            'today' => [Carbon::today()->toDateString(), Carbon::today()->toDateString()],
            'yesterday' => [Carbon::yesterday()->toDateString(), Carbon::yesterday()->toDateString()],
            'this_week' => [Carbon::now()->startOfWeek()->toDateString(), Carbon::now()->toDateString()],
            'last_month' => [
                Carbon::now()->subMonth()->startOfMonth()->toDateString(),
                Carbon::now()->subMonth()->endOfMonth()->toDateString(),
            ],
            'this_year' => [Carbon::now()->startOfYear()->toDateString(), Carbon::now()->toDateString()],
            'all_time' => ['2020-01-01', Carbon::now()->toDateString()],
            'custom' => [
                $start ?: Carbon::now()->startOfMonth()->toDateString(),
                $end ?: Carbon::now()->toDateString(),
            ],
            default => [Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->toDateString()],
        };
    }

    /**
     * Legacy Financial Export CSV.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $orders = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with(['items'])
            ->latest()
            ->get();

        $filename = "sales_report_{$startDate}_to_{$endDate}.csv";

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Order Number',
                'Date',
                'Customer Name',
                'Customer Phone',
                'Payment Method',
                'Payment Status',
                'Order Status',
                'Subtotal (INR)',
                'Discount (INR)',
                'Tax (INR)',
                'Shipping (INR)',
                'Grand Total (INR)',
                'Items Count'
            ]);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->customer_name,
                    $order->customer_phone,
                    strtoupper($order->payment_method),
                    ucfirst($order->payment_status),
                    ucfirst($order->order_status),
                    number_format($order->subtotal, 2, '.', ''),
                    number_format($order->discount_amount, 2, '.', ''),
                    number_format($order->tax_amount, 2, '.', ''),
                    number_format($order->shipping_charge, 2, '.', ''),
                    number_format($order->grand_total, 2, '.', ''),
                    $order->items->sum('quantity'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
