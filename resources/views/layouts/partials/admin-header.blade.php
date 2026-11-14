@php
    $adminUser = Auth::user();
@endphp

<header class="h-20 bg-white/80 backdrop-blur-xl border-b border-primary-100/70 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30">
    <div class="flex items-center space-x-3">
        <!-- Universal Sidebar Toggle Button (Visible on Desktop & Mobile) -->
        <button 
            @click="toggleSidebar()" 
            type="button" 
            id="admin-sidebar-toggle-btn"
            class="p-2 sm:px-3 sm:py-2 rounded-xl text-plum/80 hover:text-primary hover:bg-primary-50 transition border border-primary-200/80 bg-white/95 shadow-xs cursor-pointer flex items-center gap-2 group"
            title="Toggle Navigation Sidebar"
            aria-label="Toggle Navigation Sidebar"
        >
            <svg class="w-5 h-5 text-primary group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
            </svg>
            <span class="text-xs font-bold text-plum/90 hidden sm:inline" x-text="sidebarOpen ? 'Hide Menu' : 'Show Menu'">Toggle Menu</span>
        </button>

        <h2 class="font-serif font-bold text-base sm:text-xl text-plum truncate">
            @yield('page_title', 'Management Dashboard')
        </h2>
    </div>

    <!-- Right Controls -->
    <div class="flex items-center space-x-2 sm:space-x-3">
        <!-- Quick Attendance Punch Icon -->
        <a 
            href="{{ route('admin.attendance.mark') }}" 
            class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 transition border border-emerald-200 shadow-xs"
            title="Scan Admin Counter QR to mark Attendance"
        >
            <svg class="w-4 h-4 text-emerald-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            <span class="hidden sm:inline">Punch QR</span>
        </a>

        <!-- Storefront Quick Link -->
        <a href="{{ route('home') }}" target="_blank" class="hidden md:inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-primary-700 bg-primary-50/80 hover:bg-primary-100 transition border border-primary-100">
            <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            <span>Live Store</span>
        </a>

        <!-- User Profile Dropdown Menu (Alpine.js) -->
        <div class="relative pl-2 sm:pl-3 border-l border-primary-100/60" x-data="{ userMenuOpen: false }">
            <button 
                @click="userMenuOpen = !userMenuOpen" 
                @click.away="userMenuOpen = false"
                type="button"
                class="flex items-center space-x-2.5 p-1.5 rounded-2xl hover:bg-primary-50/80 transition cursor-pointer"
            >
                @if($adminUser->avatar_url)
                    <img 
                        src="{{ $adminUser->avatar_url }}" 
                        alt="{{ $adminUser->name }}" 
                        class="w-9 h-9 rounded-2xl object-cover border border-primary-200 shadow-xs"
                    >
                @else
                    <div class="w-9 h-9 rounded-2xl bg-gradient-to-tr from-primary-600 to-rose-400 text-white font-bold text-xs flex items-center justify-center shadow-xs">
                        {{ strtoupper(substr($adminUser->name, 0, 1)) }}
                    </div>
                @endif
                <div class="hidden lg:block text-left pr-1">
                    <p class="text-xs font-bold text-plum leading-tight">{{ Str::limit($adminUser->name, 18) }}</p>
                    <p class="text-[10px] text-plum/60">{{ $adminUser->role?->display_name ?? 'Staff' }}</p>
                </div>
                <svg class="w-4 h-4 text-plum/50 hidden sm:block transition-transform" :class="userMenuOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <!-- Dropdown Flyout -->
            <div 
                x-show="userMenuOpen"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-primary-100 py-2 z-50 text-xs"
                style="display: none;"
            >
                <div class="px-4 py-3 border-b border-plum/10">
                    <p class="font-serif font-bold text-plum text-sm leading-tight">{{ $adminUser->name }}</p>
                    <p class="text-[11px] text-plum/60 truncate">{{ $adminUser->email }}</p>
                    <div class="mt-1.5 flex items-center gap-1.5">
                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ $adminUser->isAdmin() ? 'bg-amber-100 text-amber-900' : 'bg-emerald-100 text-emerald-800' }}">
                            {{ $adminUser->role?->display_name ?? 'Staff' }}
                        </span>
                        @if($adminUser->employee_id)
                            <span class="text-[10px] text-plum/50 font-mono">{{ $adminUser->employee_id }}</span>
                        @endif
                    </div>
                </div>

                <div class="py-1">
                    <a 
                        href="{{ route('admin.profile.show') }}" 
                        class="flex items-center space-x-3 px-4 py-2.5 text-plum hover:bg-primary-50 hover:text-primary transition font-semibold"
                    >
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>My Profile &amp; Shift Info</span>
                    </a>

                    <a 
                        href="{{ route('admin.attendance.mark') }}" 
                        class="flex items-center space-x-3 px-4 py-2.5 text-plum hover:bg-primary-50 hover:text-primary transition font-semibold"
                    >
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        <span>Mark Attendance (QR)</span>
                    </a>

                    <a 
                        href="{{ route('admin.profile.salary') }}" 
                        class="flex items-center space-x-3 px-4 py-2.5 text-plum hover:bg-primary-50 hover:text-primary transition font-semibold"
                    >
                        <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>My Salary &amp; Payslips</span>
                    </a>

                    <a 
                        href="{{ route('admin.profile.purchases') }}" 
                        class="flex items-center space-x-3 px-4 py-2.5 text-plum hover:bg-primary-50 hover:text-primary transition font-semibold"
                    >
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span>My Purchased Clothes</span>
                    </a>
                </div>

                <div class="pt-1 border-t border-plum/10">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center space-x-3 px-4 py-2.5 text-rose-600 hover:bg-rose-50 transition font-semibold cursor-pointer">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            <span>Sign Out (Logout)</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
