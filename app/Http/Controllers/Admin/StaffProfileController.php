<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Order;
use App\Models\StaffAdvance;
use App\Models\StaffLeave;
use App\Models\StaffSalary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StaffProfileController extends Controller
{
    /**
     * Display the logged-in staff/admin member's comprehensive profile.
     */
    public function show(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $user->load('role');

        $today = Carbon::today()->toDateString();
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $latestSalary = StaffSalary::where('user_id', $user->id)
            ->latest('month')
            ->first();

        // Calculate personal purchases stats
        $cleanPhone = preg_replace('/[^0-9]/', '', (string) ($user->phone ?? ''));
        $ordersCount = Order::where(function ($q) use ($user, $cleanPhone) {
            $q->where('user_id', $user->id)
              ->orWhere('customer_email', $user->email);
            if (!empty($user->phone)) {
                $q->orWhere('customer_phone', $user->phone);
            }
            if (!empty($cleanPhone) && strlen($cleanPhone) >= 10) {
                $q->orWhere('customer_phone', 'like', "%{$cleanPhone}%");
            }
        })
        ->where(function ($q) use ($user) {
            // Distinguish actual buyer orders from generic walk-ins billed by cashier
            $q->where('customer_email', '!=', 'walkin@bombayfashion.com')
              ->orWhere('user_id', $user->id);
        })
        ->count();

        return view('admin.profile.show', compact('user', 'todayAttendance', 'latestSalary', 'ordersCount'));
    }

    /**
     * Update personal details and avatar.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if custom
            if ($user->avatar && !str_starts_with($user->avatar, 'http') && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? null;
        $user->address = $validated['address'] ?? null;
        $user->emergency_contact_name = $validated['emergency_contact_name'] ?? null;
        $user->emergency_contact_phone = $validated['emergency_contact_phone'] ?? null;
        $user->save();

        ActivityLog::log('profile_updated', User::class, $user->id, ['updated_by' => $user->name]);

        return back()->with('success', 'Aapka profile record successfully update ho gaya hai.');
    }

    /**
     * Update account password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLog::log('password_changed', User::class, $user->id, ['user' => $user->name]);

        return back()->with('success', 'Password successfully change ho gaya hai.');
    }

    /**
     * Display personal Salary & Payslips register.
     */
    public function salary(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $salaries = StaffSalary::where('user_id', $user->id)
            ->orderBy('month', 'desc')
            ->paginate(12);

        $currentBaseSalary = (float) ($user->salary ?? 0);
        $totalEarnedYtd = (float) StaffSalary::where('user_id', $user->id)
            ->where('payment_status', 'paid')
            ->where('month', 'like', now()->year . '-%')
            ->sum('net_salary');

        $pendingAdvances = (float) StaffAdvance::where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');

        $approvedLeavesCount = (float) StaffLeave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereYear('start_date', now()->year)
            ->sum('days_count');

        return view('admin.profile.salary', compact(
            'user',
            'salaries',
            'currentBaseSalary',
            'totalEarnedYtd',
            'pendingAdvances',
            'approvedLeavesCount'
        ));
    }

    /**
     * Display personal purchases made by staff or admin.
     */
    public function purchases(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $cleanPhone = preg_replace('/[^0-9]/', '', (string) ($user->phone ?? ''));

        $orders = Order::where(function ($q) use ($user, $cleanPhone) {
            $q->where('user_id', $user->id)
              ->orWhere('customer_email', $user->email);
            if (!empty($user->phone)) {
                $q->orWhere('customer_phone', $user->phone);
            }
            if (!empty($cleanPhone) && strlen($cleanPhone) >= 10) {
                $q->orWhere('customer_phone', 'like', "%{$cleanPhone}%");
            }
        })
        ->where(function ($q) use ($user) {
            $q->where('customer_email', '!=', 'walkin@bombayfashion.com')
              ->orWhere('user_id', $user->id);
        })
        ->with(['items.product.primaryImage', 'items.variant'])
        ->latest()
        ->paginate(10);

        $totalSpent = Order::where(function ($q) use ($user, $cleanPhone) {
            $q->where('user_id', $user->id)
              ->orWhere('customer_email', $user->email);
            if (!empty($user->phone)) {
                $q->orWhere('customer_phone', $user->phone);
            }
            if (!empty($cleanPhone) && strlen($cleanPhone) >= 10) {
                $q->orWhere('customer_phone', 'like', "%{$cleanPhone}%");
            }
        })
        ->where(function ($q) use ($user) {
            $q->where('customer_email', '!=', 'walkin@bombayfashion.com')
              ->orWhere('user_id', $user->id);
        })
        ->where('payment_status', 'paid')
        ->sum('grand_total');

        return view('admin.profile.purchases', compact('user', 'orders', 'totalSpent'));
    }
}
