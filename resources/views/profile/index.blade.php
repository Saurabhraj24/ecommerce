@extends('layouts.app')

@section('title', 'My Profile - Bombay Fashion Amritsar')

@section('content')
<div class="py-10 bg-festive-ivory/50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-serif font-bold text-festive-plum">Account Settings</h1>
            <p class="text-xs text-festive-plum/70 mt-1">Manage your personal profile and security credentials.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            {{-- Profile Navigation Sidebar --}}
            <aside class="md:col-span-1">
                <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/80 shadow-sm space-y-2">
                    <div class="flex items-center gap-3 pb-4 border-b border-festive-plum/10 mb-2">
                        <div class="w-12 h-12 rounded-full bg-festive-rose text-white font-serif font-bold text-lg flex items-center justify-center shadow">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-festive-plum text-sm truncate">{{ $user->name }}</h3>
                            <p class="text-[11px] text-festive-plum/50 truncate">{{ $user->email }}</p>
                        </div>
                    </div>

                    <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold bg-festive-rose text-white shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profile & Security
                    </a>
                    <a href="{{ route('profile.addresses') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-festive-plum hover:bg-festive-blush transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        Saved Addresses
                    </a>
                    <a href="{{ route('orders.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-festive-plum hover:bg-festive-blush transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        My Orders
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-festive-plum hover:bg-festive-blush transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Wishlist
                    </a>
                </div>
            </aside>

            {{-- Forms Column --}}
            <main class="md:col-span-2 space-y-6">
                
                {{-- Personal Information Form --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/80 shadow-md">
                    <h2 class="text-lg font-serif font-bold text-festive-plum pb-3 border-b border-festive-plum/10 mb-5">
                        Personal Information
                    </h2>

                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-semibold text-festive-plum mb-1">Full Name</label>
                            <input 
                                type="text" 
                                name="name" 
                                value="{{ old('name', $user->name) }}" 
                                required
                                class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-festive-plum mb-1">Email Address</label>
                            <input 
                                type="email" 
                                value="{{ $user->email }}" 
                                disabled
                                class="w-full text-xs rounded-xl border-festive-plum/10 bg-festive-plum/5 p-3 text-festive-plum/60 cursor-not-allowed"
                            >
                            <p class="text-[10px] text-festive-plum/50 mt-1">Contact customer support to update your registered email address.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-festive-plum mb-1">Phone Number</label>
                            <input 
                                type="tel" 
                                name="phone" 
                                value="{{ old('phone', $user->phone) }}" 
                                placeholder="10-digit mobile number"
                                class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                            >
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-festive-rose text-white shadow hover:bg-festive-rose/90 transition-all">
                                Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Password Security Form --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/80 shadow-md">
                    <h2 class="text-lg font-serif font-bold text-festive-plum pb-3 border-b border-festive-plum/10 mb-5">
                        Update Password
                    </h2>

                    <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-semibold text-festive-plum mb-1">Current Password *</label>
                            <input 
                                type="password" 
                                name="current_password" 
                                required
                                class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                            >
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-festive-plum mb-1">New Password *</label>
                                <input 
                                    type="password" 
                                    name="password" 
                                    required
                                    class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                                >
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-festive-plum mb-1">Confirm New Password *</label>
                                <input 
                                    type="password" 
                                    name="password_confirmation" 
                                    required
                                    class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                                >
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-festive-plum text-white shadow hover:bg-festive-plum/90 transition-all">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>

            </main>
        </div>

    </div>
</div>
@endsection
