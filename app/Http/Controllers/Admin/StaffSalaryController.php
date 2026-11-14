<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\Role;
use App\Models\StaffAdvance;
use App\Models\StaffLeave;
use App\Models\StaffSalary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffSalaryController extends Controller
{
    /**
     * Show Monthly Payroll & Salary Register.
     */
    public function index(Request $request): View
    {
        $selectedMonth = $request->input('month', now()->format('Y-m'));
        $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $daysInMonth = $monthDate->daysInMonth;

        $staffRole = Role::where('name', Role::STAFF)->first();
        $adminRole = Role::where('name', Role::ADMIN)->first();

        $staffMembers = User::whereIn('role_id', array_filter([$staffRole?->id, $adminRole?->id]))
            ->where('is_active', true)
            ->with(['salaries' => fn($q) => $q->where('month', $selectedMonth)])
            ->get();

        // Existing payroll records for the month
        $salaries = StaffSalary::where('month', $selectedMonth)
            ->with('user')
            ->get()
            ->keyBy('user_id');

        // Compile data for each staff
        $payrollList = [];
        $totalPayrollAmount = 0;
        $totalPaidAmount = 0;
        $paidCount = 0;
        $pendingCount = 0;

        foreach ($staffMembers as $staff) {
            $existing = $salaries->get($staff->id);

            if ($existing) {
                $totalPayrollAmount += (float) $existing->net_salary;
                if ($existing->payment_status === 'paid') {
                    $totalPaidAmount += (float) $existing->net_salary;
                    $paidCount++;
                } else {
                    $pendingCount++;
                }

                $payrollList[] = [
                    'staff' => $staff,
                    'salary' => $existing,
                    'is_generated' => true,
                ];
            } else {
                // Auto-preview based on attendance & leaves
                $startDate = $monthDate->toDateString();
                $endDate = $monthDate->copy()->endOfMonth()->toDateString();

                $attendances = Attendance::where('user_id', $staff->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();

                $presentDays = $attendances->where('status', 'present')->count();
                $halfDays = $attendances->where('status', 'half_day')->count();
                $absentDays = $attendances->where('status', 'absent')->count();

                $paidLeaves = StaffLeave::where('user_id', $staff->id)
                    ->where('status', 'approved')
                    ->whereBetween('start_date', [$startDate, $endDate])
                    ->sum('days_count');

                $baseSalary = (float) ($staff->salary ?? 20000);
                $perDayRate = $daysInMonth > 0 ? ($baseSalary / $daysInMonth) : 0;
                
                // Salaried monthly model: Deduct only for unexcused absent days & half days
                $absentDeduction = round(($absentDays + ($halfDays * 0.5)) * $perDayRate, 2);

                $pendingAdvances = StaffAdvance::where('user_id', $staff->id)
                    ->where('status', 'pending')
                    ->sum('amount');

                $deductions = $absentDeduction;
                $netPay = max(0, round($baseSalary - $deductions, 2));

                $totalPayrollAmount += $netPay;
                $pendingCount++;

                $payrollList[] = [
                    'staff' => $staff,
                    'salary' => (object) [
                        'id' => null,
                        'user_id' => $staff->id,
                        'month' => $selectedMonth,
                        'base_salary' => $baseSalary,
                        'working_days' => $daysInMonth,
                        'days_present' => $presentDays,
                        'days_absent' => $absentDays,
                        'days_half' => $halfDays,
                        'paid_leaves' => $paidLeaves,
                        'bonus_incentive' => 0,
                        'deductions' => 0,
                        'net_salary' => $netPay,
                        'payment_status' => 'pending',
                        'payment_method' => null,
                        'payment_reference' => null,
                        'payment_date' => null,
                        'pending_advances' => $pendingAdvances,
                    ],
                    'is_generated' => false,
                ];
            }
        }

        return view('admin.staff.salaries.index', compact(
            'payrollList',
            'selectedMonth',
            'totalPayrollAmount',
            'totalPaidAmount',
            'paidCount',
            'pendingCount'
        ));
    }

    /**
     * Generate or recalculate payroll record for a single staff or all staff for a month.
     */
    public function generate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'user_id' => 'nullable|exists:users,id',
            'bonus_incentive' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'deduct_advance' => 'nullable|boolean',
        ]);

        $monthDate = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();
        $daysInMonth = $monthDate->daysInMonth;
        $startDate = $monthDate->toDateString();
        $endDate = $monthDate->copy()->endOfMonth()->toDateString();

        $staffQuery = User::where('is_active', true);
        if (!empty($validated['user_id'])) {
            $staffQuery->where('id', $validated['user_id']);
        } else {
            $staffRole = Role::where('name', Role::STAFF)->first();
            $adminRole = Role::where('name', Role::ADMIN)->first();
            $staffQuery->whereIn('role_id', array_filter([$staffRole?->id, $adminRole?->id]));
        }

        $staffMembers = $staffQuery->get();

        foreach ($staffMembers as $staff) {
            $baseSalary = (float) ($staff->salary ?? 20000);
            $perDayRate = $daysInMonth > 0 ? ($baseSalary / $daysInMonth) : 0;

            $attendances = Attendance::where('user_id', $staff->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $presentDays = $attendances->where('status', 'present')->count();
            $halfDays = $attendances->where('status', 'half_day')->count();
            $absentDays = $attendances->where('status', 'absent')->count();

            $paidLeaves = StaffLeave::where('user_id', $staff->id)
                ->where('status', 'approved')
                ->whereBetween('start_date', [$startDate, $endDate])
                ->sum('days_count');

            $absentDeductions = round(($absentDays + ($halfDays * 0.5)) * $perDayRate, 2);

            $bonus = (float) ($request->input('bonus_' . $staff->id, $validated['bonus_incentive'] ?? 0));
            $customDeductions = (float) ($request->input('deduction_' . $staff->id, $validated['deductions'] ?? 0));
 
            $advanceDeduction = 0;
            if ($request->boolean('deduct_advance')) {
                $advanceDeduction = StaffAdvance::where('user_id', $staff->id)
                    ->where('status', 'pending')
                    ->sum('amount');
            }

            $totalDeductions = $absentDeductions + $customDeductions + $advanceDeduction;
            $netPay = max(0, round($baseSalary + $bonus - $totalDeductions, 2));

            $salary = StaffSalary::updateOrCreate(
                [
                    'user_id' => $staff->id,
                    'month' => $validated['month'],
                ],
                [
                    'base_salary' => $baseSalary,
                    'working_days' => $daysInMonth,
                    'days_present' => $presentDays,
                    'days_absent' => $absentDays,
                    'days_half' => $halfDays,
                    'paid_leaves' => $paidLeaves,
                    'bonus_incentive' => $bonus,
                    'deductions' => $totalDeductions,
                    'net_salary' => $netPay,
                    'payment_status' => 'pending',
                ]
            );

            // Mark advances as recovered if requested
            if ($advanceDeduction > 0) {
                StaffAdvance::where('user_id', $staff->id)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'deducted_from_salary',
                        'deducted_in_salary_id' => $salary->id,
                    ]);
            }
        }

        ActivityLog::log('payroll_generated', StaffSalary::class, null, ['month' => $validated['month'], 'staff_count' => $staffMembers->count()]);

        return redirect()->route('admin.staff.salaries.index', ['month' => $validated['month']])
            ->with('success', 'Payroll slip generated and updated successfully for ' . Carbon::createFromFormat('Y-m', $validated['month'])->format('F Y') . '.');
    }

    /**
     * Mark salary as Paid and record payment transaction.
     */
    public function updatePayment(Request $request, StaffSalary $salary): RedirectResponse
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:paid,pending,partially_paid',
            'payment_method' => 'required|in:cash,upi,bank_transfer,cheque',
            'payment_reference' => 'nullable|string|max:100',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $salary->update($validated);

        ActivityLog::log('salary_paid', StaffSalary::class, $salary->id, [
            'staff' => $salary->user->name,
            'amount' => $salary->net_salary,
            'method' => $validated['payment_method'],
        ]);

        return back()->with('success', 'Salary payment recorded successfully for ' . $salary->user->name . '.');
    }

    /**
     * Show official printable/downloadable Payslip.
     */
    public function payslip(StaffSalary $salary): View
    {
        if (!auth()->user()->isAdmin() && auth()->id() !== $salary->user_id) {
            abort(403, 'Unauthorized. Aap sirf apni salary slip dekh sakte hain.');
        }

        $salary->load(['user', 'advances']);
        return view('admin.staff.salaries.payslip', compact('salary'));
    }
}
