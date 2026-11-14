<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewModerationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Review::with(['product', 'user']);

        if ($request->filled('status')) {
            if ($request->input('status') === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->input('status') === 'approved') {
                $query->where('is_approved', true);
            }
        }

        $reviews = $query->latest()->paginate(20)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => true]);

        ActivityLog::log('review_approved', Review::class, $review->id, [
            'product' => $review->product?->name,
            'rating' => $review->rating,
        ]);

        return back()->with('success', 'Review approved and published to the store.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        ActivityLog::log('review_deleted', Review::class, $review->id);

        return back()->with('info', 'Review removed.');
    }
}
