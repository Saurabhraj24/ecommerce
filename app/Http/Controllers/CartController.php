<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(): View
    {
        $cart = $this->cartService->getCart();
        $summary = $this->cartService->calculateSummary($cart);
        return view('cart.index', compact('cart', 'summary'));
    }

    public function add(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'nullable|integer|min:1|max:50',
        ]);

        $quantity = $validated['quantity'] ?? 1;
        $variantId = $validated['product_variant_id'] ?? $validated['variant_id'] ?? null;

        try {
            $item = $this->cartService->addItem(
                $validated['product_id'],
                $variantId,
                $quantity
            );

            $cart = $this->cartService->getCart();

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Item added to your shopping bag!',
                    'cart_count' => $cart->item_count,
                    'cart_subtotal' => '₹' . number_format($cart->subtotal, 2),
                ]);
            }

            if ($request->boolean('buy_now')) {
                return redirect()->route('checkout.index');
            }

            return back()->with('success', 'Item added to your shopping bag!');
        } catch (Exception $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0|max:50',
        ]);

        try {
            $this->cartService->updateItemQuantity($id, $validated['quantity']);
            $cart = $this->cartService->getCart();

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'cart_count' => $cart->item_count,
                    'subtotal' => '₹' . number_format($cart->subtotal, 2),
                    'discount' => '₹' . number_format($cart->discount_amount, 2),
                    'tax' => '₹' . number_format($cart->tax_amount, 2),
                    'shipping' => $cart->shipping_charge > 0 ? ('₹' . number_format($cart->shipping_charge, 2)) : 'FREE',
                    'grand_total' => '₹' . number_format($cart->grand_total, 2),
                ]);
            }

            return back()->with('success', 'Cart updated successfully.');
        } catch (Exception $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function remove(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $this->cartService->removeItem($id);
        $cart = $this->cartService->getCart();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Item removed from bag.',
                'cart_count' => $cart->item_count,
                'grand_total' => '₹' . number_format($cart->grand_total, 2),
            ]);
        }

        return back()->with('info', 'Item removed from bag.');
    }

    public function updateByPost(Request $request): JsonResponse|RedirectResponse
    {
        $id = (int) $request->input('item_id');
        return $this->update($request, $id);
    }

    public function removeByPost(Request $request): JsonResponse|RedirectResponse
    {
        $id = (int) $request->input('item_id');
        return $this->remove($request, $id);
    }

    public function applyCoupon(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);

        try {
            $coupon = $this->cartService->applyCoupon($request->code);
            return back()->with('success', "Coupon '{$coupon->code}' applied successfully!");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function removeCoupon(): RedirectResponse
    {
        $this->cartService->removeCoupon();
        return back()->with('info', 'Coupon removed.');
    }
}
