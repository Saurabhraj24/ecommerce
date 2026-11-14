@extends('layouts.admin')

@section('title', 'Edit Staff Member - ' . $staff->name . ' - Bombay Fashion Admin')

@section('content')
@php
    $adminRoleObj = $roles->where('name', 'admin')->first();
    $currentPermissions = $staff->permissions ?? array_keys($availablePermissions ?? []);
@endphp

<div class="max-w-4xl mx-auto space-y-6" x-data="{
    avatarPreview: '{{ $staff->avatar_url }}',
    showPassword: false,
    selectedRoleId: '{{ old('role_id', $staff->role_id) }}',
    adminRoleId: '{{ $adminRoleObj?->id }}',
    selectedPermissions: {{ json_encode(old('permissions', $currentPermissions)) }},
    handleAvatar(e) {
        const file = e.target.files[0];
        if (file) {
            this.avatarPreview = URL.createObjectURL(file);
        }
    },
    applyPreset(preset) {
        if (preset === 'pos') {
            this.selectedPermissions = ['pos.access', 'attendance.manage'];
        } else if (preset === 'sales') {
            this.selectedPermissions = ['pos.access', 'products.manage', 'attendance.manage'];
        } else if (preset === 'stock') {
            this.selectedPermissions = ['inventory.manage', 'products.manage', 'attendance.manage'];
        } else if (preset === 'manager') {
            this.selectedPermissions = ['pos.access', 'orders.manage', 'products.manage', 'inventory.manage', 'attendance.manage', 'reviews.moderate', 'messages.manage'];
        } else if (preset === 'all') {
            this.selectedPermissions = {{ json_encode(array_keys($availablePermissions ?? [])) }};
        } else if (preset === 'none') {
            this.selectedPermissions = [];
        }
    },
    togglePerm(key) {
        const idx = this.selectedPermissions.indexOf(key);
        if (idx > -1) {
            this.selectedPermissions.splice(idx, 1);
        } else {
            this.selectedPermissions.push(key);
        }
    }
}">
    <div>
        <nav class="flex text-xs text-plum/60 mb-1">
            <a href="{{ route('admin.staff.index') }}" class="hover:text-primary">&larr; Back to Staff Directory</a>
        </nav>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-serif font-bold text-plum">Edit: {{ $staff->name }}</h1>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $staff->isAdmin() ? 'bg-gold-100 text-gold-900 border border-gold-200' : 'bg-primary-100 text-primary-800' }}">
                {{ ucfirst($staff->role?->name ?? 'Staff') }}
            </span>
            @if($staff->employee_id)
                <span class="text-xs font-mono font-bold text-plum/60 bg-plum/5 px-2 py-0.5 rounded-md">
                    {{ $staff->employee_id }}
                </span>
            @endif
        </div>
        <p class="text-xs text-plum/70 mt-0.5">Update staff member photo, designation, department, contact, and permissions.</p>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
            <p class="font-bold mb-1">Please correct the following errors:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST" enctype="multipart/form-data" class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/95 shadow-md space-y-8 text-xs">
        @csrf
        @method('PUT')

        {{-- 1. Profile Photo & Core Identity --}}
        <div>
            <h2 class="text-sm font-serif font-bold text-plum pb-3 border-b border-plum/10 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-primary-100 text-primary-700 flex items-center justify-center text-xs">👤</span>
                Profile Photo & Personal Identity
            </h2>

            <div class="mt-4 flex flex-col sm:flex-row items-center gap-6">
                {{-- Avatar Preview Frame --}}
                <div class="relative shrink-0 text-center">
                    <div class="w-24 h-24 rounded-2xl border-2 border-primary/20 bg-primary-50/50 overflow-hidden flex items-center justify-center shadow-inner relative group">
                        <img :src="avatarPreview" class="w-full h-full object-cover" alt="{{ $staff->name }}">
                    </div>
                </div>

                {{-- Avatar Upload Control --}}
                <div class="flex-1 space-y-1.5 w-full">
                    <label class="block font-semibold text-plum">Change Profile Photo (JPG, PNG, WebP)</label>
                    <input 
                        type="file" 
                        name="avatar" 
                        accept="image/*"
                        @change="handleAvatar"
                        class="w-full text-xs text-plum/70 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-700 cursor-pointer bg-plum/5 rounded-xl p-1.5"
                    >
                    <p class="text-[11px] text-plum/50">Leave empty to keep existing photo (Max 3MB).</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-5">
                <div>
                    <label class="block font-semibold text-plum mb-1">Full Name *</label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name', $staff->name) }}" 
                        required 
                        class="w-full rounded-xl border border-plum/20 bg-white p-3 text-plum focus:ring-primary font-medium"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Employee / Staff ID</label>
                    <input 
                        type="text" 
                        name="employee_id" 
                        value="{{ old('employee_id', $staff->employee_id) }}" 
                        placeholder="e.g. BF-EMP-105"
                        class="w-full rounded-xl border border-plum/20 bg-white p-3 text-plum font-mono focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Store Role *</label>
                    <select name="role_id" x-model="selectedRoleId" required class="w-full rounded-xl border border-plum/20 bg-white p-3 text-plum font-bold focus:ring-primary">
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}" {{ old('role_id', $staff->role_id) == $r->id ? 'selected' : '' }}>
                                {{ ucfirst($r->name) }} {{ $r->name === 'admin' ? '(Super Admin - Full Control)' : '(Staff - Catalog, POS & Orders)' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- 2. Showroom Job Details & Department --}}
        <div>
            <h2 class="text-sm font-serif font-bold text-plum pb-3 border-b border-plum/10 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-gold-100 text-gold-800 flex items-center justify-center text-xs">👔</span>
                Showroom Department & Position
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block font-semibold text-plum mb-1">Job Designation / Title</label>
                    <input 
                        type="text" 
                        name="designation" 
                        value="{{ old('designation', $staff->designation) }}" 
                        placeholder="e.g. Senior Saree Consultant"
                        class="w-full rounded-xl border border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                        list="designations-list"
                    >
                    <datalist id="designations-list">
                        <option value="Senior Saree Consultant">
                        <option value="Bridal Specialist">
                        <option value="Master Tailor & Cutting Head">
                        <option value="Showroom Floor Manager">
                        <option value="Billing & POS Cashier">
                        <option value="School Uniform Incharge">
                        <option value="Inventory & Stock Handler">
                    </datalist>
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Department / Counter Section</label>
                    <select name="department" class="w-full rounded-xl border border-plum/20 bg-white p-3 text-plum focus:ring-primary">
                        <option value="">-- Select Section --</option>
                        @foreach([
                            'Bridal & Designer Sarees',
                            'Punjabi Salwar Suits & Fabrics',
                            'School Uniforms Section',
                            'Men\'s Ethnic Wear',
                            'Children & Kids Wear',
                            'Tailoring & Alterations Counter',
                            'Cash Billing & Accounts'
                        ] as $dept)
                            <option value="{{ $dept }}" {{ old('department', $staff->department) == $dept ? 'selected' : '' }}>
                                {{ $dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Date of Joining</label>
                    <input 
                        type="date" 
                        name="date_of_joining" 
                        value="{{ old('date_of_joining', $staff->date_of_joining?->format('Y-m-d')) }}" 
                        class="w-full rounded-xl border border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>
            </div>
        </div>

        {{-- 3. Login Credentials & Direct Contact --}}
        <div>
            <h2 class="text-sm font-serif font-bold text-plum pb-3 border-b border-plum/10 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-teal-100 text-teal-800 flex items-center justify-center text-xs">🔐</span>
                Login Credentials & Contact
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block font-semibold text-plum mb-1">Email Address (Login ID) *</label>
                    <input 
                        type="email" 
                        name="email" 
                        value="{{ old('email', $staff->email) }}" 
                        required 
                        class="w-full rounded-xl border border-plum/20 bg-white p-3 text-plum font-medium focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Phone Number (Calling / WhatsApp) *</label>
                    <input 
                        type="tel" 
                        name="phone" 
                        value="{{ old('phone', $staff->phone) }}" 
                        required
                        class="w-full rounded-xl border border-plum/20 bg-white p-3 text-plum font-medium focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">New Password (Leave blank to keep current)</label>
                    <div class="relative">
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            name="password" 
                            class="w-full rounded-xl border border-plum/20 bg-white p-3 pr-10 text-plum focus:ring-primary font-mono"
                            placeholder="Optional new password"
                        >
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-plum/50 hover:text-plum cursor-pointer"
                        >
                            <span x-text="showPassword ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Granular Staff Module Permissions --}}
        <div>
            <div class="pb-3 border-b border-plum/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <h2 class="text-sm font-serif font-bold text-plum flex items-center gap-2">
                    <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-800 flex items-center justify-center text-xs">🛡️</span>
                    Staff Module Permissions & Access Control
                </h2>
                <div class="flex items-center gap-1.5 flex-wrap" x-show="selectedRoleId != adminRoleId">
                    <span class="text-[11px] font-semibold text-plum/50 mr-1">Presets:</span>
                    <button type="button" @click="applyPreset('pos')" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200/60 font-semibold text-[11px] transition">🧾 POS Only</button>
                    <button type="button" @click="applyPreset('sales')" class="px-2.5 py-1 rounded-lg bg-primary-50 text-primary-800 hover:bg-primary-100 border border-primary-200/60 font-semibold text-[11px] transition">👗 Sales & Products</button>
                    <button type="button" @click="applyPreset('stock')" class="px-2.5 py-1 rounded-lg bg-gold-50 text-gold-900 hover:bg-gold-100 border border-gold-200/60 font-semibold text-[11px] transition">📦 Stock Keeper</button>
                    <button type="button" @click="applyPreset('manager')" class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-900 hover:bg-indigo-100 border border-indigo-200/60 font-semibold text-[11px] transition">👔 Floor Manager</button>
                    <button type="button" @click="applyPreset('all')" class="px-2 py-1 rounded-lg bg-plum/5 text-plum hover:bg-plum/10 font-semibold text-[11px] transition">All</button>
                    <button type="button" @click="applyPreset('none')" class="px-2 py-1 rounded-lg bg-plum/5 text-plum/60 hover:text-plum font-semibold text-[11px] transition">Clear</button>
                </div>
            </div>

            {{-- Super Admin Notice --}}
            <div x-show="selectedRoleId == adminRoleId" class="mt-4 p-4 rounded-2xl bg-gradient-to-r from-primary-50/60 to-gold-50/60 border border-primary-200/60 flex items-center gap-3">
                <span class="text-2xl">👑</span>
                <div>
                    <strong class="text-plum font-bold block">Super Administrator Role Selected</strong>
                    <span class="text-plum/70 text-[11px]">Admins have complete, unrestricted access to all store modules, settings, reports, staff payroll, and master databases. No individual restrictions required.</span>
                </div>
            </div>

            {{-- Staff Granular Checkbox Grid --}}
            <div x-show="selectedRoleId != adminRoleId" class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($availablePermissions as $permKey => $perm)
                    <label 
                        @click.prevent="togglePerm('{{ $permKey }}')"
                        class="p-4 rounded-2xl border transition-all cursor-pointer flex items-start gap-3 select-none"
                        :class="selectedPermissions.includes('{{ $permKey }}') ? 'bg-primary-50/40 border-primary-300 shadow-xs ring-1 ring-primary/30' : 'bg-white border-plum/15 opacity-70 hover:opacity-100'"
                    >
                        <input 
                            type="checkbox" 
                            name="permissions[]" 
                            value="{{ $permKey }}"
                            :checked="selectedPermissions.includes('{{ $permKey }}')"
                            class="mt-1 rounded border-plum/30 text-primary focus:ring-primary pointer-events-none"
                        >
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <span class="font-bold text-plum text-xs flex items-center gap-1.5">
                                    <span>{{ $perm['icon'] ?? '🔹' }}</span>
                                    <span>{{ $perm['label'] }}</span>
                                </span>
                                <span class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded bg-plum/5 text-plum/60">
                                    {{ $perm['category'] ?? 'General' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-plum/60 leading-relaxed">{{ $perm['desc'] }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- 5. Personal, Residential & Emergency Information (Real User Records) --}}
        <div>
            <h2 class="text-sm font-serif font-bold text-plum pb-3 border-b border-plum/10 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center text-xs">🏠</span>
                Residential Address & Emergency Contact
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block font-semibold text-plum mb-1">Residential Address (Amritsar / Local)</label>
                    <textarea 
                        name="address" 
                        rows="3" 
                        class="w-full rounded-xl border border-plum/20 bg-white p-3 text-plum focus:ring-primary leading-relaxed"
                    >{{ old('address', $staff->address) }}</textarea>
                </div>

                <div class="space-y-3">
                    <div>
                        <label class="block font-semibold text-plum mb-1">Emergency Contact Person Name</label>
                        <input 
                            type="text" 
                            name="emergency_contact_name" 
                            value="{{ old('emergency_contact_name', $staff->emergency_contact_name) }}" 
                            class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                        >
                    </div>

                    <div>
                        <label class="block font-semibold text-plum mb-1">Emergency Contact Phone Number</label>
                        <input 
                            type="tel" 
                            name="emergency_contact_phone" 
                            value="{{ old('emergency_contact_phone', $staff->emergency_contact_phone) }}" 
                            class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                        >
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block font-semibold text-plum mb-1">Monthly Base Salary (₹ Optional / Confidential)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-bold text-plum/50">₹</span>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="salary" 
                            value="{{ old('salary', $staff->salary) }}" 
                            class="w-full rounded-xl border border-plum/20 bg-white p-3 pl-8 text-plum font-mono focus:ring-primary"
                        >
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Store Owner Notes & Remarks (Private)</label>
                    <input 
                        type="text" 
                        name="notes" 
                        value="{{ old('notes', $staff->notes) }}" 
                        class="w-full rounded-xl border border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>
            </div>
        </div>

        {{-- 5. Salary, Banking & Shift Details (For Payroll & Direct Transfers) --}}
        <div>
            <h2 class="text-sm font-serif font-bold text-plum pb-3 border-b border-plum/10 flex items-center gap-2">
                <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">💳</span>
                Bank Account, UPI & Showroom Shift
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                <div>
                    <label class="block font-semibold text-plum mb-1">Bank Name</label>
                    <input 
                        type="text" 
                        name="bank_name" 
                        value="{{ old('bank_name', $staff->bank_name) }}" 
                        placeholder="e.g. HDFC Bank / SBI Amritsar"
                        class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Bank Account Number</label>
                    <input 
                        type="text" 
                        name="bank_account_no" 
                        value="{{ old('bank_account_no', $staff->bank_account_no) }}" 
                        placeholder="e.g. 50100234567890"
                        class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-plum font-mono focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">IFSC Code</label>
                    <input 
                        type="text" 
                        name="bank_ifsc" 
                        value="{{ old('bank_ifsc', $staff->bank_ifsc) }}" 
                        placeholder="e.g. HDFC0001234"
                        class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-plum font-mono uppercase focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">UPI ID (PhonePe / GPay)</label>
                    <input 
                        type="text" 
                        name="upi_id" 
                        value="{{ old('upi_id', $staff->upi_id) }}" 
                        placeholder="e.g. 9814100000@okaxis"
                        class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-plum font-mono focus:ring-primary"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block font-semibold text-plum mb-1">Weekly Off Day</label>
                    <select name="weekly_off" class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-plum focus:ring-primary">
                        <option value="Sunday" {{ old('weekly_off', $staff->weekly_off ?? 'Sunday') == 'Sunday' ? 'selected' : '' }}>Sunday (Standard)</option>
                        <option value="Monday" {{ old('weekly_off', $staff->weekly_off) == 'Monday' ? 'selected' : '' }}>Monday</option>
                        <option value="Tuesday" {{ old('weekly_off', $staff->weekly_off) == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                        <option value="Wednesday" {{ old('weekly_off', $staff->weekly_off) == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                        <option value="Thursday" {{ old('weekly_off', $staff->weekly_off) == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                        <option value="Friday" {{ old('weekly_off', $staff->weekly_off) == 'Friday' ? 'selected' : '' }}>Friday</option>
                        <option value="Saturday" {{ old('weekly_off', $staff->weekly_off) == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                        <option value="Rotational" {{ old('weekly_off', $staff->weekly_off) == 'Rotational' ? 'selected' : '' }}>Rotational Off</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Showroom Shift Timings</label>
                    <input 
                        type="text" 
                        name="shift_timings" 
                        value="{{ old('shift_timings', $staff->shift_timings ?? '10:00 AM - 08:30 PM') }}" 
                        placeholder="e.g. 10:00 AM - 08:30 PM"
                        class="w-full rounded-xl border border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                    >
                </div>
            </div>
        </div>

        {{-- Active Status Toggle --}}
        @if($staff->id !== auth()->id())
            <div class="p-4 rounded-2xl bg-plum/5 border border-plum/10 flex items-center justify-between">
                <div>
                    <span class="font-bold text-plum block">Account Status</span>
                    <span class="text-[11px] text-plum/60">Allow this staff user to log into the Bombay Fashion store management desk.</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $staff->is_active) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                </label>
            </div>
        @endif

        {{-- Form Action Buttons --}}
        <div class="pt-6 flex items-center justify-end gap-3 border-t border-plum/10">
            <a href="{{ route('admin.staff.index') }}" class="px-5 py-3 rounded-xl border border-plum/20 text-plum hover:bg-plum/5 font-semibold transition-all">
                Cancel
            </a>
            <button 
                type="submit" 
                class="px-8 py-3.5 rounded-xl bg-primary text-white font-bold shadow-lg shadow-primary/25 hover:bg-primary-700 transition-all cursor-pointer flex items-center gap-2"
            >
                <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Save Staff Profile Changes</span>
            </button>
        </div>
    </form>
</div>
@endsection
