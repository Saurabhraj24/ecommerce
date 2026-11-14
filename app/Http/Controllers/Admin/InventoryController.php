<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'primaryImage', 'allVariants']);

        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('filter')) {
            if ($request->input('filter') === 'low') {
                $query->where('stock', '<=', 5);
            } elseif ($request->input('filter') === 'out') {
                $query->where('stock', '<=', 0);
            }
        }

        $products = $query->paginate(15)->withQueryString();

        return view('admin.inventory.index', compact('products'));
    }

    public function adjust(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'adjustment_type' => 'required|in:add,subtract,set',
            'quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
        ]);

        $quantity = (int) $validated['quantity'];
        $type = $validated['adjustment_type'];
        $reason = $validated['reason'];

        if (!empty($validated['product_variant_id'])) {
            $variant = ProductVariant::findOrFail($validated['product_variant_id']);
            $old = $variant->stock;

            $new = match ($type) {
                'add' => $old + $quantity,
                'subtract' => max(0, $old - $quantity),
                'set' => $quantity,
            };

            $variant->update(['stock' => $new]);

            StockLog::create([
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'user_id' => auth()->id(),
                'type' => 'manual_adjustment',
                'quantity_change' => $new - $old,
                'old_quantity' => $old,
                'new_quantity' => $new,
                'reason' => $reason,
            ]);
        } else {
            $product = Product::findOrFail($validated['product_id']);
            $old = $product->stock;

            $new = match ($type) {
                'add' => $old + $quantity,
                'subtract' => max(0, $old - $quantity),
                'set' => $quantity,
            };

            $product->update(['stock' => $new]);

            StockLog::create([
                'product_id' => $product->id,
                'product_variant_id' => null,
                'user_id' => auth()->id(),
                'type' => 'manual_adjustment',
                'quantity_change' => $new - $old,
                'old_quantity' => $old,
                'new_quantity' => $new,
                'reason' => $reason,
            ]);
        }

        ActivityLog::log('stock_adjusted', Product::class, $validated['product_id'], [
            'reason' => $reason,
            'change' => "{$type} {$quantity}",
        ]);

        return back()->with('success', 'Stock adjustment recorded successfully.');
    }

    public function logs(Request $request): View
    {
        $query = StockLog::with(['product', 'variant', 'user']);

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        $logs = $query->latest()->paginate(25)->withQueryString();

        return view('admin.inventory.logs', compact('logs'));
    }

    /**
     * Low Stock Reorder & Procurement Alerts.
     */
    public function reorderAlerts(Request $request): View
    {
        $products = Product::where('stock', '<=', 5)
            ->with(['category', 'primaryImage', 'variants' => fn($q) => $q->where('stock', '<=', 5)])
            ->orderBy('stock')
            ->paginate(20);

        $outOfStockCount = Product::where('stock', '<=', 0)->count();
        $lowStockCount = Product::where('stock', '>', 0)->where('stock', '<=', 5)->count();

        return view('admin.inventory.reorder', compact('products', 'outOfStockCount', 'lowStockCount'));
    }

    /**
     * Slow-Moving & Dead Stock Analyzer (No sales in 30+ days).
     */
    public function deadStock(Request $request): View
    {
        $days = $request->integer('days', 30);
        $cutoffDate = now()->subDays($days);

        // Products with stock > 0 that have not appeared in any paid OrderItem since cutoffDate
        $activeSoldProductIds = \App\Models\OrderItem::whereHas('order', function ($q) use ($cutoffDate) {
            $q->where('created_at', '>=', $cutoffDate)->where('payment_status', 'paid');
        })->whereNotNull('product_id')->distinct()->pluck('product_id');

        $deadProducts = Product::where('stock', '>', 0)
            ->whereNotIn('id', $activeSoldProductIds)
            ->with(['category', 'primaryImage'])
            ->orderByDesc('stock')
            ->paginate(20)
            ->withQueryString();

        $totalDeadUnits = (int) Product::where('stock', '>', 0)->whereNotIn('id', $activeSoldProductIds)->sum('stock');
        $deadCapitalLocked = (float) Product::where('stock', '>', 0)->whereNotIn('id', $activeSoldProductIds)->selectRaw('SUM(stock * price) as val')->value('val');

        return view('admin.inventory.dead-stock', compact('deadProducts', 'days', 'totalDeadUnits', 'deadCapitalLocked'));
    }
}
