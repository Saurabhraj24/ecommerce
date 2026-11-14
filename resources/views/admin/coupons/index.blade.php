@extends('layouts.admin')

@section('title', 'Manage Coupons - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-plum">Discount Coupons</h1>
            <p class="text-xs text-plum/70 mt-1">Create festive promotion codes, flat discounts, or percentage reductions.</p>
        </div>
        <a 
            href="{{ route('admin.coupons.create') }}" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold bg-primary text-white shadow hover:bg-primary-700 transition-all flex items-center gap-1.5 self-start sm:self-auto"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create New Coupon
        </a>
    </div>

    <div class="glass-card rounded-3xl border border-white/80 bg-white/85 shadow-md overflow-hidden">
        @if($coupons->isEmpty())
            <div class="p-12 text-center text-plum/60 text-xs italic">
                No coupons found in the system. Click "Create New Coupon" to set up your first store discount code.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px] bg-plum/5">
                            <th class="py-3 px-4">Coupon Code</th>
                            <th class="py-3 px-4">Discount Type & Value</th>
                            <th class="py-3 px-4">Min. Spend</th>
                            <th class="py-3 px-4 text-center">Usage Count</th>
                            <th class="py-3 px-4">Validity</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($coupons as $cpn)
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3 px-4">
                                    <span class="font-mono font-bold text-sm text-primary">{{ $cpn->code }}</span>
                                    @if($cpn->description)
                                        <span class="block text-[11px] text-plum/60">{{ $cpn->description }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-bold text-plum">
                                    @if($cpn->discount_type === 'percentage')
                                        {{ $cpn->discount_value }}% OFF
                                        @if($cpn->max_discount_amount)
                                            <span class="block text-[10px] text-plum/50 font-normal">Max ₹{{ number_format($cpn->max_discount_amount, 2) }}</span>
                                        @endif
                                    @else
                                        Flat ₹{{ number_format($cpn->discount_value, 2) }} OFF
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-serif text-plum">
                                    ₹{{ number_format($cpn->min_order_amount, 2) }}
                                </td>
                                <td class="py-3 px-4 text-center font-mono">
                                    {{ $cpn->used_count }}
                                    @if($cpn->usage_limit)
                                        / {{ $cpn->usage_limit }}
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-[11px] text-plum/70">
                                    @if($cpn->start_date || $cpn->end_date)
                                        {{ $cpn->start_date ? $cpn->start_date->format('d M') : 'Start' }} - 
                                        {{ $cpn->end_date ? $cpn->end_date->format('d M Y') : 'Ongoing' }}
                                    @else
                                        No expiry
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $cpn->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-700' }}">
                                        {{ $cpn->is_active ? 'Active' : 'Disabled' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.coupons.edit', $cpn->id) }}" class="p-1.5 rounded-lg border border-plum/20 text-plum hover:bg-plum/5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('admin.coupons.destroy', $cpn->id) }}" method="POST" onsubmit="return confirm('Delete this coupon code?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-plum/10">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
