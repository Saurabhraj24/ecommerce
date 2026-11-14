<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Services\CloudinaryImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct(
        protected CloudinaryImageService $cloudinaryService
    ) {}

    public function store(Request $request, int $productId): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:150',
            'comment' => 'required|string|min:5|max:1000',
            'fit_feedback' => 'nullable|string|in:runs_small,true_to_size,runs_large',
            'quality_rating' => 'nullable|string|in:exceptional,good,average,poor',
            'images' => 'nullable|array|max:3',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:4096', // max 4MB per image
        ]);

        $product = Product::active()->findOrFail($productId);
        $userId = Auth::id();

        // Check if user has purchased this item in a DELIVERED order
        $deliveredOrder = Order::where('user_id', $userId)
            ->where('order_status', 'delivered')
            ->whereHas('items', fn($q) => $q->where('product_id', $productId))
            ->latest('delivered_at')
            ->first();

        if (!$deliveredOrder) {
            return back()->with('error', 'Only verified patrons who have purchased and received this product (order delivered) can submit a review.');
        }

        // Upload images if any
        $uploadedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file->isValid()) {
                    $uploaded = $this->cloudinaryService->upload($file, 'bombay_fashion/reviews');
                    $uploadedImages[] = [
                        'image_url' => $uploaded['secure_url'],
                        'public_id' => $uploaded['public_id'],
                    ];
                }
            }
        }

        // Check if already reviewed (Customers review once per product)
        $existing = Review::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($existing) {
            $finalImages = $existing->images ?? [];
            if (!empty($uploadedImages)) {
                $finalImages = array_slice(array_merge($finalImages, $uploadedImages), 0, 3);
            }

            $existing->update([
                'order_id' => $deliveredOrder->id,
                'rating' => $validated['rating'],
                'title' => $validated['title'] ?? null,
                'comment' => $validated['comment'],
                'fit_feedback' => $validated['fit_feedback'] ?? $existing->fit_feedback,
                'quality_rating' => $validated['quality_rating'] ?? $existing->quality_rating,
                'images' => $finalImages,
                'verified_purchase' => true,
                'is_approved' => false, // Resubmitted for moderation
            ]);

            return back()->with('success', 'Your verified review and photos have been updated and submitted for moderation.');
        }

        Review::create([
            'product_id' => $productId,
            'user_id' => $userId,
            'order_id' => $deliveredOrder->id,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'],
            'fit_feedback' => $validated['fit_feedback'] ?? null,
            'quality_rating' => $validated['quality_rating'] ?? null,
            'images' => $uploadedImages,
            'verified_purchase' => true,
            'is_approved' => false,
        ]);

        return back()->with('success', 'Thank you! Your verified review and photos have been submitted and will appear once approved by our Amritsar store team.');
    }
}
