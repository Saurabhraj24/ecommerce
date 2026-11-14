@extends('layouts.admin')

@section('title', 'Mark My Attendance - Bombay Fashion Amritsar')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto" x-data="attendanceScanner()">
    
    {{-- Header Banner --}}
    <div class="glass-card rounded-2xl p-5 border border-white/80 bg-gradient-to-r from-blush/40 via-white to-cream-100/60 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                    Live Attendance Desk
                </span>
                <span class="text-xs text-plum/50 font-medium">Bombay Fashion Amritsar</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-serif font-bold text-plum mt-1">
                Mark My Attendance &amp; QR Scanner
            </h1>
            <p class="text-xs text-plum/70 mt-0.5">
                Admin screen par chal rahe live 20-second dynamic QR code ko camera se scan karein aur apni haaziri lagayein.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button 
                type="button" 
                @click="toggleScanner()" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold bg-primary text-white shadow-md hover:bg-primary-700 transition-all flex items-center gap-2 cursor-pointer"
            >
                <span x-show="!scannerActive">📷 Open Camera Scanner</span>
                <span x-show="scannerActive">✕ Close Camera</span>
            </button>
        </div>
    </div>

    {{-- Today's Status Banner --}}
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/90 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-plum/10">
            <div>
                <span class="text-[10px] uppercase font-bold tracking-wider text-plum/50 block">Today's Shift Status</span>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-xl sm:text-2xl font-serif font-bold text-plum">
                        {{ \Carbon\Carbon::now('Asia/Kolkata')->format('l, d M Y') }}
                    </span>
                    @if(!$todayAttendance)
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                            🔴 Not Punched Yet
                        </span>
                    @elseif($todayAttendance->punch_in_at && !$todayAttendance->punch_out_at)
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 animate-pulse">
                            🟢 Shift Active (Punched In)
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                            🔵 Shift Completed (Punched Out)
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3 bg-plum/5 p-3 rounded-2xl text-xs">
                <div class="w-10 h-10 rounded-xl bg-primary text-white flex items-center justify-center font-bold text-sm shadow-xs shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <span class="font-bold text-plum block leading-tight">{{ $user->name }}</span>
                    <span class="text-[11px] text-plum/60">{{ $user->designation ?: 'Showroom Staff' }}</span>
                    <span class="text-[10px] text-primary font-mono block mt-0.5">{{ $user->employee_id ?: 'ID #' . $user->id }}</span>
                </div>
            </div>
        </div>

        {{-- Today's Punch Metrics --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
            <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-50 to-white border border-emerald-200/60 shadow-xs">
                <div class="flex items-center justify-between text-xs text-emerald-800/70 mb-1">
                    <span class="font-bold uppercase tracking-wider text-[10px]">Punch In Time</span>
                    <span>⏰</span>
                </div>
                <div class="text-xl sm:text-2xl font-mono font-bold text-emerald-900">
                    {{ $todayAttendance?->punch_in_at ? \Carbon\Carbon::parse($todayAttendance->punch_in_at)->format('h:i A') : '--:--' }}
                </div>
                <div class="text-[11px] text-emerald-700/70 mt-1">
                    @if($todayAttendance?->punch_in_at)
                        @if($todayAttendance->status === 'late')
                            <span class="text-amber-700 font-bold">⚠️ Late Arrival (After 10:30 AM)</span>
                        @else
                            <span class="text-emerald-700 font-bold">✓ On Time Punch</span>
                        @endif
                    @else
                        <span>Counter QR scan required</span>
                    @endif
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-gradient-to-br from-indigo-50 to-white border border-indigo-200/60 shadow-xs">
                <div class="flex items-center justify-between text-xs text-indigo-800/70 mb-1">
                    <span class="font-bold uppercase tracking-wider text-[10px]">Punch Out Time</span>
                    <span>🏁</span>
                </div>
                <div class="text-xl sm:text-2xl font-mono font-bold text-indigo-900">
                    {{ $todayAttendance?->punch_out_at ? \Carbon\Carbon::parse($todayAttendance->punch_out_at)->format('h:i A') : '--:--' }}
                </div>
                <div class="text-[11px] text-indigo-700/70 mt-1">
                    @if($todayAttendance?->punch_out_at)
                        <span class="text-indigo-700 font-bold">✓ Shift Closed Successfully</span>
                    @elseif($todayAttendance?->punch_in_at)
                        <span class="text-indigo-600">Scan again before leaving</span>
                    @else
                        <span>Awaiting punch in</span>
                    @endif
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-gradient-to-br from-purple-50 to-white border border-purple-200/60 shadow-xs">
                <div class="flex items-center justify-between text-xs text-purple-800/70 mb-1">
                    <span class="font-bold uppercase tracking-wider text-[10px]">Total Shift Hours</span>
                    <span>⏳</span>
                </div>
                <div class="text-xl sm:text-2xl font-mono font-bold text-purple-900">
                    {{ $todayAttendance?->total_hours ? $todayAttendance->total_hours . ' hrs' : ($todayAttendance?->punch_in_at ? 'In Progress' : '0.0 hrs') }}
                </div>
                <div class="text-[11px] text-purple-700/70 mt-1">
                    <span>This Month: <strong>{{ $monthPresent }} days</strong> ({{ number_format($monthHours, 1) }} hrs)</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Interactive Live Camera QR Scanner Modal / Widget --}}
    <div 
        x-show="scannerActive" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="glass-card rounded-3xl p-6 sm:p-8 border-2 border-primary/40 bg-white/95 shadow-xl text-center space-y-4"
    >
        <div class="max-w-md mx-auto">
            <div class="flex items-center justify-between pb-3 border-b border-plum/10 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                    <h3 class="font-serif font-bold text-plum text-sm sm:text-base">
                        Point Camera at Admin Counter Screen
                    </h3>
                </div>
                <button type="button" @click="stopScanner()" class="p-1 rounded-lg text-plum/50 hover:text-plum text-xs">
                    ✕ Close
                </button>
            </div>

            {{-- Camera Feed Container --}}
            <div class="relative w-full aspect-square max-w-xs mx-auto rounded-2xl overflow-hidden bg-black/90 border-2 border-primary/40 shadow-inner flex items-center justify-center">
                <div id="qr-reader" class="w-full h-full"></div>

                {{-- Animated Target Frame --}}
                <div class="absolute inset-8 border-2 border-dashed border-white/60 rounded-xl pointer-events-none flex items-center justify-center">
                    <div class="w-full h-0.5 bg-gradient-to-r from-transparent via-rose-500 to-transparent animate-pulse"></div>
                </div>

                <div x-show="scanning" class="absolute bottom-3 left-0 right-0 text-center">
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-black/75 text-white backdrop-blur-md">
                        Scanning live 20s counter QR...
                    </span>
                </div>
            </div>

            <p class="text-[11px] text-plum/70 mt-3 leading-relaxed">
                Showroom reception/counter par chal rahe tablet ya monitor screen ke 20s QR code par camera point karein. Jaise hi QR detect hoga, aapki haaziri turant punch ho jayegi.
            </p>

            {{-- Manual Token Fallback --}}
            <div class="mt-4 pt-4 border-t border-plum/10 text-xs">
                <span class="text-plum/50 block text-[10px] mb-2">Camera permission disabled? Paste scan URL / Token:</span>
                <form @submit.prevent="submitManualToken()" class="flex gap-2">
                    <input 
                        type="text" 
                        x-model="manualToken" 
                        placeholder="Paste scan URL or token..." 
                        class="flex-1 rounded-xl border border-plum/20 bg-white p-2 text-xs text-plum"
                    >
                    <button type="submit" class="px-4 py-2 rounded-xl font-bold bg-primary text-white text-xs hover:bg-primary-700">
                        Punch
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Instructions Card --}}
    <div class="glass-card rounded-2xl p-5 border border-white/80 bg-gradient-to-r from-amber-50/60 to-white shadow-xs flex items-start gap-3 text-xs text-plum">
        <span class="text-2xl leading-none">💡</span>
        <div class="space-y-1">
            <h4 class="font-bold text-plum text-xs">How Bombay Fashion QR Attendance Works:</h4>
            <ul class="list-disc pl-4 space-y-0.5 text-plum/70 text-[11px]">
                <li><strong>Admin Kiosk Screen:</strong> Store counter par reception display screen par har 20 second me naya dynamic encrypted QR code generate hota hai.</li>
                <li><strong>Punch In:</strong> Showroom aate hi apne camera se counter QR scan karein. 10:30 AM se pehle scan karne par on-time haaziri lagegi.</li>
                <li><strong>Punch Out:</strong> Shaam ko showroom band hone ke time wapas wahi counter QR scan karein to total duty hours calculate ho jayenge.</li>
                <li><strong>Anti-Proxy:</strong> Ek phone se sirf usi staff ki haaziri lagti hai. Kisi doosre ke phone se proxy punch nahi ho sakti.</li>
            </ul>
        </div>
    </div>

    {{-- Recent 30 Days Shift & Attendance Ledger --}}
    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/90 shadow-sm">
        <div class="flex items-center justify-between pb-3 border-b border-plum/10 mb-4">
            <h2 class="text-base font-serif font-bold text-plum">
                My Attendance History (Past 30 Days)
            </h2>
            <span class="text-xs text-plum/60 font-medium">
                {{ $monthPresent }} Present Days This Month
            </span>
        </div>

        @if($recentAttendances->isEmpty())
            <div class="py-10 text-center text-xs text-plum/50 italic">
                Aapka koi purana attendance record nahi mila. Counter screen scan karke apni pehli haaziri darj karein.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px] bg-plum/5">
                            <th class="py-2.5 px-3">Date</th>
                            <th class="py-2.5 px-3">In Time</th>
                            <th class="py-2.5 px-3">Out Time</th>
                            <th class="py-2.5 px-3 text-center">Duration</th>
                            <th class="py-2.5 px-3">Verification</th>
                            <th class="py-2.5 px-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($recentAttendances as $att)
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3 px-3 font-medium text-plum">
                                    {{ \Carbon\Carbon::parse($att->date)->format('d M Y (D)') }}
                                    @if($att->date === date('Y-m-d'))
                                        <span class="inline-block ml-1 text-[9px] font-bold bg-primary-100 text-primary-800 px-1.5 py-0.2 rounded">Today</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 font-mono">
                                    {{ $att->punch_in_at ? \Carbon\Carbon::parse($att->punch_in_at)->format('h:i A') : '--' }}
                                </td>
                                <td class="py-3 px-3 font-mono">
                                    {{ $att->punch_out_at ? \Carbon\Carbon::parse($att->punch_out_at)->format('h:i A') : '--' }}
                                </td>
                                <td class="py-3 px-3 text-center font-mono font-bold text-plum">
                                    {{ $att->total_hours ? $att->total_hours . ' hrs' : '--' }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-plum/5 text-plum">
                                        {{ $att->verification_method === 'dynamic_qr' ? '📷 Counter QR' : '✍️ Manual Admin' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $att->status === 'present' ? 'bg-emerald-100 text-emerald-800' : ($att->status === 'late' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700') }}">
                                        {{ ucfirst($att->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

{{-- HTML5 QR Code Library for reliable Camera Scanning --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
function attendanceScanner() {
    return {
        scannerActive: false,
        scanning: false,
        html5QrCode: null,
        manualToken: '',

        toggleScanner() {
            if (this.scannerActive) {
                this.stopScanner();
            } else {
                this.startScanner();
            }
        },

        startScanner() {
            this.scannerActive = true;
            this.scanning = true;

            this.$nextTick(() => {
                if (typeof Html5Qrcode === 'undefined') {
                    alert('Camera scanner library loading. Please wait a moment.');
                    return;
                }

                if (!this.html5QrCode) {
                    this.html5QrCode = new Html5Qrcode("qr-reader");
                }

                const config = { fps: 10, qrbox: { width: 220, height: 220 } };

                this.html5QrCode.start(
                    { facingMode: "environment" },
                    config,
                    (decodedText, decodedResult) => {
                        this.handleScanSuccess(decodedText);
                    },
                    (errorMessage) => {
                        // ignore parsing noise
                    }
                ).catch(err => {
                    console.error("Camera access error:", err);
                    // Fallback to front camera if environment camera fails
                    this.html5QrCode.start(
                        { facingMode: "user" },
                        config,
                        (decodedText) => this.handleScanSuccess(decodedText),
                        (error) => {}
                    ).catch(e2 => {
                        alert("Camera access was blocked or is not available on this device. Please paste the scan URL manually.");
                        this.scanning = false;
                    });
                });
            });
        },

        stopScanner() {
            if (this.html5QrCode && this.html5QrCode.isScanning) {
                this.html5QrCode.stop().then(() => {
                    this.scannerActive = false;
                    this.scanning = false;
                }).catch(err => {
                    this.scannerActive = false;
                    this.scanning = false;
                });
            } else {
                this.scannerActive = false;
                this.scanning = false;
            }
        },

        handleScanSuccess(decodedText) {
            // Vibrate phone if supported
            if (navigator.vibrate) {
                navigator.vibrate(200);
            }

            this.stopScanner();

            // If scanned URL contains attendance token or URL
            if (decodedText.startsWith('http://') || decodedText.startsWith('https://')) {
                window.location.href = decodedText;
            } else {
                window.location.href = "{{ route('attendance.scan') }}?token=" + encodeURIComponent(decodedText);
            }
        },

        submitManualToken() {
            if (!this.manualToken.trim()) return;

            let val = this.manualToken.trim();
            if (val.startsWith('http://') || val.startsWith('https://')) {
                window.location.href = val;
            } else {
                window.location.href = "{{ route('attendance.scan') }}?token=" + encodeURIComponent(val);
            }
        }
    };
}
</script>
@endsection
