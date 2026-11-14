<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $customerRole = Role::where('name', Role::USER)->first();

        $query = User::where('role_id', $customerRole?->id)
            ->withCount('orders')
            ->withSum(['orders' => fn($q) => $q->where('payment_status', 'paid')], 'grand_total');

        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load(['orders.items', 'addresses', 'reviews.product']);
        return view('admin.users.show', compact('user'));
    }

    public function toggleActive(User $user): RedirectResponse
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';

        ActivityLog::log("user_{$status}", User::class, $user->id, ['email' => $user->email]);

        return back()->with('success', "Customer account {$status}.");
    }
}
