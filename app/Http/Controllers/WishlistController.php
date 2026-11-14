<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(): View
    {
        $wishlists = Wishlist::where('user_id', Auth::id())
            ->with(['product.primaryImage', 'product.images', 'product.category', 'product.variants'])
            ->latest()
            ->paginate(12);

        return view('wishlist.index', compact('wishlists'));
    }

    public function toggle(int $productId): JsonResponse|RedirectResponse
    {
        if (!Auth::check()) {
            if (request()->expectsJson()) {
                return response()->json(['status' => 'unauthorized', 'message' => 'Please log in to add items to your wishlist.'], 401);
            }
            return redirect()->route('login')->with('info', 'Please log in to manage your wishlist.');
        }

        $userId = Auth::id();
        $product = Product::active()->findOrFail($productId);

        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $count = Wishlist::where('user_id', $userId)->count();

            if (request()->expectsJson()) {
                return response()->json([
                    'status' => 'removed',
                    'message' => 'Removed from wishlist.',
                    'count' => $count,
                ]);
            }

            return back()->with('info', 'Item removed from your wishlist.');
        }

        Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        $count = Wishlist::where('user_id', $userId)->count();

        if (request()->expectsJson()) {
            return response()->json([
                'status' => 'added',
                'message' => 'Added to your wishlist!',
                'count' => $count,
            ]);
        }

        return back()->with('success', 'Item added to your wishlist!');
    }
}
