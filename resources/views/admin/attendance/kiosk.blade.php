<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Counter Attendance QR - Bombay Fashion Amritsar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body 
    class="bg-gradient-to-br from-festive-ivory via-festive-blush/30 to-festive-ivory min-h-screen text-festive-plum font-sans flex flex-col justify-between overflow-x-hidden select-none"
    x-data="attendanceKiosk()"
    x-init="init()"
>

    {{-- Top Showroom Navigation Header --}}
    <header class="h-20 px-6 sm:px-12 flex items-center justify-between border-b border-festive-plum/10 bg-white/80 backdrop-blur-md">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-primary-600 to-festive-rose flex items-center justify-center text-white shadow-md shadow-festive-rose/25">
                <span class="font-serif font-bold text-xl">BF</span>
            </div>
            <div>
                <h1 class="font-serif font-bold text-lg text-festive-plum leading-tight">Bombay Fashion Amritsar</h1>
                <p class="text-[11px] text-festive-plum/60 font-medium">Mahila College Road Showroom • Live Counter Attendance Kiosk</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- QR Code Size Selector (Bada ya Chota karne ke liye) --}}
            <div class="inline-flex rounded-xl p-1 bg-festive-plum/5 border border-festive-plum/10 text-xs">
                <button 
                    type="button" 
                    @click="setQrSize('normal')"
                    class="px-2.5 py-1 rounded-lg font-semibold transition-all cursor-pointer text-[11px]"
                    :class="qrSize === 'normal' ? 'bg-white text-festive-rose shadow-xs' : 'text-festive-plum/60 hover:text-festive-plum'"
                    title="Normal QR (280px)"
                >
                    Normal
                </button>
                <button 
                    type="button" 
                    @click="setQrSize('large')"
                    class="px-2.5 py-1 rounded-lg font-semibold transition-all cursor-pointer text-[11px]"
                    :class="qrSize === 'large' ? 'bg-white text-festive-rose shadow-xs' : 'text-festive-plum/60 hover:text-festive-plum'"
                    title="Large QR (380px)"
                >
                    Large
                </button>
                <button 
                    type="button" 
                    @click="setQrSize('giant')"
                    class="px-2.5 py-1 rounded-lg font-bold transition-all cursor-pointer text-[11px] flex items-center gap-1"
                    :class="qrSize === 'giant' ? 'bg-festive-rose text-white shadow-xs' : 'text-festive-plum/60 hover:text-festive-plum'"
                    title="Giant QR (480px) for Far Distance Scanning"
                >
                    <span>⚡ Giant</span>
                </button>
            </div>

            {{-- Toggle Show/Hide Punches Feed --}}
            <button 
                type="button" 
                @click="showPunchesFeed = !showPunchesFeed"
                class="px-3 py-1.5 rounded-xl border text-xs font-semibold flex items-center gap-1.5 transition-all shadow-xs cursor-pointer"
                :class="showPunchesFeed ? 'bg-white border-festive-plum/20 text-festive-plum hover:bg-festive-blush' : 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700'"
                title="Hide or Show Today's Punches Feed"
            >
                <span x-text="showPunchesFeed ? '👁️ Hide Punches' : '📋 Show Punches'"></span>
            </button>

            {{-- Digital Live Clock --}}
            <div class="text-right hidden sm:block">
                <div class="font-mono font-bold text-lg text-festive-plum" x-text="clockTime"></div>
                <div class="text-[11px] text-festive-plum/60" x-text="clockDate"></div>
            </div>

            {{-- Fullscreen Toggle --}}
            <button 
                type="button" 
                @click="toggleFullscreen()"
                class="p-2.5 rounded-xl border border-festive-plum/20 bg-white hover:bg-festive-blush text-festive-plum transition-all shadow-xs cursor-pointer"
                title="Toggle Fullscreen"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
            </button>
        </div>
    </header>

    {{-- Main Kiosk Stage --}}
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-8 flex flex-col lg:flex-row items-center justify-center gap-8 transition-all duration-300">
        
        {{-- QR Code Center Box --}}
        <div 
            class="glass-card rounded-3xl p-6 sm:p-8 border border-white/90 bg-white/95 shadow-2xl flex flex-col items-center text-center relative transition-all duration-300 w-full"
            :class="{
                'max-w-md': showPunchesFeed && qrSize === 'normal',
                'max-w-xl': showPunchesFeed && qrSize !== 'normal',
                'max-w-2xl py-10': !showPunchesFeed && qrSize === 'giant',
                'max-w-lg py-8': !showPunchesFeed && qrSize !== 'giant'
            }"
        >
            
            {{-- Ambient Glow --}}
            <div class="absolute -top-12 -left-12 w-48 h-48 rounded-full bg-festive-rose/15 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-12 -right-12 w-48 h-48 rounded-full bg-festive-gold/15 blur-3xl pointer-events-none"></div>

            {{-- Top Badge with Live Pulsing Beacon --}}
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold bg-festive-blush text-festive-rose border border-festive-rose/20 mb-3 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-festive-rose animate-ping"></span>
                <span>20-Second Dynamic Rolling QR</span>
            </div>

            <h2 class="text-xl sm:text-2xl font-serif font-bold text-festive-plum mb-1">
                Scan to Mark Attendance
            </h2>
            <p class="text-xs text-festive-plum/65 mb-4 max-w-xs leading-relaxed">
                Point your mobile phone camera at this live QR code to record your showroom punch.
            </p>

            {{-- QR Code Display with Dynamic Scaling --}}
            <div 
                class="relative rounded-3xl bg-white border-2 border-festive-plum/10 shadow-lg mb-3 group transition-all duration-300 flex items-center justify-center"
                :class="{
                    'p-4': qrSize === 'normal',
                    'p-6': qrSize === 'large',
                    'p-8 shadow-2xl ring-8 ring-festive-rose/10': qrSize === 'giant'
                }"
            >
                <img 
                    :src="qrImageUrl" 
                    alt="Attendance QR" 
                    class="object-contain rounded-2xl transition-all duration-300 mx-auto"
                    :class="{
                        'w-64 h-64 sm:w-72 sm:h-72': qrSize === 'normal',
                        'w-80 h-80 sm:w-96 sm:h-96': qrSize === 'large',
                        'w-[340px] h-[340px] sm:w-[480px] sm:h-[480px]': qrSize === 'giant',
                        'opacity-50 scale-95': shimmer,
                        'opacity-100 scale-100': !shimmer
                    }"
                >

                {{-- Corner decorative boutique markers --}}
                <div class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-festive-rose rounded-tl"></div>
                <div class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-festive-rose rounded-tr"></div>
                <div class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-festive-rose rounded-bl"></div>
                <div class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-festive-rose rounded-br"></div>
            </div>

            {{-- Quick Size Switcher Link --}}
            <button 
                type="button" 
                @click="toggleQrSize()"
                class="text-[11px] font-semibold text-festive-rose hover:text-primary-700 flex items-center gap-1.5 cursor-pointer mb-4 transition-colors"
            >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                <span x-text="qrSize === 'giant' ? 'Switch to Normal Size' : 'Enlarge QR Code (Bada Karein)'"></span>
            </button>

            {{-- Dynamic Progress Timer Pill --}}
            <div class="w-full bg-festive-plum/5 rounded-2xl p-3 border border-festive-plum/10 flex items-center justify-between text-xs mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full border-2 border-festive-rose border-t-transparent animate-spin"></span>
                    <span class="font-medium text-festive-plum/70">Next QR Rotation:</span>
                </div>
                <span class="font-mono font-bold text-sm text-festive-rose">
                    <span x-text="countdown"></span>s remaining
                </span>
            </div>

            {{-- Security Metadata (Admin email, year, date, time) --}}
            <div class="text-[10px] text-festive-plum/50 space-y-0.5 font-mono">
                <div>Admin: <span class="font-bold text-festive-plum/70">{{ $tokenData['email'] ?? 'admin@bombayfashion.com' }}</span></div>
                <div>Encrypted: <span class="text-festive-rose font-bold">HMAC-SHA256</span> • Window: #<span x-text="windowIndex"></span></div>
                <div>Anti-Spoofing: Rotates every 20s • 1 Mobile per Staff</div>
            </div>
        </div>

        {{-- Right Column: Live Check-in Activity Feed (Can be hidden by admin) --}}
        <div 
            x-show="showPunchesFeed" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-x-4"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-4"
            class="max-w-md w-full space-y-4"
        >
            <div class="glass-card rounded-3xl p-6 border border-white/90 bg-white/90 shadow-xl relative">
                <div class="flex items-center justify-between pb-3 border-b border-festive-plum/10 mb-4">
                    <h3 class="font-serif font-bold text-festive-plum text-sm flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">✓</span>
                        <span>Today's Live Showroom Punches</span>
                    </h3>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200" x-text="recentCheckins.length + ' Recorded'"></span>
                        {{-- Close Button on Feed --}}
                        <button 
                            @click="showPunchesFeed = false"
                            type="button"
                            class="p-1 rounded-lg text-festive-plum/40 hover:text-festive-rose hover:bg-festive-rose/10 transition cursor-pointer"
                            title="Hide Punches Feed"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                {{-- Activity List --}}
                <div class="space-y-3 max-h-[360px] overflow-y-auto pr-1">
                    <template x-for="(item, idx) in recentCheckins" :key="idx">
                        <div class="p-3 rounded-2xl bg-white border border-festive-plum/10 flex items-center justify-between shadow-xs hover:border-festive-rose/30 transition-all">
                            <div class="flex items-center gap-2.5">
                                <img :src="item.avatar" class="w-9 h-9 rounded-xl object-cover border border-festive-plum/10 shrink-0" alt="Avatar">
                                <div>
                                    <span class="font-bold text-festive-plum text-xs block leading-tight" x-text="item.name"></span>
                                    <span class="text-[10px] text-festive-plum/50" x-text="item.designation"></span>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" :class="item.status === 'Late' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'" x-text="item.status"></span>
                                <span class="text-[11px] font-mono font-bold text-festive-plum block mt-0.5" x-text="item.punch_out ? item.punch_out + ' (Out)' : item.punch_in + ' (In)'"></span>
                            </div>
                        </div>
                    </template>

                    <template x-if="recentCheckins.length === 0">
                        <div class="text-center py-8 text-festive-plum/50 text-xs">
                            No attendance punches recorded yet today.<br>Staff scans will appear here live!
                        </div>
                    </template>
                </div>
            </div>

            {{-- Showroom Policy Card --}}
            <div class="p-4 rounded-2xl bg-gradient-to-r from-festive-blush/60 to-amber-50/50 border border-festive-plum/10 text-xs flex items-center gap-3">
                <span class="text-2xl">⏰</span>
                <div class="text-[11px] text-festive-plum/70 leading-relaxed">
                    <span class="font-bold text-festive-plum block">Showroom Timings & Grace:</span>
                    Reporting time is 10:00 AM. Scans between 10:00 AM - 10:30 AM marked On Time. Scans after 10:30 AM automatically flagged as Late Arrival.
                </div>
            </div>
        </div>

    </main>

    {{-- Kiosk Footer --}}
    <footer class="h-12 px-6 flex items-center justify-between text-[11px] text-festive-plum/50 border-t border-festive-plum/10 bg-white/50 backdrop-blur-xs">
        <span>Bombay Fashion • Mahila College Road, Amritsar (Closes 9:30 PM)</span>
        <span>Secure Showroom Kiosk Mode • Connected to Store Server</span>
    </footer>

    {{-- Alpine Logic for 20-Second Dynamic Rolling QR --}}
    <script>
        function attendanceKiosk() {
            return {
                qrImageUrl: '{{ $tokenData['qr_image_url'] }}',
                countdown: {{ $tokenData['seconds_remaining'] }},
                windowIndex: {{ $tokenData['window'] }},
                shimmer: false,
                clockTime: '',
                clockDate: '',
                recentCheckins: @json($recentCheckinsPayload ?? []),
                showPunchesFeed: true,
                qrSize: 'normal',

                setQrSize(size) {
                    this.qrSize = size;
                    if (size === 'giant') {
                        this.showPunchesFeed = false;
                    }
                },

                toggleQrSize() {
                    if (this.qrSize === 'normal') {
                        this.setQrSize('large');
                    } else if (this.qrSize === 'large') {
                        this.setQrSize('giant');
                    } else {
                        this.setQrSize('normal');
                        this.showPunchesFeed = true;
                    }
                },

                init() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);

                    // 1-second countdown loop
                    setInterval(() => {
                        if (this.countdown > 1) {
                            this.countdown--;
                        } else {
                            this.fetchFreshToken();
                        }
                    }, 1000);
                },

                updateClock() {
                    const now = new Date();
                    this.clockTime = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
                    this.clockDate = now.toLocaleDateString('en-US', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
                },

                async fetchFreshToken() {
                    try {
                        this.shimmer = true;
                        const res = await fetch('{{ route('admin.attendance.live-token') }}');
                        const data = await res.json();
                        if (data.success) {
                            this.qrImageUrl = data.qr_image_url;
                            this.countdown = data.seconds_remaining;
                            this.windowIndex = data.window;
                            if (data.recent_checkins) {
                                this.recentCheckins = data.recent_checkins;
                            }
                        }
                    } catch (err) {
                        console.error('Error fetching live token:', err);
                        this.countdown = 20;
                    } finally {
                        setTimeout(() => this.shimmer = false, 300);
                    }
                },

                toggleFullscreen() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(err => alert(err.message));
                    } else {
                        document.exitFullscreen();
                    }
                }
            }
        }
    </script>
</body>
</html>
