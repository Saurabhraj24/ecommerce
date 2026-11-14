@extends('layouts.app')

@section('title', 'Confirm Staff Attendance - Bombay Fashion Amritsar')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 bg-gradient-to-b from-festive-ivory via-festive-blush/20 to-festive-ivory">
    <div class="max-w-md w-full glass-card rounded-3xl p-6 sm:p-8 border border-white/90 bg-white/95 shadow-2xl space-y-6 text-xs">
        
        <div class="text-center space-y-2">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-tr from-festive-rose to-primary-700 text-white flex items-center justify-center text-2xl shadow-lg shadow-festive-rose/25">
                📸
            </div>
            <h1 class="text-xl sm:text-2xl font-serif font-bold text-festive-plum">
                Confirm Attendance Punch
            </h1>
            <p class="text-xs text-festive-plum/70">
                You have scanned the live Bombay Fashion showroom counter QR code. Select your staff account to confirm punch.
            </p>
        </div>

        <form action="{{ route('attendance.submit-punch') }}" method="POST" class="space-y-4" id="punchForm">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="client_device_id" id="client_device_id" value="{{ $deviceId ?? '' }}">

            {{-- 1-Mobile-1-Staff Security Banner --}}
            <div class="p-3 rounded-xl bg-festive-plum/5 border border-festive-plum/10 text-left flex items-start gap-2.5">
                <span class="text-base leading-none">📱</span>
                <div>
                    <span class="font-bold text-[11px] text-festive-plum block">1-Mobile Anti-Proxy Protection</span>
                    <span class="text-[10px] text-festive-plum/70 block leading-tight">
                        Attendance will be stamped with this phone's device signature. You cannot punch for another colleague from this device.
                    </span>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-festive-plum mb-1.5">Select Your Name *</label>
                <select name="user_id" required class="w-full rounded-xl border border-festive-plum/20 bg-white p-3 text-festive-plum font-bold focus:ring-primary">
                    <option value="">-- Choose Your Staff Account --</option>
                    @foreach($staffMembers as $staff)
                        <option value="{{ $staff->id }}">
                            {{ $staff->name }} ({{ $staff->designation ?: 'Staff' }} - {{ $staff->employee_id ?: 'ID #' . $staff->id }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-festive-plum mb-1">Verify Last 4 Digits of Your Phone Number *</label>
                <input 
                    type="password" 
                    maxlength="4" 
                    pattern="[0-9]{4}" 
                    name="phone_last4" 
                    required 
                    placeholder="e.g. 3210"
                    class="w-full rounded-xl border border-festive-plum/20 bg-white p-3 text-center text-lg font-mono tracking-widest text-festive-plum focus:ring-primary"
                >
                <span class="text-[11px] text-festive-plum/50 block mt-1">Proof of phone possession for showroom security.</span>
            </div>

            <button 
                type="submit" 
                class="w-full py-3.5 rounded-xl bg-primary text-white font-bold text-sm shadow-lg shadow-primary/25 hover:bg-primary-700 transition-all cursor-pointer flex items-center justify-center gap-2"
            >
                <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Confirm My Punch Now</span>
            </button>
        </form>

        <div class="pt-4 border-t border-festive-plum/10 text-center text-[11px] text-festive-plum/50">
            Bombay Fashion Showroom • Mahila College Road, Amritsar
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('client_device_id');
        let storedId = localStorage.getItem('bf_attendance_device_token');
        if (!storedId) {
            storedId = input.value || ('mob_' + Math.random().toString(36).substring(2, 12) + '_' + Date.now().toString(36));
            localStorage.setItem('bf_attendance_device_token', storedId);
        }
        input.value = storedId;
    });
</script>
@endsection
