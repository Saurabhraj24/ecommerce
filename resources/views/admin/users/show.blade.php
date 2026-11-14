@extends('layouts.admin')

@section('title', 'Customer Profile: ' . $user->name . ' - Bombay Fashion Admin')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <div>
        <nav class="flex text-xs text-plum/60 mb-1">
            <a href="{{ route('admin.users.index') }}" class="hover:text-primary">&larr; Back to Customers</a>
        </nav>
        <h1 class="text-2xl font-serif font-bold text-plum">Customer Details: {{ $user->name }}</h1>
        <p class="text-xs text-plum/70">Account history, delivery locations, and purchases.</p>
    </div>

    {{-- Details Card --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass-card rounded-3xl p-6 border border-white/80 bg-white/85 shadow-sm text-xs space-y-3">
            <h3 class="font-serif font-bold text-plum text-sm pb-2 border-b border-plum/10">Profile Overview</h3>
            <div>
                <span class="text-plum/50 block text-[11px]">Full Name</span>
                <span class="font-bold text-plum text-sm">{{ $user->name }}</span>
            </div>
            <div>
                <span class="text-plum/50 block text-[11px]">Email Address</span>
                <span class="text-plum">{{ $user->email }}</span>
            </div>
            <div>
                <span class="text-plum/50 block text-[11px]">Phone Number</span>
                <span class="text-plum font-mono">{{ $user->phone ?? 'Not provided' }}</span>
            </div>
            <div>
                <span class="text-plum/50 block text-[11px]">Account Status</span>
                <form action="{{ route('admin.users.toggleActive', $user->id) }}" method="POST" class="mt-1">
                    @csrf
                    <button type="submit" class="px-3 py-1 rounded-full text-xs font-bold {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ $user->is_active ? 'Active (Click to Suspend)' : 'Suspended (Click to Activate)' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="md:col-span-2 glass-card rounded-3xl p-6 border border-white/80 bg-white/85 shadow-sm text-xs space-y-3">
            <h3 class="font-serif font-bold text-plum text-sm pb-2 border-b border-plum/10">Saved Delivery Addresses ({{ $user->addresses->count() }})</h3>
            @if($user->addresses->isEmpty())
                <p class="text-plum/50 italic py-2">No saved addresses on file.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($user->addresses as $addr)
                        <div class="p-3 rounded-2xl bg-plum/5 border border-plum/10">
                            <span class="font-bold text-plum block">{{ $addr->name }} ({{ strtoupper($addr->type) }})</span>
                            <p class="text-plum/70 leading-relaxed mt-1">
                                {{ $addr->address_line1 }}<br>
                                @if($addr->address_line2){{ $addr->address_line2 }}<br>@endif
                                {{ $addr->city }}, {{ $addr->state }} - {{ $addr->pincode }}<br>
                                Phone: {{ $addr->phone }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Orders History Table --}}
    <div class="glass-card rounded-3xl p-6 border border-white/80 bg-white/85 shadow-md">
        <h2 class="text-base font-serif font-bold text-plum pb-3 border-b border-plum/10 mb-4">
            Orders Placed by this Customer ({{ $user->orders->count() }})
        </h2>

        @if($user->orders->isEmpty())
            <p class="text-xs text-plum/50 italic py-4">No orders placed by this customer yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px]">
                            <th class="py-2.5">Order #</th>
                            <th class="py-2.5">Date</th>
                            <th class="py-2.5">Status</th>
                            <th class="py-2.5">Payment</th>
                            <th class="py-2.5 text-right">Total (₹)</th>
                            <th class="py-2.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($user->orders as $ord)
                            <tr>
                                <td class="py-2.5 font-mono font-bold text-plum">#{{ $ord->order_number }}</td>
                                <td class="py-2.5 text-plum/70">{{ $ord->created_at->format('d M Y') }}</td>
                                <td class="py-2.5">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-plum/10 text-plum">
                                        {{ $ord->order_status }}
                                    </span>
                                </td>
                                <td class="py-2.5 uppercase font-mono text-[10px]">{{ $ord->payment_method }} ({{ $ord->payment_status }})</td>
                                <td class="py-2.5 text-right font-serif font-bold text-plum">₹{{ number_format($ord->grand_total, 2) }}</td>
                                <td class="py-2.5 text-right">
                                    <a href="{{ route('admin.orders.show', $ord->id) }}" class="text-primary hover:underline font-bold">View Order</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
