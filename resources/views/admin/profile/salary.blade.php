@extends('layouts.admin')

@section('title', 'My Salary & Payslip Slips - Bombay Fashion Staff')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    {{-- Top Alert Messages --}}
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-800 text-xs font-semibold flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold">&times;</button>
        </div>
    @endif

    {{-- Header & Back Link --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <nav class="flex text-xs text-plum/60 mb-1">
                <a href="{{ route('admin.profile.show') }}" class="hover:text-primary">&larr; Back to My Profile</a>
            </nav>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                    Staff HR Payroll
                </span>
                <span class="text-xs text-plum/50 font-medium">Monthly Salary &amp; Payout Records</span>
            </div>
            <h1 class="text-2xl font-serif font-bold text-plum mt-0.5">
                My Salary &amp; Payslip Register
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <a 
                href="{{ route('admin.attendance.mark') }}" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold border border-plum/20 bg-white text-plum hover:bg-plum/5 shadow-xs transition flex items-center gap-1.5"
            >
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                <span>Check My Attendance (QR)</span>
            </a>
        </div>
    </div>

    {{-- 4 Stat Metric Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="glass-card rounded-2xl p-4 sm:p-5 border border-white/80 bg-white/90 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-plum/50 block mb-1">Base Monthly Pay</span>
            <div class="text-xl sm:text-2xl font-serif font-black text-plum">
                ₹{{ number_format($currentBaseSalary, 2) }}
            </div>
            <span class="text-[11px] text-plum/60 mt-1 block">Fixed showroom monthly rate</span>
        </div>

        <div class="glass-card rounded-2xl p-4 sm:p-5 border border-white/80 bg-white/90 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700/70 block mb-1">Received ({{ now()->year }} YTD)</span>
            <div class="text-xl sm:text-2xl font-serif font-black text-emerald-700">
                ₹{{ number_format($totalEarnedYtd, 2) }}
            </div>
            <span class="text-[11px] text-emerald-800/60 mt-1 block">Total credited this calendar year</span>
        </div>

        <div class="glass-card rounded-2xl p-4 sm:p-5 border border-white/80 bg-white/90 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700/70 block mb-1">Pending Advances</span>
            <div class="text-xl sm:text-2xl font-serif font-black text-amber-700">
                ₹{{ number_format($pendingAdvances, 2) }}
            </div>
            <span class="text-[11px] text-amber-800/60 mt-1 block">Active showroom advance (peshgi)</span>
        </div>

        <div class="glass-card rounded-2xl p-4 sm:p-5 border border-white/80 bg-white/90 shadow-sm">
            <span class="text-[10px] font-bold uppercase tracking-wider text-primary-700/70 block mb-1">Paid Leaves ({{ now()->year }})</span>
            <div class="text-xl sm:text-2xl font-serif font-black text-primary">
                {{ $approvedLeavesCount }} Days
            </div>
            <span class="text-[11px] text-plum/60 mt-1 block">Approved festival &amp; casual leaves</span>
        </div>

    </div>

    {{-- Monthly Payslips History Table --}}
    <div class="glass-card rounded-3xl p-5 sm:p-7 border border-white/80 bg-white/95 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-plum/10">
            <div>
                <h2 class="font-serif font-bold text-plum text-base">Monthly Salary Register &amp; Printable Payslips</h2>
                <p class="text-xs text-plum/60">Aapki monthly attendance, bonus, deductions, aur official slip download karne ka record.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-primary-50 text-primary border border-primary-100">
                {{ $salaries->total() }} Slip {{ Str::plural('Record', $salaries->total()) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-plum/10 text-plum/60 font-bold uppercase text-[10px]">
                        <th class="py-3 px-3">Month</th>
                        <th class="py-3 px-3">Working Days</th>
                        <th class="py-3 px-3">Attendance</th>
                        <th class="py-3 px-3">Base Pay</th>
                        <th class="py-3 px-3">Incentive</th>
                        <th class="py-3 px-3">Deductions</th>
                        <th class="py-3 px-3 font-bold text-plum">Net Salary</th>
                        <th class="py-3 px-3">Payment Mode</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-right">Official Payslip</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-plum/5">
                    @forelse($salaries as $sal)
                        <tr class="hover:bg-plum/5 transition">
                            <td class="py-3.5 px-3 font-serif font-bold text-plum">
                                {{ $sal->month_label }}
                            </td>
                            <td class="py-3.5 px-3 text-plum/70 font-mono">
                                {{ $sal->working_days }} Days
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="font-semibold text-emerald-700">{{ $sal->days_present }}P</span>
                                @if($sal->days_half > 0)
                                    <span class="text-amber-600 font-semibold">&bull; {{ $sal->days_half }}H</span>
                                @endif
                                @if($sal->paid_leaves > 0)
                                    <span class="text-primary font-semibold">&bull; {{ $sal->paid_leaves }}L</span>
                                @endif
                                @if($sal->days_absent > 0)
                                    <span class="text-rose-600 font-semibold">&bull; {{ $sal->days_absent }}A</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 font-mono text-plum/80">
                                ₹{{ number_format((float)$sal->base_salary, 2) }}
                            </td>
                            <td class="py-3.5 px-3 font-mono text-emerald-700">
                                {{ $sal->bonus_incentive > 0 ? '+₹' . number_format((float)$sal->bonus_incentive, 2) : '--' }}
                            </td>
                            <td class="py-3.5 px-3 font-mono text-rose-600">
                                {{ $sal->deductions > 0 ? '-₹' . number_format((float)$sal->deductions, 2) : '₹0.00' }}
                            </td>
                            <td class="py-3.5 px-3 font-serif font-black text-plum text-sm">
                                ₹{{ number_format((float)$sal->net_salary, 2) }}
                            </td>
                            <td class="py-3.5 px-3 text-plum/70 capitalize">
                                @if($sal->payment_method)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-plum/5">
                                        {{ str_replace('_', ' ', $sal->payment_method) }}
                                    </span>
                                @else
                                    <span class="text-plum/40 italic">Pending</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3">
                                @if($sal->payment_status === 'paid')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Paid
                                    </span>
                                @elseif($sal->payment_status === 'partially_paid')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Partial
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 text-right">
                                <a 
                                    href="{{ route('admin.staff.salaries.payslip', $sal->id) }}" 
                                    target="_blank"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-primary-50 hover:bg-primary-100 text-primary font-bold text-xs border border-primary-200 shadow-xs transition"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span>Print Payslip</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-12 text-center text-xs text-plum/40 italic">
                                Abhi tak aapka koi salary slip register generate nahi hua hai. Admin dwara monthly salary generate karne par slip yahan dikhegi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($salaries->hasPages())
            <div class="pt-4 border-t border-plum/10">
                {{ $salaries->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
