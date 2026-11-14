@extends('layouts.admin')

@section('title', 'Staff Salary & Monthly Payroll Register - Bombay Fashion Admin')

@section('content')
<div class="space-y-6" x-data="{
    paymentModalOpen: false,
    generateModalOpen: false,
    selectedStaff: null,
    salaryRecord: null,
    paymentMethod: 'bank_transfer',
    paymentDate: '{{ now()->toDateString() }}',
    paymentRef: '',
    paymentNotes: '',
    openPayment(item) {
        this.selectedStaff = item.staff;
        this.salaryRecord = item.salary;
        this.paymentModalOpen = true;
    }
}">
    {{-- Header Banner --}}
    <div class="glass-card rounded-3xl p-6 sm:p-7 border border-white/80 bg-gradient-to-r from-emerald-50/60 via-white to-primary-50/40 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                    HR &amp; Payroll Desk
                </span>
                <span class="text-xs text-plum/50 font-medium">Bombay Fashion Amritsar</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-plum mt-1">
                Staff Salary &amp; Payroll Register
            </h1>
            <p class="text-xs text-plum/70 mt-1">
                Showroom sales executives, master tailors aur counter cashiers ka monthly attendance-based salary calculation, bank transfers aur official payslips.
            </p>
        </div>

        {{-- Month Selector & Fast Actions --}}
        <div class="flex flex-wrap items-center gap-2.5">
            <form action="{{ route('admin.staff.salaries.index') }}" method="GET" class="flex items-center gap-2">
                <input 
                    type="month" 
                    name="month" 
                    value="{{ $selectedMonth }}" 
                    onchange="this.form.submit()"
                    class="rounded-xl border border-plum/20 bg-white px-3 py-2 text-xs font-bold text-plum shadow-inner focus:ring-primary"
                >
            </form>

            <button 
                type="button"
                @click="generateModalOpen = true"
                class="px-4 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-md shadow-primary/25 hover:bg-primary-700 transition flex items-center gap-2 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Calculate &amp; Lock Month</span>
            </button>
        </div>
    </div>

    {{-- Metrics KPI Bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="glass-card rounded-2xl p-4 border border-plum/10 bg-white shadow-xs">
            <span class="text-[11px] font-bold text-plum/60 uppercase tracking-wider block">Month Net Payroll</span>
            <span class="text-xl sm:text-2xl font-serif font-bold text-plum mt-1 block">
                ₹{{ number_format($totalPayrollAmount, 2) }}
            </span>
            <span class="text-[10px] text-plum/50 font-medium mt-0.5 block">
                For {{ Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
            </span>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-emerald-200 bg-emerald-50/40 shadow-xs">
            <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Disbursed (Paid)</span>
            <span class="text-xl sm:text-2xl font-serif font-bold text-emerald-700 mt-1 block">
                ₹{{ number_format($totalPaidAmount, 2) }}
            </span>
            <span class="text-[10px] text-emerald-800 font-medium mt-0.5 block">
                {{ $paidCount }} staff members paid
            </span>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-amber-200 bg-amber-50/40 shadow-xs">
            <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wider block">Pending Payout</span>
            <span class="text-xl sm:text-2xl font-serif font-bold text-amber-800 mt-1 block">
                ₹{{ number_format($totalPayrollAmount - $totalPaidAmount, 2) }}
            </span>
            <span class="text-[10px] text-amber-800 font-medium mt-0.5 block">
                {{ $pendingCount }} staff awaiting salary
            </span>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-plum/10 bg-white shadow-xs flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-bold text-plum/60 uppercase tracking-wider block">Quick Staff Shortcuts</span>
                <div class="flex items-center gap-2 mt-2">
                    <a href="{{ route('admin.staff.leaves.index') }}" class="text-[11px] font-bold text-primary hover:underline">
                        🌴 Leaves / Chutti
                    </a>
                    <span class="text-plum/30">•</span>
                    <a href="{{ route('admin.staff.advances.index') }}" class="text-[11px] font-bold text-amber-700 hover:underline">
                        💸 Advance Khata
                    </a>
                </div>
            </div>
            <a href="{{ route('admin.attendance.index') }}" class="text-[10px] text-plum/50 hover:text-plum mt-1">
                View 20s QR Attendance &rarr;
            </a>
        </div>
    </div>

    {{-- Main Payroll Table --}}
    <div class="glass-card rounded-3xl border border-plum/10 bg-white shadow-sm overflow-hidden">
        <div class="p-5 border-b border-plum/10 flex items-center justify-between">
            <div>
                <h2 class="font-serif font-bold text-plum text-base">
                    Staff Salary Disbursement Sheet
                </h2>
                <p class="text-xs text-plum/60">
                    Salary is calculated based on dynamic QR punch attendance, approved leaves, overtime bonuses and pending advance deductions.
                </p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-plum/5 text-plum">
                {{ count($payrollList) }} Staff Listed
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-plum/5 text-plum/70 uppercase text-[10px] font-bold border-b border-plum/10">
                    <tr>
                        <th class="px-4 py-3">Staff Profile</th>
                        <th class="px-3 py-3">Designation</th>
                        <th class="px-3 py-3 text-right">Base Salary</th>
                        <th class="px-3 py-3 text-center">Attendance (P / L / A)</th>
                        <th class="px-3 py-3 text-right">Bonus / Inc.</th>
                        <th class="px-3 py-3 text-right">Deductions</th>
                        <th class="px-4 py-3 text-right font-bold text-plum">Net Salary</th>
                        <th class="px-3 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-plum/10 text-plum font-medium">
                    @forelse($payrollList as $item)
                        @php
                            $staff = $item['staff'];
                            $sal = $item['salary'];
                            $isGenerated = $item['is_generated'];
                        @endphp
                        <tr class="hover:bg-plum/[0.02] transition">
                            <td class="px-4 py-3.5">
                                <div class="flex items-center space-x-3">
                                    <img 
                                        src="{{ $staff->avatar_url }}" 
                                        alt="{{ $staff->name }}" 
                                        class="w-9 h-9 rounded-xl object-cover border border-plum/10 shrink-0"
                                    >
                                    <div>
                                        <div class="font-bold text-plum">{{ $staff->name }}</div>
                                        <div class="text-[10px] text-plum/50 font-mono">{{ $staff->employee_id ?? 'BF-EMP-' . $staff->id }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-3 py-3.5">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-plum/5 text-plum">
                                    {{ $staff->designation ?? 'Sales Executive' }}
                                </span>
                                @if($staff->upi_id)
                                    <div class="text-[9px] text-emerald-700 font-mono mt-0.5">UPI: {{ $staff->upi_id }}</div>
                                @elseif($staff->bank_account_no)
                                    <div class="text-[9px] text-plum/50 font-mono mt-0.5">A/C: {{ Str::mask($staff->bank_account_no, '*', 0, -4) }}</div>
                                @endif
                            </td>

                            <td class="px-3 py-3.5 text-right font-mono font-bold">
                                ₹{{ number_format($sal->base_salary, 2) }}
                            </td>

                            <td class="px-3 py-3.5 text-center">
                                <div class="inline-flex items-center space-x-1.5 text-[11px] font-mono">
                                    <span class="text-emerald-700 font-bold" title="Present Days">{{ $sal->days_present }}P</span>
                                    <span class="text-plum/30">•</span>
                                    <span class="text-primary font-bold" title="Approved Leaves">{{ $sal->paid_leaves }}L</span>
                                    <span class="text-plum/30">•</span>
                                    <span class="text-rose-600 font-bold" title="Absent Days">{{ $sal->days_absent }}A</span>
                                </div>
                                <div class="text-[9px] text-plum/40">{{ $sal->working_days }} days total</div>
                            </td>

                            <td class="px-3 py-3.5 text-right font-mono text-emerald-700">
                                +₹{{ number_format($sal->bonus_incentive, 2) }}
                            </td>

                            <td class="px-3 py-3.5 text-right font-mono text-rose-600">
                                -₹{{ number_format($sal->deductions, 2) }}
                                @if(!$isGenerated && isset($sal->pending_advances) && $sal->pending_advances > 0)
                                    <div class="text-[9px] text-amber-700 font-bold">(₹{{ number_format($sal->pending_advances) }} advance pending)</div>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 text-right font-mono font-serif font-bold text-sm text-plum">
                                ₹{{ number_format($sal->net_salary, 2) }}
                            </td>

                            <td class="px-3 py-3.5 text-center">
                                @if($sal->payment_status === 'paid')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 flex items-center justify-center gap-1 w-max mx-auto">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Paid ({{ ucfirst($sal->payment_method) }})</span>
                                    </span>
                                    @if($sal->payment_date)
                                        <span class="text-[9px] text-plum/50 block mt-0.5">{{ \Carbon\Carbon::parse($sal->payment_date)->format('d M Y') }}</span>
                                    @endif
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 block w-max mx-auto">
                                        Pending
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    @if($isGenerated && $sal->id)
                                        <a 
                                            href="{{ route('admin.staff.salaries.payslip', $sal->id) }}" 
                                            target="_blank"
                                            class="p-1.5 rounded-lg border border-plum/15 text-plum hover:bg-plum/5 transition"
                                            title="Print Official Payslip"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        </a>

                                        @if($sal->payment_status !== 'paid')
                                            <button 
                                                type="button" 
                                                @click="openPayment(@js($item))"
                                                class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-xs transition cursor-pointer"
                                            >
                                                Record Payment
                                            </button>
                                        @endif
                                    @else
                                        <form action="{{ route('admin.staff.salaries.generate') }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="month" value="{{ $selectedMonth }}">
                                            <input type="hidden" name="user_id" value="{{ $staff->id }}">
                                            <button 
                                                type="submit" 
                                                class="px-2.5 py-1.5 rounded-lg bg-primary hover:bg-primary-700 text-white font-bold text-[11px] shadow-xs transition cursor-pointer"
                                            >
                                                Generate Slip
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-plum/50">
                                No active staff members found in the store system.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL 1: Record Salary Payment (Cash / UPI / Bank Transfer) --}}
    <div 
        x-show="paymentModalOpen" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-plum/60 backdrop-blur-xs flex items-center justify-center p-4"
    >
        <div 
            @click.away="paymentModalOpen = false"
            class="glass-card rounded-3xl p-6 sm:p-7 border border-white/80 bg-white shadow-2xl max-w-md w-full space-y-4"
        >
            <div class="flex items-center justify-between pb-3 border-b border-plum/10">
                <div>
                    <h3 class="font-serif font-bold text-plum text-lg">Record Salary Payment</h3>
                    <p class="text-xs text-plum/60" x-text="selectedStaff?.name + ' (' + selectedStaff?.designation + ')'"></p>
                </div>
                <button @click="paymentModalOpen = false" class="text-plum/40 hover:text-plum text-xl">&times;</button>
            </div>

            <template x-if="salaryRecord">
                <form 
                    :action="'/admin/staff-salaries/' + salaryRecord.id + '/payment'" 
                    method="POST" 
                    class="space-y-4 text-xs font-semibold text-plum"
                >
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="payment_status" value="paid">

                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                        <span class="text-emerald-800">Net Amount to Pay:</span>
                        <span class="text-lg font-serif font-bold text-emerald-800" x-text="'₹' + Number(salaryRecord.net_salary).toLocaleString('en-IN', {minimumFractionDigits: 2})"></span>
                    </div>

                    <div>
                        <label class="block mb-1">Payment Mode *</label>
                        <select name="payment_method" x-model="paymentMethod" required class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-xs text-plum focus:ring-primary">
                            <option value="bank_transfer">Direct Bank Transfer (NEFT / IMPS)</option>
                            <option value="upi">UPI Transfer (Google Pay / PhonePe / Paytm)</option>
                            <option value="cash">Showroom Counter Cash</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-1">Payment Date *</label>
                        <input type="date" name="payment_date" x-model="paymentDate" required class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-xs text-plum focus:ring-primary">
                    </div>

                    <div>
                        <label class="block mb-1">Transaction Ref / UTR / Receipt No.</label>
                        <input type="text" name="payment_reference" x-model="paymentRef" placeholder="e.g. UTR3892719873 / Cash Voucher #12" class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-xs text-plum font-mono focus:ring-primary">
                    </div>

                    <div>
                        <label class="block mb-1">Notes / Remarks</label>
                        <input type="text" name="notes" x-model="paymentNotes" placeholder="e.g. Cleared full month salary" class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-xs text-plum focus:ring-primary">
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-2 border-t border-plum/10">
                        <button type="button" @click="paymentModalOpen = false" class="px-4 py-2 rounded-xl border border-plum/20 text-plum font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-md shadow-emerald-600/25 transition">
                            Confirm &amp; Mark as Paid
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

    {{-- MODAL 2: Calculate & Lock All Staff Salaries for the Month --}}
    <div 
        x-show="generateModalOpen" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-plum/60 backdrop-blur-xs flex items-center justify-center p-4"
    >
        <div 
            @click.away="generateModalOpen = false"
            class="glass-card rounded-3xl p-6 sm:p-7 border border-white/80 bg-white shadow-2xl max-w-lg w-full space-y-4"
        >
            <div class="flex items-center justify-between pb-3 border-b border-plum/10">
                <div>
                    <h3 class="font-serif font-bold text-plum text-lg">Generate Full Store Payroll</h3>
                    <p class="text-xs text-plum/60">Calculate monthly salaries for all active showroom employees.</p>
                </div>
                <button @click="generateModalOpen = false" class="text-plum/40 hover:text-plum text-xl">&times;</button>
            </div>

            <form action="{{ route('admin.staff.salaries.generate') }}" method="POST" class="space-y-4 text-xs font-semibold text-plum">
                @csrf
                <input type="hidden" name="month" value="{{ $selectedMonth }}">

                <div class="p-4 rounded-2xl bg-primary-50/50 border border-primary-200">
                    <p class="font-bold text-primary mb-1">Automated Salary Rules:</p>
                    <ul class="list-disc list-inside space-y-1 text-plum/70 text-[11px]">
                        <li>Dynamic QR attendance records se Present, Late aur Half-day count honge.</li>
                        <li>Approved casual &amp; medical leaves automatically full-paid count hongi.</li>
                        <li>Agar staff ne peshgi (salary advance) li hui hai, toh auto-deduct option select kar sakte hain.</li>
                    </ul>
                </div>

                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-3">
                    <input type="checkbox" name="deduct_advance" value="1" id="deduct_adv" checked class="mt-0.5 rounded text-primary focus:ring-primary">
                    <label for="deduct_adv" class="text-amber-900 cursor-pointer">
                        <span class="font-bold block">Auto-deduct pending salary advances (पेशगी)</span>
                        <span class="text-[10px] text-amber-800">Agar staff ne advance liya hua hai toh is mahine ke net payout se automatic kat jayega.</span>
                    </label>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2 border-t border-plum/10">
                    <button type="button" @click="generateModalOpen = false" class="px-4 py-2 rounded-xl border border-plum/20 text-plum font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold shadow-md shadow-primary/25 transition">
                        Run Monthly Payroll Engine
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
