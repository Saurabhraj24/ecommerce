@extends('layouts.admin')

@section('title', 'Staff Attendance & Dynamic QR - Bombay Fashion Admin')

@section('content')
<div class="space-y-6" x-data="{
    manualModal: false,
    selectedUser: null,
    editStatus: 'present',
    editIn: '',
    editOut: '',
    editNotes: '',
    openManual(user, att) {
        this.selectedUser = user;
        if (att) {
            this.editStatus = att.status || 'present';
            this.editIn = att.punch_in_at || '';
            this.editOut = att.punch_out_at || '';
            this.editNotes = att.notes || '';
        } else {
            this.editStatus = 'present';
            this.editIn = '10:00';
            this.editOut = '20:30';
            this.editNotes = 'Manual entry by admin';
        }
        this.manualModal = true;
    }
}">
    {{-- Header with Kiosk Launcher --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-plum flex items-center gap-2">
                <span>Showroom Staff Attendance</span>
                <span class="text-xs font-sans font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                    20s Dynamic QR Active
                </span>
            </h1>
            <p class="text-xs text-plum/70 mt-1">
                Track staff reporting times, late arrivals, shift hours, and manage the live 20-second rotating counter QR code.
            </p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Launch Kiosk Button --}}
            <a 
                href="{{ route('admin.attendance.kiosk') }}" 
                target="_blank"
                class="px-4 py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-md shadow-primary/25 hover:shadow-lg hover:scale-[1.02] transition-all flex items-center gap-2 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                <span>Launch Counter QR Kiosk</span>
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="glass-card rounded-2xl p-4 border border-white/80 bg-white/90 shadow-sm">
            <span class="text-[10px] uppercase font-bold tracking-wider text-plum/50 block">Total Staff</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-serif font-bold text-plum">{{ $totalStaff }}</span>
                <span class="text-[11px] text-plum/60">Registered Team</span>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-white/80 bg-white/90 shadow-sm">
            <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-700 block">Present Today</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-serif font-bold text-emerald-700">{{ $presentCount }}</span>
                <span class="text-[11px] text-emerald-600 font-semibold">On Floor</span>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-white/80 bg-white/90 shadow-sm">
            <span class="text-[10px] uppercase font-bold tracking-wider text-amber-700 block">Late Arrivals</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-serif font-bold text-amber-700">{{ $lateCount }}</span>
                <span class="text-[11px] text-amber-600 font-semibold">After 10:30 AM</span>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-4 border border-white/80 bg-white/90 shadow-sm">
            <span class="text-[10px] uppercase font-bold tracking-wider text-rose-700 block">Absent / Leave</span>
            <div class="flex items-baseline gap-2 mt-1">
                <span class="text-2xl font-serif font-bold text-rose-700">{{ $absentCount }}</span>
                <span class="text-[11px] text-rose-600">Pending punch</span>
            </div>
        </div>
    </div>

    {{-- Date Filter & Actions Bar --}}
    <div class="glass-card rounded-2xl p-4 border border-white/80 bg-white/90 shadow-sm flex flex-wrap items-center justify-between gap-4 text-xs">
        <form method="GET" action="{{ route('admin.attendance.index') }}" class="flex items-center gap-3">
            <label class="font-bold text-plum">Select Date:</label>
            <input 
                type="date" 
                name="date" 
                value="{{ $selectedDate }}" 
                onchange="this.form.submit()"
                class="rounded-xl border border-plum/20 bg-white p-2 text-plum font-semibold focus:ring-primary"
            >
            @if($selectedDate !== date('Y-m-d'))
                <a href="{{ route('admin.attendance.index') }}" class="text-primary font-bold hover:underline">
                    &larr; View Today
                </a>
            @endif
        </form>

        <div class="text-[11px] text-plum/60 flex items-center gap-4">
            <span class="flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> On Time (Before 10:30 AM)
            </span>
            <span class="flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Late (After 10:30 AM)
            </span>
            <span class="flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-rose-400"></span> Absent
            </span>
        </div>
    </div>

    {{-- Attendance Table --}}
    <div class="glass-card rounded-3xl border border-white/80 bg-white/95 shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px] bg-plum/5">
                        <th class="py-3.5 px-4">Staff Member</th>
                        <th class="py-3.5 px-4">Section / Counter</th>
                        <th class="py-3.5 px-4">Punch In</th>
                        <th class="py-3.5 px-4">Punch Out</th>
                        <th class="py-3.5 px-4">Work Duration</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-plum/5">
                    @foreach($staffMembers as $staff)
                        @php
                            $att = $attendances->get($staff->id);
                        @endphp
                        <tr class="hover:bg-plum/5 transition-colors">
                            {{-- Staff Info --}}
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <img 
                                        src="{{ $staff->avatar_url }}" 
                                        alt="{{ $staff->name }}" 
                                        class="w-9 h-9 rounded-xl object-cover border border-plum/10 shrink-0"
                                    >
                                    <div>
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-bold text-plum text-sm">{{ $staff->name }}</span>
                                            @if($staff->employee_id)
                                                <span class="text-[10px] text-plum/50 font-mono font-semibold">{{ $staff->employee_id }}</span>
                                            @endif
                                        </div>
                                        <span class="text-[11px] text-plum/60 block">
                                            {{ $staff->designation ?: ($staff->isAdmin() ? 'Super Admin' : 'Showroom Associate') }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Department --}}
                            <td class="py-3.5 px-4 text-plum/70 font-medium">
                                {{ $staff->department ?: 'Showroom Floor' }}
                            </td>

                            {{-- Punch In Time --}}
                            <td class="py-3.5 px-4">
                                @if($att && $att->punch_in_at)
                                    <span class="font-mono font-bold {{ $att->status === 'late' ? 'text-amber-700' : 'text-emerald-700' }}">
                                        {{ \Carbon\Carbon::parse($att->punch_in_at)->format('h:i A') }}
                                    </span>
                                    <span class="text-[10px] text-plum/40 block">via {{ $att->verification_method === 'dynamic_qr' ? 'Dynamic QR' : 'Admin Manual' }}</span>
                                @else
                                    <span class="text-plum/30 font-mono">—</span>
                                @endif
                            </td>

                            {{-- Punch Out Time --}}
                            <td class="py-3.5 px-4">
                                @if($att && $att->punch_out_at)
                                    <span class="font-mono font-bold text-plum">
                                        {{ \Carbon\Carbon::parse($att->punch_out_at)->format('h:i A') }}
                                    </span>
                                @else
                                    <span class="text-plum/30 font-mono">—</span>
                                @endif
                            </td>

                            {{-- Hours Worked --}}
                            <td class="py-3.5 px-4">
                                @if($att && $att->total_hours > 0)
                                    <span class="font-bold text-plum font-mono">{{ $att->total_hours }} hrs</span>
                                @elseif($att && $att->punch_in_at && !$att->punch_out_at)
                                    <span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 animate-pulse">
                                        Active Shift
                                    </span>
                                @else
                                    <span class="text-plum/30">—</span>
                                @endif
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-3.5 px-4 text-center">
                                @if($att)
                                    @if($att->status === 'present')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Present
                                        </span>
                                    @elseif($att->status === 'late')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            Late Arrival
                                        </span>
                                    @elseif($att->status === 'half_day')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-200">
                                            Half Day
                                        </span>
                                    @elseif($att->status === 'leave')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                            Approved Leave
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            Absent
                                        </span>
                                    @endif
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">
                                        Not Marked
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-4 text-right">
                                <button 
                                    type="button" 
                                    @click="openManual({{ json_encode($staff) }}, {{ json_encode($att) }})"
                                    class="px-2.5 py-1.5 rounded-lg border border-plum/20 text-plum hover:bg-plum/5 hover:text-primary font-semibold text-[11px] transition-all cursor-pointer"
                                >
                                    Adjust / Mark
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Manual Attendance Modal --}}
    <div 
        x-show="manualModal" 
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
    >
        <div 
            @click.away="manualModal = false"
            class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-plum/10 text-xs space-y-4"
        >
            <div class="flex items-center justify-between pb-3 border-b border-plum/10">
                <h3 class="font-serif font-bold text-plum text-sm">
                    Manual Attendance: <span class="text-primary" x-text="selectedUser?.name"></span>
                </h3>
                <button type="button" @click="manualModal = false" class="text-plum/40 hover:text-plum text-lg">&times;</button>
            </div>

            <form action="{{ route('admin.attendance.manual') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="user_id" :value="selectedUser?.id">
                <input type="hidden" name="date" value="{{ $selectedDate }}">

                <div>
                    <label class="block font-semibold text-plum mb-1">Status</label>
                    <select name="status" x-model="editStatus" class="w-full rounded-xl border border-plum/20 p-2.5 text-plum font-bold focus:ring-primary">
                        <option value="present">Present (On Time)</option>
                        <option value="late">Late Arrival</option>
                        <option value="half_day">Half Day</option>
                        <option value="leave">Approved Leave</option>
                        <option value="absent">Absent</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-plum mb-1">Punch In Time</label>
                        <input type="time" name="punch_in_at" x-model="editIn" class="w-full rounded-xl border border-plum/20 p-2 text-plum focus:ring-primary">
                    </div>
                    <div>
                        <label class="block font-semibold text-plum mb-1">Punch Out Time</label>
                        <input type="time" name="punch_out_at" x-model="editOut" class="w-full rounded-xl border border-plum/20 p-2 text-plum focus:ring-primary">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Remarks / Reason</label>
                    <input type="text" name="notes" x-model="editNotes" placeholder="e.g. Forgot phone, reported to showroom in person" class="w-full rounded-xl border border-plum/20 p-2.5 text-plum focus:ring-primary">
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-plum/10">
                    <button type="button" @click="manualModal = false" class="px-4 py-2 rounded-xl border border-plum/20 text-plum hover:bg-plum/5">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-primary text-white font-bold hover:bg-primary-700 shadow">Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
