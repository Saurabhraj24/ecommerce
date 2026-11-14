@extends('layouts.admin')

@section('title', 'Staff Salary Advances & Peshgi Ledger (वेतन अग्रिम खाता) - Bombay Fashion Admin')

@section('content')
<div class="space-y-6" x-data="{
    advanceModalOpen: false
}">
    {{-- Header Banner --}}
    <div class="glass-card rounded-3xl p-6 sm:p-7 border border-white/80 bg-gradient-to-r from-amber-50/60 via-white to-primary-50/40 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800">
                    Staff Credit &amp; Advance
                </span>
                <span class="text-xs text-plum/50 font-medium">Bombay Fashion Amritsar</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-plum mt-1">
                Staff Salary Advance (पेशगी खाता)
            </h1>
            <p class="text-xs text-plum/70 mt-1">
                Tyohar, medical emergency ya shaadi ke liye staff ko di gayi advance peshgi ka hisaab-kitab aur agle mahine ki salary se automatic recovery.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button 
                type="button"
                @click="advanceModalOpen = true"
                class="px-4 py-2.5 rounded-xl bg-amber-600 text-white text-xs font-bold shadow-md shadow-amber-600/25 hover:bg-amber-700 transition flex items-center gap-2 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span>Issue Salary Advance (Peshgi)</span>
            </button>
        </div>
    </div>

    {{-- Metrics KPI Bar --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass-card rounded-2xl p-4 border border-rose-200 bg-rose-50/40 shadow-xs">
            <span class="text-[11px] font-bold text-rose-800 uppercase tracking-wider block">Total Outstanding Advances</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-serif font-bold text-rose-700 font-mono">
                    ₹{{ number_format($totalOutstandingAdvances, 2) }}
                </span>
            </div>
            <span class="text-[10px] text-rose-800 font-medium mt-0.5 block">
                Pending recovery from upcoming salaries
            </span>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-emerald-200 bg-emerald-50/40 shadow-xs">
            <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Recovered This Year</span>
            <span class="text-2xl font-serif font-bold text-emerald-700 font-mono mt-1 block">
                ₹{{ number_format($recoveredThisYear, 2) }}
            </span>
            <span class="text-[10px] text-emerald-800 font-medium mt-0.5 block">
                Deducted via monthly salary slips or repaid
            </span>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-plum/10 bg-white shadow-xs flex flex-col justify-between">
            <div>
                <span class="text-[11px] font-bold text-plum/60 uppercase tracking-wider block">Staff Payroll Integration</span>
                <p class="text-[11px] text-plum/70 mt-1">
                    Jab aap <a href="{{ route('admin.staff.salaries.index') }}" class="font-bold text-primary underline">Salary Register</a> me month calculate karenge, toh pending advance automatically salary se deduct ho jayega.
                </p>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-plum/10 shadow-xs text-xs font-semibold">
        <div class="flex items-center space-x-2">
            <span class="text-plum/50 text-[11px]">Status:</span>
            <a href="{{ route('admin.staff.advances.index', ['status' => 'all']) }}" class="px-3 py-1.5 rounded-xl transition {{ $statusFilter === 'all' ? 'bg-amber-600 text-white font-bold' : 'text-plum hover:bg-plum/5' }}">
                All Records
            </a>
            <a href="{{ route('admin.staff.advances.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-xl transition {{ $statusFilter === 'pending' ? 'bg-rose-600 text-white font-bold' : 'text-plum hover:bg-plum/5' }}">
                Pending Recovery
            </a>
            <a href="{{ route('admin.staff.advances.index', ['status' => 'deducted_from_salary']) }}" class="px-3 py-1.5 rounded-xl transition {{ $statusFilter === 'deducted_from_salary' ? 'bg-emerald-600 text-white font-bold' : 'text-plum hover:bg-plum/5' }}">
                Salary Deducted
            </a>
            <a href="{{ route('admin.staff.advances.index', ['status' => 'cash_repaid']) }}" class="px-3 py-1.5 rounded-xl transition {{ $statusFilter === 'cash_repaid' ? 'bg-teal-600 text-white font-bold' : 'text-plum hover:bg-plum/5' }}">
                Cash Repaid
            </a>
        </div>

        <form action="{{ route('admin.staff.advances.index') }}" method="GET" class="flex items-center gap-2">
            <input type="hidden" name="status" value="{{ $statusFilter }}">
            <select name="user_id" onchange="this.form.submit()" class="rounded-xl border border-plum/20 bg-white px-3 py-1.5 text-xs text-plum">
                <option value="">All Staff Members</option>
                @foreach($staffMembers as $staff)
                    <option value="{{ $staff->id }}" {{ $staffFilter == $staff->id ? 'selected' : '' }}>
                        {{ $staff->name }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Advances Table --}}
    <div class="glass-card rounded-3xl border border-plum/10 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-plum/5 text-plum/70 uppercase text-[10px] font-bold border-b border-plum/10">
                    <tr>
                        <th class="px-4 py-3">Staff Profile</th>
                        <th class="px-3 py-3">Issue Date</th>
                        <th class="px-4 py-3 text-right">Advance Amount</th>
                        <th class="px-4 py-3">Reason / Details</th>
                        <th class="px-3 py-3 text-center">Status</th>
                        <th class="px-3 py-3">Recovery Ledger</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-plum/10 text-plum font-medium">
                    @forelse($advances as $adv)
                        <tr class="hover:bg-plum/[0.02] transition">
                            <td class="px-4 py-3.5">
                                <div class="flex items-center space-x-3">
                                    <img 
                                        src="{{ $adv->user->avatar_url }}" 
                                        alt="{{ $adv->user->name }}" 
                                        class="w-8 h-8 rounded-xl object-cover border border-plum/10 shrink-0"
                                    >
                                    <div>
                                        <div class="font-bold text-plum">{{ $adv->user->name }}</div>
                                        <div class="text-[10px] text-plum/50">{{ $adv->user->designation ?? 'Staff' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-3 py-3.5 font-mono text-[11px]">
                                {{ $adv->advance_date->format('d M Y') }}
                            </td>

                            <td class="px-4 py-3.5 text-right font-mono font-bold text-sm text-plum">
                                ₹{{ number_format($adv->amount, 2) }}
                            </td>

                            <td class="px-4 py-3.5 max-w-xs">
                                <p class="text-plum truncate" title="{{ $adv->reason }}">{{ $adv->reason }}</p>
                                @if($adv->notes)
                                    <p class="text-[10px] text-plum/50 italic mt-0.5 truncate">{{ $adv->notes }}</p>
                                @endif
                            </td>

                            <td class="px-3 py-3.5 text-center">
                                @if($adv->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 block w-max mx-auto animate-pulse">
                                        Pending Recovery
                                    </span>
                                @elseif($adv->status === 'deducted_from_salary')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 flex items-center justify-center gap-1 w-max mx-auto">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Salary Deducted</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 block w-max mx-auto">
                                        Cash Repaid
                                    </span>
                                @endif
                            </td>

                            <td class="px-3 py-3.5 text-plum/60 text-[11px]">
                                @if($adv->salary)
                                    <a href="{{ route('admin.staff.salaries.payslip', $adv->salary->id) }}" target="_blank" class="text-primary font-semibold hover:underline">
                                        {{ $adv->salary->month_label }} Payslip &rarr;
                                    </a>
                                @else
                                    <span>Direct Showroom Desk</span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 text-right">
                                @if($adv->status === 'pending')
                                    <form action="{{ route('admin.staff.advances.settle', $adv->id) }}" method="POST" onsubmit="return confirm('Mark this advance as repaid in cash directly by the staff member?')" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold text-[10px] shadow-xs cursor-pointer">
                                            Mark Cash Repaid
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[11px] text-plum/40 italic">Settled</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-plum/50">
                                No advance records found for this filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($advances->hasPages())
            <div class="p-4 border-t border-plum/10">
                {{ $advances->links() }}
            </div>
        @endif
    </div>

    {{-- Issue Advance Modal --}}
    <div 
        x-show="advanceModalOpen" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-plum/60 backdrop-blur-xs flex items-center justify-center p-4"
    >
        <div 
            @click.away="advanceModalOpen = false"
            class="glass-card rounded-3xl p-6 sm:p-7 border border-white/80 bg-white shadow-2xl max-w-md w-full space-y-4"
        >
            <div class="flex items-center justify-between pb-3 border-b border-plum/10">
                <div>
                    <h3 class="font-serif font-bold text-plum text-lg">Issue Staff Salary Advance (Peshgi)</h3>
                    <p class="text-xs text-plum/60">Record cash/UPI advance given to showroom staff.</p>
                </div>
                <button @click="advanceModalOpen = false" class="text-plum/40 hover:text-plum text-xl">&times;</button>
            </div>

            <form action="{{ route('admin.staff.advances.store') }}" method="POST" class="space-y-4 text-xs font-semibold text-plum">
                @csrf

                <div>
                    <label class="block mb-1">Staff Member *</label>
                    <select name="user_id" required class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-xs text-plum focus:ring-primary font-medium">
                        <option value="">Select Employee</option>
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->designation ?? 'Staff' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1">Advance Amount (₹) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-plum/50">₹</span>
                        <input type="number" step="0.01" name="amount" required placeholder="e.g. 5000.00" class="w-full rounded-xl border border-plum/20 bg-white p-2.5 pl-8 text-xs text-plum font-mono focus:ring-primary">
                    </div>
                </div>

                <div>
                    <label class="block mb-1">Disbursement Date *</label>
                    <input type="date" name="advance_date" required value="{{ now()->toDateString() }}" class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-xs text-plum focus:ring-primary">
                </div>

                <div>
                    <label class="block mb-1">Reason / Purpose *</label>
                    <input type="text" name="reason" required placeholder="e.g. Diwali shopping advance / Family medical emergency" class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-xs text-plum focus:ring-primary">
                </div>

                <div>
                    <label class="block mb-1">Notes / Cash Voucher Reference</label>
                    <input type="text" name="notes" placeholder="e.g. Paid in cash from counter drawer by Manager" class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-xs text-plum focus:ring-primary">
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2 border-t border-plum/10">
                    <button type="button" @click="advanceModalOpen = false" class="px-4 py-2 rounded-xl border border-plum/20 text-plum font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold shadow-md shadow-amber-600/25 transition">
                        Disburse Advance
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
