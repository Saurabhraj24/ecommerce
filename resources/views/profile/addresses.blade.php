@extends('layouts.app')

@section('title', 'Saved Addresses - Bombay Fashion Amritsar')

@section('content')
<div 
    class="py-10 bg-festive-ivory/50"
    x-data="{ addAddressModal: false }"
>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-serif font-bold text-festive-plum">Saved Addresses</h1>
                <p class="text-xs text-festive-plum/70 mt-1">Manage delivery locations for quick checkout at Bombay Fashion.</p>
            </div>
            <button 
                type="button" 
                @click="addAddressModal = true"
                class="px-5 py-2.5 rounded-xl text-xs font-bold bg-festive-rose text-white shadow hover:bg-festive-rose/90 transition-all flex items-center gap-1.5 self-start sm:self-auto"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add New Address
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Navigation Sidebar --}}
            <aside class="md:col-span-1">
                <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/80 shadow-sm space-y-2">
                    <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-festive-plum hover:bg-festive-blush transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Profile & Security
                    </a>
                    <a href="{{ route('profile.addresses') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold bg-festive-rose text-white shadow-sm">
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

            {{-- Addresses Grid --}}
            <main class="md:col-span-2">
                @if($addresses->isEmpty())
                    <div class="glass-card rounded-3xl p-10 text-center border border-white/80 bg-white/80 shadow-sm">
                        <p class="text-sm text-festive-plum font-semibold mb-2">No Saved Addresses</p>
                        <p class="text-xs text-festive-plum/60 mb-4">Add your home or office address for 1-click checkout.</p>
                        <button 
                            type="button" 
                            @click="addAddressModal = true"
                            class="px-5 py-2 rounded-xl text-xs font-bold bg-festive-rose text-white shadow"
                        >
                            Add Your First Address
                        </button>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($addresses as $addr)
                            <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/80 shadow-sm flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <span class="text-xs font-bold text-festive-plum">{{ $addr->name }}</span>
                                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-festive-blush text-festive-rose ml-2">
                                                {{ $addr->type }}
                                            </span>
                                        </div>
                                        @if($addr->is_default)
                                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                                Default
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-xs text-festive-plum/70 leading-relaxed mb-4">
                                        {{ $addr->address_line1 }}<br>
                                        @if($addr->address_line2){{ $addr->address_line2 }}<br>@endif
                                        {{ $addr->city }}, {{ $addr->state }} - {{ $addr->pincode }}<br>
                                        <span class="text-festive-plum font-medium">Phone: {{ $addr->phone }}</span>
                                    </p>
                                </div>

                                <div class="pt-3 border-t border-festive-plum/10 flex items-center justify-between text-xs">
                                    @if(!$addr->is_default)
                                        <form action="{{ route('profile.addresses.default', $addr->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-festive-rose hover:underline font-semibold text-[11px]">
                                                Set as Default
                                            </button>
                                        </form>
                                    @else
                                        <span></span>
                                    @endif

                                    <form action="{{ route('profile.addresses.destroy', $addr->id) }}" method="POST" onsubmit="return confirm('Delete this address?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:underline text-[11px] font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </main>
        </div>

    </div>

    {{-- Add Address Modal --}}
    <div 
        x-show="addAddressModal" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-festive-plum/40 backdrop-blur-sm"
        @click.self="addAddressModal = false"
    >
        <div class="glass-card rounded-3xl p-6 sm:p-8 max-w-lg w-full bg-white shadow-2xl border border-white">
            <div class="flex items-center justify-between pb-3 border-b border-festive-plum/10 mb-4">
                <h3 class="text-lg font-serif font-bold text-festive-plum">Add New Address</h3>
                <button @click="addAddressModal = false" class="text-festive-plum/50 hover:text-festive-rose font-bold text-lg">&times;</button>
            </div>

            <form action="{{ route('profile.addresses.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-festive-plum mb-1">Full Name *</label>
                        <input type="text" name="name" required class="w-full rounded-xl border-festive-plum/20 p-2.5 text-festive-plum">
                    </div>
                    <div>
                        <label class="block font-semibold text-festive-plum mb-1">Phone Number *</label>
                        <input type="tel" name="phone" required class="w-full rounded-xl border-festive-plum/20 p-2.5 text-festive-plum">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-festive-plum mb-1">Address Line 1 *</label>
                    <input type="text" name="address_line1" required placeholder="Flat, House no., Building, Street" class="w-full rounded-xl border-festive-plum/20 p-2.5 text-festive-plum">
                </div>

                <div>
                    <label class="block font-semibold text-festive-plum mb-1">Address Line 2</label>
                    <input type="text" name="address_line2" placeholder="Colony, Area, Landmark" class="w-full rounded-xl border-festive-plum/20 p-2.5 text-festive-plum">
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="block font-semibold text-festive-plum mb-1">City *</label>
                        <input type="text" name="city" value="Amritsar" required class="w-full rounded-xl border-festive-plum/20 p-2.5 text-festive-plum">
                    </div>
                    <div>
                        <label class="block font-semibold text-festive-plum mb-1">State *</label>
                        <input type="text" name="state" value="Punjab" required class="w-full rounded-xl border-festive-plum/20 p-2.5 text-festive-plum">
                    </div>
                    <div>
                        <label class="block font-semibold text-festive-plum mb-1">PIN Code *</label>
                        <input type="text" name="pincode" value="143001" required class="w-full rounded-xl border-festive-plum/20 p-2.5 text-festive-plum font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block font-semibold text-festive-plum mb-1">Address Type</label>
                        <select name="type" class="w-full rounded-xl border-festive-plum/20 p-2.5 text-festive-plum">
                            <option value="home">Home</option>
                            <option value="work">Work / Office</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="flex items-center pt-5">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_default" value="1" class="rounded text-festive-rose focus:ring-festive-rose">
                            <span class="text-festive-plum">Set as default</span>
                        </label>
                    </div>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-festive-plum/10">
                    <button type="button" @click="addAddressModal = false" class="px-4 py-2 rounded-xl border border-festive-plum/20 text-festive-plum hover:bg-festive-blush">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-festive-rose text-white font-bold shadow hover:bg-festive-rose/90">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
