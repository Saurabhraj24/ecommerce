<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\StaffAdvance;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffAdvanceController extends Controller
{
    /**
     * Show Staff Advances & Peshgi Ledger.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->input('status', 'all');
        $staffFilter = $request->input('user_id');

        $staffRole = Role::where('name', Role::STAFF)->first();
        $adminRole = Role::where('name', Role::ADMIN)->first();

        $staffMembers = User::whereIn('role_id', array_filter([$staffRole?->id, $adminRole?->id]))
            ->where('is_active', true)
            ->get();

        $advancesQuery = StaffAdvance::with(['user', 'salary'])->latest('advance_date');

        if ($statusFilter !== 'all') {
            $advancesQuery->where('status', $statusFilter);
        }

        if (!empty($staffFilter)) {
            $advancesQuery->where('user_id', $staffFilter);
        }

        $advances = $advancesQuery->paginate(15)->withQueryString();

        $totalOutstandingAdvances = StaffAdvance::where('status', 'pending')->sum('amount');
        $recoveredThisYear = StaffAdvance::where('status', '!=', 'pending')->whereYear('updated_at', now()->year)->sum('amount');

        return view('admin.staff.advances.index', compact(
            'advances',
            'staffMembers',
            'totalOutstandingAdvances',
            'recoveredThisYear',
            'statusFilter',
            'staffFilter'
        ));
    }

    /**
     * Disburse a new cash/UPI salary advance to staff.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:100',
            'advance_date' => 'required|date',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $advance = StaffAdvance::create([
            'user_id' => $validated['user_id'],
            'amount' => $validated['amount'],
            'advance_date' => $validated['advance_date'],
            'reason' => $validated['reason'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        ActivityLog::log('advance_disbursed', StaffAdvance::class, $advance->id, [
            'staff' => $advance->user->name,
            'amount' => $advance->amount,
            'reason' => $advance->reason,
        ]);

        return back()->with('success', 'Salary advance of ₹' . number_format($advance->amount, 2) . ' issued to ' . $advance->user->name . '.');
    }

    /**
     * Manually settle an advance (e.g., staff repaid cash directly).
     */
    public function markSettled(Request $request, StaffAdvance $advance): RedirectResponse
    {
        $advance->update([
            'status' => 'cash_repaid',
            'notes' => ($advance->notes ? $advance->notes . ' | ' : '') . 'Settled in cash on ' . now()->toDateString(),
        ]);

        ActivityLog::log('advance_settled', StaffAdvance::class, $advance->id, [
            'staff' => $advance->user->name,
            'amount' => $advance->amount,
        ]);

        return back()->with('success', 'Advance marked as settled/repaid for ' . $advance->user->name . '.');
    }
}
