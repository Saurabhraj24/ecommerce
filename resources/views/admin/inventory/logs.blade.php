@extends('layouts.admin')

@section('title', 'Stock Audit Logs - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <nav class="flex text-xs text-plum/60 mb-1">
                <a href="{{ route('admin.inventory.index') }}" class="hover:text-primary">&larr; Back to Inventory</a>
            </nav>
            <h1 class="text-2xl font-serif font-bold text-plum">Stock History & Audit Trail</h1>
            <p class="text-xs text-plum/70">Complete chronological record of all inventory changes, sales reductions, and manual adjustments.</p>
        </div>
    </div>

    <div class="glass-card rounded-3xl border border-white/80 bg-white/85 shadow-md overflow-hidden">
        @if($logs->isEmpty())
            <div class="p-12 text-center text-plum/60 text-xs italic">
                No stock adjustment logs recorded yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px] bg-plum/5">
                            <th class="py-3 px-4">Date & Time</th>
                            <th class="py-3 px-4">Product</th>
                            <th class="py-3 px-4">Variant</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4 text-center">Change</th>
                            <th class="py-3 px-4 text-center">Qty (Old &rarr; New)</th>
                            <th class="py-3 px-4">Reason / Notes</th>
                            <th class="py-3 px-4">User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($logs as $log)
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3 px-4 text-[11px] text-plum/60">
                                    {{ $log->created_at->format('d M Y, h:i A') }}
                                </td>
                                <td class="py-3 px-4 font-bold text-plum">
                                    {{ $log->product?->name ?? 'Deleted Item' }}
                                </td>
                                <td class="py-3 px-4 text-plum/70">
                                    @if($log->variant)
                                        {{ $log->variant->size }} {{ $log->variant->color ? "({$log->variant->color})" : '' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-plum/10 text-plum">
                                        {{ str_replace('_', ' ', $log->type) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center font-bold font-mono">
                                    @if($log->quantity_change > 0)
                                        <span class="text-emerald-700">+{{ $log->quantity_change }}</span>
                                    @elseif($log->quantity_change < 0)
                                        <span class="text-rose-700">{{ $log->quantity_change }}</span>
                                    @else
                                        <span class="text-plum/50">0</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center font-mono text-[11px]">
                                    {{ $log->old_quantity }} &rarr; <strong class="text-plum">{{ $log->new_quantity }}</strong>
                                </td>
                                <td class="py-3 px-4 text-plum/80">
                                    {{ $log->reason }}
                                </td>
                                <td class="py-3 px-4 text-[11px] text-plum/60">
                                    {{ $log->user?->name ?? 'System' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-plum/10">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
