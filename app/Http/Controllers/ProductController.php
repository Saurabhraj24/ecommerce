<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::active()
            ->with(['category', 'primaryImage', 'images', 'variants']);

        // Filter by Category
        if ($request->filled('category')) {
            $catSlug = $request->input('category');
            $category = Category::where('slug', $catSlug)->first();

            if ($category) {
                // Include children subcategories if root
                $categoryIds = $category->children()->pluck('id')->push($category->id);
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Filter by Subcategory
        if ($request->filled('subcategory')) {
            $subcat = Category::where('slug', $request->input('subcategory'))->first();
            if ($subcat) {
                $query->where('category_id', $subcat->id);
            }
        }

        // Filter by Season
        if ($request->filled('season') && in_array($request->input('season'), ['winter', 'summer', 'all_season'])) {
            $query->whereIn('season', [$request->input('season'), 'all_season']);
        }

        // Filter by Fabric
        if ($request->filled('fabric')) {
            $query->where('fabric', $request->input('fabric'));
        }

        // Filter by Price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        // Filter by In-Stock
        if ($request->boolean('in_stock')) {
            $query->where(function ($q) {
                $q->where('stock', '>', 0)
                  ->orWhereHas('variants', fn($vq) => $vq->where('stock', '>', 0));
            });
        }

        // Filter by Discount
        if ($request->boolean('discount_only')) {
            $query->whereNotNull('discount_price')->whereColumn('discount_price', '<', 'price');
        }

        // Filter by Variant Size
        if ($request->filled('size')) {
            $size = $request->input('size');
            $query->whereHas('variants', fn($q) => $q->where('size', $size));
        }

        // Filter by Variant Color
        if ($request->filled('color')) {
            $color = $request->input('color');
            $query->whereHas('variants', fn($q) => $q->where('color', $color));
        }

        // Search Keyword
        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%")
                  ->orWhere('short_description', 'like', "%{$keyword}%")
                  ->orWhere('fabric', 'like', "%{$keyword}%")
                  ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', "%{$keyword}%"))
                  ->orWhereHas('schoolUniform', fn($uq) => $uq->where('school_name', 'like', "%{$keyword}%"));
            });
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'popularity' => $query->orderBy('view_count', 'desc'),
            'best_sellers' => $query->orderBy('is_best_seller', 'desc')->latest(),
            default => $query->latest(),
        };

        $products = $query->paginate(16)->withQueryString();

        // Facets for filter sidebar (from real DB)
        $categories = Category::active()->root()->with(['activeChildren'])->orderBy('display_order')->get();
        $fabrics = Product::active()->whereNotNull('fabric')->distinct()->pluck('fabric')->filter()->values();
        $sizes = ProductVariant::where('is_active', true)->whereNotNull('size')->distinct()->pluck('size')->values();
        $colors = ProductVariant::where('is_active', true)->whereNotNull('color')->distinct()->pluck('color')->values();

        $selectedCategory = $request->filled('category') ? Category::where('slug', $request->input('category'))->first() : null;

        return view('products.index', compact(
            'products',
            'categories',
            'fabrics',
            'sizes',
            'colors',
            'selectedCategory',
            'sort'
        ));
    }

    public function show(string $slug): View
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with([
                'category',
                'images',
                'variants',
                'schoolUniform',
                'approvedReviews.user'
            ])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->firstOrFail();

        // Increment view count
        $product->increment('view_count');

        // Related products from same category
        $relatedProducts = Product::active()
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->with(['primaryImage', 'images', 'category'])
            ->take(4)
            ->get();

        // If not enough related from same category, fallback to same season
        if ($relatedProducts->count() < 4) {
            $extra = Product::active()
                ->where('id', '!=', $product->id)
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->where('season', $product->season)
                ->with(['primaryImage', 'images', 'category'])
                ->take(4 - $relatedProducts->count())
                ->get();

            $relatedProducts = $relatedProducts->merge($extra);
        }

        $canReview = false;
        $orderPendingDelivery = false;
        $userReview = null;

        if (auth()->check()) {
            $userId = auth()->id();

            // Has this product in a DELIVERED order?
            $hasDelivered = auth()->user()->orders()
                ->where('order_status', 'delivered')
                ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
                ->exists();

            // Has this product in an order that is still pending delivery?
            $hasOrderedNotDelivered = auth()->user()->orders()
                ->whereIn('order_status', ['pending', 'confirmed', 'packed', 'shipped'])
                ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
                ->exists();

            $userReview = Review::where('user_id', $userId)
                ->where('product_id', $product->id)
                ->first();

            $canReview = $hasDelivered;
            $orderPendingDelivery = !$hasDelivered && $hasOrderedNotDelivered;
        }

        return view('products.show', compact('product', 'relatedProducts', 'canReview', 'orderPendingDelivery', 'userReview'));
    }

    public function liveSearch(Request $request): JsonResponse
    {
        $keyword = trim($request->input('q', ''));
        if (strlen($keyword) < 2) {
            return response()->json(['products' => [], 'categories' => []]);
        }

        $products = Product::active()
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%")
                  ->orWhere('fabric', 'like', "%{$keyword}%");
            })
            ->with(['primaryImage', 'category'])
            ->take(6)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'url' => route('products.show', $p->slug),
                'price' => '₹' . number_format($p->effective_price, 2),
                'original_price' => $p->discount_price ? ('₹' . number_format($p->price, 2)) : null,
                'category' => $p->category?->name,
                'image' => $p->main_image_url,
            ]);

        $categories = Category::active()
            ->where('name', 'like', "%{$keyword}%")
            ->take(3)
            ->get()
            ->map(fn($c) => [
                'name' => $c->name,
                'slug' => $c->slug,
                'url' => route('products.index', ['category' => $c->slug]),
            ]);

        return response()->json([
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
