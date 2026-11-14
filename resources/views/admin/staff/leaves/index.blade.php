@extends('layouts.admin')

@section('title', 'Staff Vacation & Leaves (छुट्टी रजिस्टर) - Bombay Fashion Admin')

@section('content')
<div class="space-y-6" x-data="{
    leaveModalOpen: false,
    selectedLeave: null,
    statusModalOpen: false,
    newStatus: 'approved',
    adminNotes: ''
}">
    {{-- Header Banner --}}
    <div class="glass-card rounded-3xl p-6 sm:p-7 border border-white/80 bg-gradient-to-r from-pink-50/60 via-white to-amber-50/40 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-pink-100 text-pink-800">
                    HR &amp; Attendance
                </span>
                <span class="text-xs text-plum/50 font-medium">Bombay Fashion Amritsar</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-serif font-bold text-plum mt-1">
                Staff Vacation &amp; Leaves (छुट्टी रजिस्टर)
            </h1>
            <p class="text-xs text-plum/70 mt-1">
                Sales team, tailors aur billing staff ki casual leaves, bimari chutti, shaadi/tyohar leaves aur salary attendance sync.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <button 
                type="button"
                @click="leaveModalOpen = true"
                class="px-4 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-md shadow-primary/25 hover:bg-primary-700 transition flex items-center gap-2 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span>Apply / Sanction Leave</span>
            </button>
        </div>
    </div>

    {{-- Metrics KPI Bar --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass-card rounded-2xl p-4 border border-plum/10 bg-white shadow-xs">
            <span class="text-[11px] font-bold text-plum/60 uppercase tracking-wider block">Staff On Leave Today</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-serif font-bold text-plum">
                    {{ $activeLeavesToday->count() }}
                </span>
                <span class="text-xs text-plum/50">employees away</span>
            </div>
            @if($activeLeavesToday->isNotEmpty())
                <div class="mt-2 flex flex-wrap gap-1">
                    @foreach($activeLeavesToday as $active)
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-pink-100 text-pink-800">
                            {{ $active->user->name }} ({{ $active->leave_type_label }})
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="glass-card rounded-2xl p-4 border border-amber-200 bg-amber-50/40 shadow-xs">
            <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wider block">Pending Approval Requests</span>
            <span class="text-2xl font-serif font-bold text-amber-800 mt-1 block">
                {{ $pendingCount }}
            </span>
            <span class="text-[10px] text-amber-800 font-medium mt-0.5 block">
                Awaiting store manager approval
            </span>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-emerald-200 bg-emerald-50/40 shadow-xs">
            <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">Approved This Month</span>
            <span class="text-2xl font-serif font-bold text-emerald-700 mt-1 block">
                {{ $thisMonthCount }}
            </span>
            <span class="text-[10px] text-emerald-800 font-medium mt-0.5 block">
                Sanctioned leaves in {{ now()->format('F Y') }}
            </span>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-plum/10 shadow-xs text-xs font-semibold">
        <div class="flex items-center space-x-2">
            <span class="text-plum/50 text-[11px]">Filter Status:</span>
            <a href="{{ route('admin.staff.leaves.index', ['status' => 'all']) }}" class="px-3 py-1.5 rounded-xl transition {{ $statusFilter === 'all' ? 'bg-primary text-white font-bold' : 'text-plum hover:bg-plum/5' }}">
                All Leaves
            </a>
            <a href="{{ route('admin.staff.leaves.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-xl transition {{ $statusFilter === 'pending' ? 'bg-amber-500 text-white font-bold' : 'text-plum hover:bg-plum/5' }}">
                Pending ({{ $pendingCount }})
            </a>
            <a href="{{ route('admin.staff.leaves.index', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-xl transition {{ $statusFilter === 'approved' ? 'bg-emerald-600 text-white font-bold' : 'text-plum hover:bg-plum/5' }}">
                Approved
            </a>
            <a href="{{ route('admin.staff.leaves.index', ['status' => 'rejected']) }}" class="px-3 py-1.5 rounded-xl transition {{ $statusFilter === 'rejected' ? 'bg-rose-600 text-white font-bold' : 'text-plum hover:bg-plum/5' }}">
                Rejected
            </a>
        </div>

        <form action="{{ route('admin.staff.leaves.index') }}" method="GET" class="flex items-center gap-2">
            <input type="hidden" name="status" value="{{ $statusFilter }}">
            <select name="user_id" onchange="this.form.submit()" class="rounded-xl border border-plum/20 bg-white px-3 py-1.5 text-xs text-plum">
                <option value="">All Staff Members</option>
                @foreach($staffMembers as $staff)
                    <option value="{{ $staff->id }}" {{ $staffFilter == $staff->id ? 'selected' : '' }}>
                        {{ $staff->name }} ({{ $staff->designation ?? 'Staff' }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Leaves Table --}}
    <div class="glass-card rounded-3xl border border-plum/10 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-plum/5 text-plum/70 uppercase text-[10px] font-bold border-b border-plum/10">
                    <tr>
                        <th class="px-4 py-3">Staff Member</th>
                        <th class="px-3 py-3">Leave Type</th>
                        <th class="px-3 py-3">Date Duration</th>
                        <th class="px-3 py-3 text-center">Days</th>
                        <th class="px-4 py-3">Reason / Details</th>
                        <th class="px-3 py-3 text-center">Status</th>
                        <th class="px-3 py-3">Approved By</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-plum/10 text-plum font-medium">
                    @forelse($leaves as $leave)
                        <tr class="hover:bg-plum/[0.02] transition">
                            <td class="px-4 py-3.5">
                                <div class="flex items-center space-x-3">
                                    <img 
                                        src="{{ $leave->user->avatar_url }}" 
                                        alt="{{ $leave->user->name }}" 
                                        class="w-8 h-8 rounded-xl object-cover border border-plum/10 shrink-0"
                                    >
                                    <div>
                                        <div class="font-bold text-plum">{{ $leave->user->name }}</div>
                                        <div class="text-[10px] text-plum/50">{{ $leave->user->designation ?? 'Staff' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-3 py-3.5">
                                @php
                                    $badgeColor = match($leave->leave_type) {
                                        'casual' => 'bg-indigo-100 text-indigo-800',
                                        'sick' => 'bg-rose-100 text-rose-800',
                                        'festival' => 'bg-amber-100 text-amber-800',
                                        'unpaid' => 'bg-slate-100 text-slate-700',
                                        default => 'bg-plum/10 text-plum',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $badgeColor }}">
                                    {{ $leave->leave_type_label }}
                                </span>
                            </td>

                            <td class="px-3 py-3.5 font-mono text-[11px]">
                                {{ $leave->start_date->format('d M Y') }}
                                @if($leave->start_date->ne($leave->end_date))
                                    &rarr; {{ $leave->end_date->format('d M Y') }}
                                @endif
                            </td>

                            <td class="px-3 py-3.5 text-center font-bold">
                                {{ $leave->days_count }} {{ Str::plural('day', $leave->days_count) }}
                            </td>

                            <td class="px-4 py-3.5 max-w-xs">
                                <p class="text-plum truncate" title="{{ $leave->reason }}">{{ $leave->reason }}</p>
                                @if($leave->admin_notes)
                                    <p class="text-[10px] text-plum/50 italic mt-0.5 truncate">Note: {{ $leave->admin_notes }}</p>
                                @endif
                            </td>

                            <td class="px-3 py-3.5 text-center">
                                @if($leave->status === 'approved')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 flex items-center justify-center gap-1 w-max mx-auto">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span>Approved</span>
                                    </span>
                                @elseif($leave->status === 'rejected')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 block w-max mx-auto">
                                        Rejected
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 block w-max mx-auto animate-pulse">
                                        Pending Review
                                    </span>
                                @endif
                            </td>

                            <td class="px-3 py-3.5 text-plum/60 text-[11px]">
                                {{ $leave->approver?->name ?? '—' }}
                            </td>

                            <td class="px-4 py-3.5 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    @if($leave->status === 'pending')
                                        <form action="{{ route('admin.staff.leaves.update-status', $leave->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button 
                                                type="submit" 
                                                class="px-2 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] shadow-xs cursor-pointer"
                                                title="Approve Leave"
                                            >
                                                Approve
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.staff.leaves.update-status', $leave->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button 
                                                type="submit" 
                                                class="px-2 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-[10px] shadow-xs cursor-pointer"
                                                title="Reject Leave"
                                            >
                                                Reject
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.staff.leaves.destroy', $leave->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this leave record?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded text-plum/30 hover:text-rose-600 cursor-pointer" title="Delete Leave">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-plum/50">
                                No leave applications found for this filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leaves->hasPages())
            <div class="p-4 border-t border-plum/10">
                {{ $leaves->links() }}
            </div>
        @endif
    </div>

    {{-- Apply / Record Leave Modal --}}
    <div 
        x-show="leaveModalOpen" 
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto bg-plum/60 backdrop-blur-xs flex items-center justify-center p-4"
    >
        <div 
            @click.away="leaveModalOpen = false"
            class="glass-card rounded-3xl p-6 sm:p-7 border border-white/80 bg-white shadow-2xl max-w-lg w-full space-y-4"
        >
            <div class="flex items-center justify-between pb-3 border-b border-plum/10">
                <div>
                    <h3 class="font-serif font-bold text-plum text-lg">Apply / Sanction Staff Leave</h3>
                    <p class="text-xs text-plum/60">Record approved or pending vacation/chutti for showroom staff.</p>
                </div>
                <button @click="leaveModalOpen = false" class="text-plum/40 hover:text-plum text-xl">&times;</button>
            </div>

            <form action="{{ route('admin.staff.leaves.store') }}" method="POST" class="space-y-4 text-xs font-semibold text-plum">
                @csrf

                <div>
                    <label class="block mb-1">Staff Member *</label>
                    <select name="user_id" required class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-xs text-plum focus:ring-primary font-medium">
                        <option value="">Select Employee</option>
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }} — {{ $staff->designation ?? 'Staff' }} (ID: {{ $staff->employee_id ?? 'BF-EMP-' . $staff->id }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block mb-1">Leave Type *</label>
                        <select name="leave_type" required class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-xs text-plum focus:ring-primary">
                            <option value="casual">Casual Leave (CL - Paid)</option>
                            <option value="sick">Sick / Medical Leave (SL - Paid)</option>
                            <option value="festival">Festival / Wedding Leave (Paid)</option>
                            <option value="unpaid">Unpaid Leave (LWP - Salary Deducted)</option>
                            <option value="emergency">Emergency Family Leave</option>
                        </select>
                    </div>

                    <div>
                        <label class="block mb-1">Initial Status *</label>
                        <select name="status" required class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-xs text-plum focus:ring-primary">
                            <option value="approved">Sanctioned &amp; Approved (Syncs Attendance)</option>
                            <option value="pending">Pending Admin Approval</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block mb-1">From Date *</label>
                        <input type="date" name="start_date" required value="{{ now()->toDateString() }}" class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-xs text-plum focus:ring-primary">
                    </div>

                    <div>
                        <label class="block mb-1">To Date *</label>
                        <input type="date" name="end_date" required value="{{ now()->toDateString() }}" class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-xs text-plum focus:ring-primary">
                    </div>
                </div>

                <div>
                    <label class="block mb-1">Reason for Leave *</label>
                    <input type="text" name="reason" required placeholder="e.g. Sister's wedding ceremony in Jalandhar / High fever" class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-xs text-plum focus:ring-primary">
                </div>

                <div>
                    <label class="block mb-1">Admin / Store Manager Notes</label>
                    <input type="text" name="admin_notes" placeholder="e.g. Counter handover arranged with cashier" class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-xs text-plum focus:ring-primary">
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2 border-t border-plum/10">
                    <button type="button" @click="leaveModalOpen = false" class="px-4 py-2 rounded-xl border border-plum/20 text-plum font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold shadow-md shadow-primary/25 transition">
                        Submit Leave Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
