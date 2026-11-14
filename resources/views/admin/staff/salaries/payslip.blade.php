<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $salary->user->name }} - {{ $salary->month_label }} - Bombay Fashion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white !important; font-size: 12px; }
            .no-print { display: none !important; }
            .print-border { border: 1px solid #1e1124 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8 text-slate-800 antialiased font-sans">

    {{-- Control Bar --}}
    <div class="max-w-3xl mx-auto mb-4 flex items-center justify-between no-print">
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.staff.salaries.index', ['month' => $salary->month]) }}" class="px-4 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold hover:bg-slate-50 transition">
                &larr; Back to Salary Register
            </a>
        @else
            <a href="{{ route('admin.profile.salary') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-300 text-xs font-semibold hover:bg-slate-50 transition">
                &larr; Back to My Salary
            </a>
        @endif
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-[#c2185b] hover:bg-[#ad1457] text-white text-xs font-bold shadow-md transition flex items-center space-x-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Print Official Payslip (Ctrl+P)</span>
            </button>
        </div>
    </div>

    {{-- Official Payslip Container --}}
    <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl border border-slate-200 p-8 sm:p-10 space-y-6 print:shadow-none print:border-none print:p-0">
        
        {{-- Showroom Header --}}
        <div class="border-b-2 border-[#3b2a3e] pb-6 flex items-start justify-between">
            <div>
                <span class="text-[10px] font-bold tracking-widest uppercase text-[#c2185b] block mb-1">
                    Premium Bridal Wear &amp; Apparel Store
                </span>
                <h1 class="text-2xl sm:text-3xl font-serif font-black text-[#3b2a3e] tracking-tight">
                    BOMBAY FASHION
                </h1>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                    Showroom No. 12, Guru Bazar, Near Golden Temple Road, Amritsar, Punjab - 143001<br>
                    Phone: +91 98765 43210 &bull; Email: care@bombayfashion.com &bull; GSTIN: 03AAAAA0000A1Z5
                </p>
            </div>
            <div class="text-right">
                <div class="inline-block px-3 py-1 rounded-lg bg-pink-50 border border-pink-200 text-xs font-bold text-[#c2185b] uppercase tracking-wider mb-2">
                    Salary Slip
                </div>
                <div class="text-sm font-bold text-slate-900">{{ $salary->month_label }}</div>
                <div class="text-[10px] text-slate-500 font-mono">Ref: BF-PAY-{{ $salary->month }}-{{ str_pad($salary->id, 4, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>

        {{-- Employee & Attendance Details Grid --}}
        <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-xs border border-slate-200 rounded-xl p-4 bg-slate-50/50">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Employee Name</span>
                <span class="font-bold text-slate-900 text-sm">{{ $salary->user->name }}</span>
            </div>

            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Staff / Employee ID</span>
                <span class="font-mono font-bold text-slate-800">{{ $salary->user->employee_id ?? 'BF-EMP-' . $salary->user->id }}</span>
            </div>

            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Designation / Role</span>
                <span class="font-semibold text-slate-800">{{ $salary->user->designation ?? 'Sales Executive' }}</span>
            </div>

            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Department</span>
                <span class="font-semibold text-slate-800">{{ $salary->user->department ?? 'Showroom Sales Floor' }}</span>
            </div>

            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Bank Account &amp; IFSC</span>
                <span class="font-mono text-slate-700">
                    {{ $salary->user->bank_name ?? 'Showroom Desk' }} 
                    @if($salary->user->bank_account_no) - {{ Str::mask($salary->user->bank_account_no, '*', 0, -4) }} @endif
                    @if($salary->user->bank_ifsc) ({{ $salary->user->bank_ifsc }}) @endif
                </span>
            </div>

            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">UPI ID / Shift</span>
                <span class="font-mono text-slate-700">
                    {{ $salary->user->upi_id ?? 'Cash Mode' }} &bull; Shift: {{ $salary->user->shift_timings ?? '10:00 AM - 08:30 PM' }}
                </span>
            </div>

            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Attendance Summary</span>
                <span class="font-semibold text-slate-800">
                    {{ $salary->days_present }} Present &bull; {{ $salary->paid_leaves }} Paid Leave &bull; {{ $salary->days_absent }} Absent
                </span>
            </div>

            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Total Working Days</span>
                <span class="font-semibold text-slate-800">{{ $salary->working_days }} Days in Month</span>
            </div>
        </div>

        {{-- Earnings & Deductions Breakdown Table --}}
        <div class="border border-slate-300 rounded-xl overflow-hidden">
            <div class="grid grid-cols-2 bg-[#3b2a3e] text-white text-xs font-bold uppercase tracking-wider py-2.5 px-4">
                <div>Earnings (कमाई)</div>
                <div class="text-right">Deductions (कटौती)</div>
            </div>

            <div class="grid grid-cols-2 divide-x divide-slate-200 text-xs">
                {{-- Earnings Column --}}
                <div class="p-4 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Basic Monthly Salary</span>
                        <span class="font-mono font-semibold">₹{{ number_format($salary->base_salary, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Festive Bonus / Sales Commission</span>
                        <span class="font-mono font-semibold text-emerald-700">+₹{{ number_format($salary->bonus_incentive, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Paid Leave &amp; Holidays Allowance</span>
                        <span class="font-mono font-semibold">Included</span>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex items-center justify-between font-bold text-slate-900">
                        <span>Total Gross Earnings:</span>
                        <span class="font-mono">₹{{ number_format($salary->base_salary + $salary->bonus_incentive, 2) }}</span>
                    </div>
                </div>

                {{-- Deductions Column --}}
                <div class="p-4 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Unpaid Absence Deductions</span>
                        @php
                            $perDay = $salary->working_days > 0 ? ($salary->base_salary / $salary->working_days) : 0;
                            $unpaidLoss = max(0, $salary->days_absent * $perDay);
                        @endphp
                        <span class="font-mono font-semibold text-rose-600">
                            -₹{{ number_format($unpaidLoss, 2) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Salary Advance (Peshgi Recovery)</span>
                        <span class="font-mono font-semibold text-rose-600">
                            -₹{{ number_format(max(0, $salary->deductions - $unpaidLoss), 2) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">TDS / Professional Tax</span>
                        <span class="font-mono font-semibold">₹0.00</span>
                    </div>

                    <div class="pt-3 border-t border-slate-200 flex items-center justify-between font-bold text-rose-700">
                        <span>Total Deductions:</span>
                        <span class="font-mono">-₹{{ number_format($salary->deductions, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Net Pay Banner --}}
            <div class="bg-emerald-50 border-t border-emerald-200 p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <span class="text-[11px] uppercase tracking-wider font-bold text-emerald-800 block">
                        Net Payable Salary (कुल देय वेतन)
                    </span>
                    <span class="text-xs text-emerald-700 italic">
                        Payment Status: {{ strtoupper($salary->payment_status) }} 
                        @if($salary->payment_method) (via {{ strtoupper($salary->payment_method) }}) @endif
                        @if($salary->payment_reference) &bull; UTR: {{ $salary->payment_reference }} @endif
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-2xl sm:text-3xl font-serif font-black text-emerald-800 font-mono">
                        ₹{{ number_format($salary->net_salary, 2) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Signatures & Acknowledgement --}}
        <div class="pt-12 grid grid-cols-2 gap-8 text-xs text-slate-600">
            <div class="text-center">
                <div class="border-b border-slate-400 w-48 mx-auto mb-2"></div>
                <span class="font-bold text-slate-800">Employee Signature</span>
                <p class="text-[10px] text-slate-500">I acknowledge receipt of full payment.</p>
            </div>

            <div class="text-center">
                <div class="border-b border-slate-400 w-48 mx-auto mb-2"></div>
                <span class="font-bold text-slate-800">Bombay Fashion Amritsar</span>
                <p class="text-[10px] text-slate-500">Authorized Signatory / Store Stamp</p>
            </div>
        </div>

        {{-- System Footer --}}
        <div class="pt-6 border-t border-slate-200 text-center text-[10px] text-slate-400">
            This is a computer-generated salary slip from the Bombay Fashion Retail Management System. Amritsar Showroom.
        </div>
    </div>
</body>
</html>
