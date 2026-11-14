@extends('layouts.admin')

@section('title', 'My Profile & Staff Workspace - Bombay Fashion Admin')

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

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
            <div class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Kripya niche di gayi galtiyan theek karein:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-[11px] pl-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header Banner & Quick Navigation --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-plum via-[#3b2a3e] to-primary-900 text-white p-6 sm:p-8 shadow-xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4 sm:gap-5">
                <div class="relative shrink-0">
                    <img 
                        src="{{ $user->avatar_url }}" 
                        alt="{{ $user->name }}" 
                        class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border-2 border-white/20 shadow-md bg-white/10"
                    >
                    <span class="absolute -bottom-1 -right-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $user->isAdmin() ? 'bg-amber-400 text-plum' : 'bg-emerald-400 text-plum' }}">
                        {{ $user->isAdmin() ? 'Admin' : 'Staff' }}
                    </span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white/15 text-white/90">
                            ID: {{ $user->employee_id ?: 'BF-EMP-' . str_pad($user->id, 3, '0', STR_PAD_LEFT) }}
                        </span>
                        <span class="text-xs text-white/70">{{ $user->department ?: 'Showroom Floor Operations' }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-serif font-black tracking-tight text-white mt-1">
                        {{ $user->name }}
                    </h1>
                    <p class="text-xs text-white/75 flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">
                        <span>👔 {{ $user->designation ?: ($user->isAdmin() ? 'Store Owner / General Manager' : 'Showroom Associate') }}</span>
                        <span>•</span>
                        <span>📧 {{ $user->email }}</span>
                        @if($user->phone)
                            <span>•</span>
                            <span>📱 {{ $user->phone }}</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Quick Tabs / Action Shortcuts --}}
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                <a 
                    href="{{ route('admin.attendance.mark') }}" 
                    class="px-4 py-2.5 rounded-xl bg-primary text-white hover:bg-primary-700 text-xs font-bold shadow-md transition flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>Mark Attendance (QR)</span>
                </a>
                <a 
                    href="{{ route('admin.profile.salary') }}" 
                    class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/20 shadow-xs transition flex items-center gap-2"
                >
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>My Salary &amp; Slips</span>
                </a>
                <a 
                    href="{{ route('admin.profile.purchases') }}" 
                    class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/20 shadow-xs transition flex items-center gap-2"
                >
                    <svg class="w-4 h-4 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span>My Purchases ({{ $ordersCount }})</span>
                </a>
            </div>
        </div>

        {{-- Subtle Luxury Motif Background SVG --}}
        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
            <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L1 21h22L12 2zm0 3.8L19.5 19h-15L12 5.8z"/>
            </svg>
        </div>
    </div>

    {{-- 3 Summary Quick Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        {{-- Card 1: Today's Shift & Attendance --}}
        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/90 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-plum/50">Today's Attendance</span>
                <span class="w-2.5 h-2.5 rounded-full {{ $todayAttendance ? 'bg-emerald-500 animate-pulse' : 'bg-amber-400' }}"></span>
            </div>
            <div>
                <p class="text-lg font-serif font-bold text-plum">
                    @if($todayAttendance)
                        {{ ucfirst($todayAttendance->status) }}
                    @else
                        Not Punched In
                    @endif
                </p>
                <p class="text-xs text-plum/60 mt-0.5">
                    @if($todayAttendance)
                        In: {{ $todayAttendance->check_in ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') : '--' }}
                        @if($todayAttendance->check_out)
                            &bull; Out: {{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') }}
                        @endif
                    @else
                        Scan Showroom QR to punch in
                    @endif
                </p>
            </div>
            <a href="{{ route('admin.attendance.mark') }}" class="mt-3 text-xs font-bold text-primary hover:underline flex items-center gap-1">
                <span>Open Scanner &amp; History</span>
                <span>&rarr;</span>
            </a>
        </div>

        {{-- Card 2: Current Salary & Latest Slip --}}
        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/90 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-plum/50">Monthly Base Salary</span>
                <span class="text-xs text-emerald-600 font-bold">₹ Slips</span>
            </div>
            <div>
                <p class="text-lg font-serif font-bold text-plum">
                    ₹{{ number_format((float)($user->salary ?? 0), 2) }}
                </p>
                <p class="text-xs text-plum/60 mt-0.5">
                    @if($latestSalary)
                        Latest: {{ $latestSalary->month_label }} ({{ ucfirst($latestSalary->payment_status) }})
                    @else
                        Payroll record updated monthly
                    @endif
                </p>
            </div>
            <a href="{{ route('admin.profile.salary') }}" class="mt-3 text-xs font-bold text-emerald-600 hover:underline flex items-center gap-1">
                <span>View Payslips Register</span>
                <span>&rarr;</span>
            </a>
        </div>

        {{-- Card 3: Personal Orders / Purchases --}}
        <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/90 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-plum/50">My Purchases</span>
                <span class="text-xs text-primary font-bold">Showroom &amp; Web</span>
            </div>
            <div>
                <p class="text-lg font-serif font-bold text-plum">
                    {{ $ordersCount }} Garment {{ Str::plural('Order', $ordersCount) }}
                </p>
                <p class="text-xs text-plum/60 mt-0.5">
                    Clothes &amp; items bought by you
                </p>
            </div>
            <a href="{{ route('admin.profile.purchases') }}" class="mt-3 text-xs font-bold text-primary hover:underline flex items-center gap-1">
                <span>View My Purchased Clothes</span>
                <span>&rarr;</span>
            </a>
        </div>

    </div>

    {{-- Main 2-Column Details & Settings --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {{-- Left 7/12: Personal Employment, Shift & Bank Info --}}
        <div class="lg:col-span-7 space-y-6">
            
            {{-- Employment & Shift Details --}}
            <div class="glass-card rounded-3xl p-6 sm:p-7 border border-white/80 bg-white/95 shadow-sm space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-plum/10">
                    <h2 class="font-serif font-bold text-plum text-base flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Staff Employment &amp; Shift Schedule</span>
                    </h2>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-plum/5 text-plum/70">
                        Active Staff
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3.5 rounded-2xl bg-plum/5 border border-plum/5">
                        <span class="text-[10px] uppercase font-bold text-plum/50 block">Employee Code</span>
                        <span class="font-mono font-bold text-plum text-sm">
                            {{ $user->employee_id ?: 'BF-EMP-' . str_pad($user->id, 3, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-plum/5 border border-plum/5">
                        <span class="text-[10px] uppercase font-bold text-plum/50 block">Official Designation</span>
                        <span class="font-bold text-plum text-sm">
                            {{ $user->designation ?: ($user->isAdmin() ? 'General Store Administrator' : 'Showroom Associate') }}
                        </span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-plum/5 border border-plum/5">
                        <span class="text-[10px] uppercase font-bold text-plum/50 block">Department</span>
                        <span class="font-bold text-plum text-sm">
                            {{ $user->department ?: 'Retail Garments & Sales' }}
                        </span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-plum/5 border border-plum/5">
                        <span class="text-[10px] uppercase font-bold text-plum/50 block">Date of Joining</span>
                        <span class="font-bold text-plum text-sm">
                            {{ $user->date_of_joining ? $user->date_of_joining->format('d M Y') : 'Active Member' }}
                        </span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-plum/5 border border-plum/5">
                        <span class="text-[10px] uppercase font-bold text-plum/50 block">Daily Shift Timings</span>
                        <span class="font-bold text-plum text-sm">
                            🕒 {{ $user->shift_timings ?: '10:00 AM - 08:30 PM' }}
                        </span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-plum/5 border border-plum/5">
                        <span class="text-[10px] uppercase font-bold text-plum/50 block">Weekly Off Day</span>
                        <span class="font-bold text-primary text-sm">
                            🌟 {{ $user->weekly_off ?: 'Sunday' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Bank & Payout Information --}}
            <div class="glass-card rounded-3xl p-6 sm:p-7 border border-white/80 bg-white/95 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-plum/10">
                    <h2 class="font-serif font-bold text-plum text-base flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>Bank &amp; Salary Payout Details</span>
                    </h2>
                    <span class="text-[10px] text-plum/50">For Monthly Direct Transfer</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3.5 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                        <span class="text-[10px] uppercase font-bold text-emerald-800/60 block">Bank Name</span>
                        <span class="font-bold text-plum text-sm">{{ $user->bank_name ?: 'State Bank of India (Amritsar)' }}</span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                        <span class="text-[10px] uppercase font-bold text-emerald-800/60 block">Account Number</span>
                        <span class="font-mono font-bold text-plum text-sm">
                            {{ $user->bank_account_no ? str_repeat('*', max(0, strlen($user->bank_account_no) - 4)) . substr($user->bank_account_no, -4) : 'Provided to Admin' }}
                        </span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                        <span class="text-[10px] uppercase font-bold text-emerald-800/60 block">IFSC Code</span>
                        <span class="font-mono font-bold text-plum text-sm">{{ $user->bank_ifsc ?: 'SBIN0001234' }}</span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-emerald-50/50 border border-emerald-100">
                        <span class="text-[10px] uppercase font-bold text-emerald-800/60 block">UPI ID for Salary</span>
                        <span class="font-mono font-bold text-plum text-sm">{{ $user->upi_id ?: $user->phone . '@upi' }}</span>
                    </div>
                </div>
            </div>

            {{-- Operational Permissions Granted by Admin --}}
            <div class="glass-card rounded-3xl p-6 sm:p-7 border border-white/80 bg-white/95 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-plum/10">
                    <h2 class="font-serif font-bold text-plum text-base flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Assigned System Permissions</span>
                    </h2>
                    <span class="text-[10px] text-plum/50">Configured by Admin</span>
                </div>

                @if($user->isAdmin())
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 font-semibold flex items-center gap-3">
                        <span class="text-xl">👑</span>
                        <div>
                            <span class="font-bold block">Super Administrator Access</span>
                            <span class="text-[11px] text-amber-800/80">Aapke paas Bombay Fashion showroom, online store, reports, aur configuration ke sabhi rights hain.</span>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        @php
                            $perms = \App\Models\User::availablePermissions();
                            $myPerms = $user->permissions ?? [];
                        @endphp
                        @foreach($perms as $key => $pInfo)
                            @php $hasThis = in_array($key, $myPerms); @endphp
                            <div class="p-3 rounded-2xl border transition {{ $hasThis ? 'bg-emerald-50/70 border-emerald-200 text-emerald-900' : 'bg-plum/5 border-plum/5 text-plum/40 opacity-60' }}">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold flex items-center gap-1.5 text-xs">
                                        <span>{{ $pInfo['icon'] }}</span>
                                        <span>{{ $pInfo['label'] }}</span>
                                    </span>
                                    <span class="text-[10px] font-black uppercase {{ $hasThis ? 'text-emerald-700' : 'text-plum/30' }}">
                                        {{ $hasThis ? '✓ Enabled' : 'Disabled' }}
                                    </span>
                                </div>
                                <p class="text-[10px] leading-relaxed {{ $hasThis ? 'text-emerald-800/80' : 'text-plum/40' }}">
                                    {{ $pInfo['desc'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- Right 5/12: Edit Profile & Change Password Forms --}}
        <div class="lg:col-span-5 space-y-6">
            
            {{-- Edit Profile Details Form --}}
            <div class="glass-card rounded-3xl p-6 sm:p-7 border border-white/80 bg-white/95 shadow-sm space-y-4">
                <h2 class="font-serif font-bold text-plum text-base pb-3 border-b border-plum/10 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    <span>Update Personal Info</span>
                </h2>

                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-semibold text-plum mb-1">Full Name *</label>
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('name', $user->name) }}" 
                            required
                            class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary font-medium"
                        >
                    </div>

                    <div>
                        <label class="block font-semibold text-plum mb-1">Mobile Phone Number</label>
                        <input 
                            type="text" 
                            name="phone" 
                            value="{{ old('phone', $user->phone) }}" 
                            placeholder="e.g. 9876543210"
                            class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary font-medium"
                        >
                    </div>

                    <div>
                        <label class="block font-semibold text-plum mb-1">Residential Address</label>
                        <textarea 
                            name="address" 
                            rows="2"
                            placeholder="Street address, city, pincode..."
                            class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary font-medium"
                        >{{ old('address', $user->address) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-plum mb-1">Emergency Contact</label>
                            <input 
                                type="text" 
                                name="emergency_contact_name" 
                                value="{{ old('emergency_contact_name', $user->emergency_contact_name) }}" 
                                placeholder="Father / Spouse Name"
                                class="w-full rounded-xl border-plum/20 bg-white p-2 text-plum focus:ring-primary"
                            >
                        </div>
                        <div>
                            <label class="block font-semibold text-plum mb-1">Emergency Phone</label>
                            <input 
                                type="text" 
                                name="emergency_contact_phone" 
                                value="{{ old('emergency_contact_phone', $user->emergency_contact_phone) }}" 
                                placeholder="Emergency number"
                                class="w-full rounded-xl border-plum/20 bg-white p-2 text-plum focus:ring-primary"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-plum mb-1">Profile Photo (Avatar)</label>
                        <input 
                            type="file" 
                            name="avatar" 
                            accept="image/*"
                            class="w-full text-xs text-plum/70 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary hover:file:bg-primary-100 cursor-pointer"
                        >
                    </div>

                    <button 
                        type="submit" 
                        class="w-full py-2.5 rounded-xl bg-primary hover:bg-primary-700 text-white font-bold text-xs shadow-md transition"
                    >
                        Save Profile Record
                    </button>
                </form>
            </div>

            {{-- Change Account Password Form --}}
            <div class="glass-card rounded-3xl p-6 sm:p-7 border border-white/80 bg-white/95 shadow-sm space-y-4">
                <h2 class="font-serif font-bold text-plum text-base pb-3 border-b border-plum/10 flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Change Account Password</span>
                </h2>

                <form action="{{ route('admin.profile.password') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-semibold text-plum mb-1">Current Password *</label>
                        <input 
                            type="password" 
                            name="current_password" 
                            required
                            placeholder="Current account password"
                            class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                        >
                    </div>

                    <div>
                        <label class="block font-semibold text-plum mb-1">New Password (Min 6 chars) *</label>
                        <input 
                            type="password" 
                            name="password" 
                            required
                            placeholder="Enter new password"
                            class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                        >
                    </div>

                    <div>
                        <label class="block font-semibold text-plum mb-1">Confirm New Password *</label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            required
                            placeholder="Re-type new password"
                            class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                        >
                    </div>

                    <button 
                        type="submit" 
                        class="w-full py-2.5 rounded-xl bg-plum hover:bg-plum/90 text-white font-bold text-xs shadow-md transition"
                    >
                        Update Password
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
