<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Role;
use App\Models\StaffLeave;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffLeaveController extends Controller
{
    /**
     * Show Staff Leaves & Vacation Register.
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

        $leavesQuery = StaffLeave::with(['user', 'approver'])
            ->latest('start_date');

        if ($statusFilter !== 'all') {
            $leavesQuery->where('status', $statusFilter);
        }

        if (!empty($staffFilter)) {
            $leavesQuery->where('user_id', $staffFilter);
        }

        $leaves = $leavesQuery->paginate(15)->withQueryString();

        // Metrics
        $today = now()->toDateString();
        $activeLeavesToday = StaffLeave::where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->with('user')
            ->get();

        $pendingCount = StaffLeave::where('status', 'pending')->count();
        $thisMonthCount = StaffLeave::where('status', 'approved')
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->count();

        return view('admin.staff.leaves.index', compact(
            'leaves',
            'staffMembers',
            'activeLeavesToday',
            'pendingCount',
            'thisMonthCount',
            'statusFilter',
            'staffFilter'
        ));
    }

    /**
     * Submit a new staff leave / vacation application.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type' => 'required|in:casual,sick,festival,unpaid,emergency',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'status' => 'required|in:pending,approved',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $daysCount = $start->diffInDays($end) + 1;

        $leave = StaffLeave::create([
            'user_id' => $validated['user_id'],
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days_count' => $daysCount,
            'reason' => $validated['reason'],
            'status' => $validated['status'],
            'approved_by' => $validated['status'] === 'approved' ? auth()->id() : null,
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        // If approved immediately, sync with attendance records
        if ($leave->status === 'approved') {
            $this->syncAttendanceWithLeave($leave);
        }

        ActivityLog::log('leave_applied', StaffLeave::class, $leave->id, [
            'staff' => $leave->user->name,
            'type' => $leave->leave_type,
            'days' => $daysCount,
            'status' => $leave->status,
        ]);

        return back()->with('success', 'Leave applied successfully (' . $daysCount . ' days for ' . $leave->user->name . ').');
    }

    /**
     * Approve or reject leave request.
     */
    public function updateStatus(Request $request, StaffLeave $leave): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,cancelled',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $leave->update([
            'status' => $validated['status'],
            'approved_by' => auth()->id(),
            'admin_notes' => $validated['admin_notes'] ?? $leave->admin_notes,
        ]);

        if ($validated['status'] === 'approved') {
            $this->syncAttendanceWithLeave($leave);
        }

        ActivityLog::log('leave_status_updated', StaffLeave::class, $leave->id, [
            'staff' => $leave->user->name,
            'status' => $validated['status'],
        ]);

        return back()->with('success', 'Leave status updated to ' . ucfirst($validated['status']) . ' for ' . $leave->user->name . '.');
    }

    /**
     * Cancel or delete a leave record.
     */
    public function destroy(StaffLeave $leave): RedirectResponse
    {
        $staffName = $leave->user->name;
        $leave->delete();

        return back()->with('success', 'Leave record removed for ' . $staffName . '.');
    }

    /**
     * Helper to mark attendance dates as 'leave' when approved.
     */
    private function syncAttendanceWithLeave(StaffLeave $leave): void
    {
        $period = CarbonPeriod::create($leave->start_date, $leave->end_date);

        foreach ($period as $date) {
            Attendance::updateOrCreate(
                [
                    'user_id' => $leave->user_id,
                    'date' => $date->toDateString(),
                ],
                [
                    'status' => 'leave',
                    'notes' => 'Approved ' . $leave->leave_type_label . ': ' . $leave->reason,
                    'verification_method' => 'admin_leave_approval',
                ]
            );
        }
    }
}
