@extends('layouts.app')

@section('title', 'Attendance Result - Bombay Fashion Amritsar')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 bg-gradient-to-b from-festive-ivory via-festive-blush/20 to-festive-ivory">
    <div class="max-w-md w-full glass-card rounded-3xl p-6 sm:p-8 border border-white/90 bg-white/95 shadow-2xl text-center space-y-6 text-xs">
        
        @if($success)
            {{-- Success Icon --}}
            <div class="w-16 h-16 mx-auto rounded-3xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-3xl shadow-lg shadow-emerald-500/20 ring-4 ring-emerald-500/20 animate-bounce">
                ✓
            </div>

            <div class="space-y-1">
                <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider {{ ($attendance->status ?? '') === 'late' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                    {{ ($action_type ?? '') === 'PUNCH_OUT' ? 'Punch Out Recorded' : (($attendance->status ?? '') === 'late' ? 'Punch In (Late Arrival)' : 'Punch In (On Time)') }}
                </span>
                <h1 class="text-xl sm:text-2xl font-serif font-bold text-festive-plum">
                    Attendance Logged!
                </h1>
            </div>

            @if(isset($user))
                <div class="p-4 rounded-2xl bg-festive-plum/5 border border-festive-plum/10 flex items-center gap-3 text-left">
                    <img src="{{ $user->avatar_url }}" class="w-12 h-12 rounded-2xl object-cover border border-festive-plum/10 shadow-xs" alt="{{ $user->name }}">
                    <div>
                        <span class="font-bold text-festive-plum text-sm block leading-tight">{{ $user->name }}</span>
                        <span class="text-[11px] text-festive-plum/60">{{ $user->designation ?: 'Staff Member' }}</span>
                        <span class="text-[10px] text-festive-rose font-mono block mt-0.5">{{ $user->employee_id ?: 'Showroom Associate' }}</span>
                    </div>
                </div>
            @endif

            <p class="text-xs text-festive-plum/80 leading-relaxed font-medium bg-festive-blush/40 p-3 rounded-xl border border-festive-rose/10">
                {{ $message }}
            </p>

            @if(isset($attendance) && $attendance->total_hours > 0)
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="p-3 rounded-xl bg-white border border-festive-plum/10 shadow-xs">
                        <span class="text-[10px] text-festive-plum/50 block">In Time</span>
                        <span class="font-mono font-bold text-sm text-festive-plum">{{ \Carbon\Carbon::parse($attendance->punch_in_at)->format('h:i A') }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-white border border-festive-plum/10 shadow-xs">
                        <span class="text-[10px] text-festive-plum/50 block">Shift Duration</span>
                        <span class="font-mono font-bold text-sm text-emerald-700">{{ $attendance->total_hours }} Hours</span>
                    </div>
                </div>
            @endif

        @else
            {{-- Error State --}}
            <div class="w-16 h-16 mx-auto rounded-3xl bg-rose-100 text-rose-700 flex items-center justify-center text-3xl shadow-lg shadow-rose-500/20">
                ✕
            </div>

            <div class="space-y-1">
                <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider {{ !empty($device_blocked) ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                    {{ !empty($device_blocked) ? 'Anti-Proxy Policy Active' : 'Verification Failed' }}
                </span>
                <h1 class="text-xl sm:text-2xl font-serif font-bold text-festive-plum">
                    {{ !empty($device_blocked) ? 'Device Restriction' : 'Scan Expired or Invalid' }}
                </h1>
            </div>

            <p class="text-xs text-rose-800 leading-relaxed bg-rose-50 p-4 rounded-2xl border border-rose-200">
                {{ $message }}
            </p>
        @endif

        <div class="pt-4 border-t border-festive-plum/10 flex items-center justify-center gap-3">
            <a href="{{ route('home') }}" class="px-5 py-2.5 rounded-xl border border-festive-plum/20 text-festive-plum font-semibold hover:bg-festive-blush transition-all">
                Return to Store Home
            </a>
            @if(Auth::check() && Auth::user()->canAccessAdmin())
                <a href="{{ route('admin.attendance.index') }}" class="px-5 py-2.5 rounded-xl bg-primary text-white font-bold hover:bg-primary-700 shadow transition-all">
                    View Attendance Dashboard
                </a>
            @endif
        </div>
    </div>
</div>

@if(isset($attendance) && $attendance->device_id)
<script>
    localStorage.setItem('bf_attendance_device_token', '{{ $attendance->device_id }}');
</script>
@endif
@endsection
