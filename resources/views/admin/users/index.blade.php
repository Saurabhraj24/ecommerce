@extends('layouts.admin')

@section('title', 'Manage Customers - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-plum">Registered Customers</h1>
            <p class="text-xs text-plum/70 mt-1">View Bombay Fashion patron accounts, order volumes, and lifetime spend.</p>
        </div>
    </div>

    {{-- Filter Search --}}
    <div class="glass-card rounded-2xl p-4 sm:p-5 border border-white/80 bg-white/80 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex gap-3 text-xs">
            <input 
                type="text" 
                name="q" 
                value="{{ request('q') }}" 
                placeholder="Search customers by name, email, or phone number..." 
                class="w-full rounded-xl border-plum/20 bg-white/70 p-2.5 text-plum focus:ring-primary"
            >
            <button type="submit" class="px-5 py-2.5 rounded-xl font-bold bg-primary text-white hover:bg-primary-700 shadow-sm flex-shrink-0">
                Search
            </button>
            @if(request('q'))
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 rounded-xl border border-plum/20 text-plum hover:bg-plum/5 flex-shrink-0">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <div class="glass-card rounded-3xl border border-white/80 bg-white/85 shadow-md overflow-hidden">
        @if($users->isEmpty())
            <div class="p-12 text-center text-plum/60 text-xs italic">
                No customer accounts found in the system yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px] bg-plum/5">
                            <th class="py-3 px-4">Customer</th>
                            <th class="py-3 px-4">Contact</th>
                            <th class="py-3 px-4 text-center">Orders</th>
                            <th class="py-3 px-4 text-right">Lifetime Spend</th>
                            <th class="py-3 px-4">Joined Date</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($users as $cust)
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3 px-4">
                                    <span class="font-bold text-plum block text-sm">{{ $cust->name }}</span>
                                    <span class="text-[10px] text-plum/50 font-mono">ID: #{{ $cust->id }}</span>
                                </td>
                                <td class="py-3 px-4 text-plum/80">
                                    <span class="block">{{ $cust->email }}</span>
                                    <span class="text-[11px] text-plum/60">{{ $cust->phone ?? 'No phone' }}</span>
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-plum">
                                    {{ $cust->orders_count }}
                                </td>
                                <td class="py-3 px-4 text-right font-serif font-bold text-primary">
                                    ₹{{ number_format($cust->orders_sum_grand_total ?? 0, 2) }}
                                </td>
                                <td class="py-3 px-4 text-[11px] text-plum/60">
                                    {{ $cust->created_at->format('d M Y') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <form action="{{ route('admin.users.toggleActive', $cust->id) }}" method="POST">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $cust->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}"
                                        >
                                            {{ $cust->is_active ? 'Active' : 'Suspended' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('admin.users.show', $cust->id) }}" class="px-3 py-1 rounded-lg bg-plum/10 text-plum hover:bg-plum/20 font-bold text-[11px]">
                                        Profile
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-plum/10">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
