@extends('layouts.admin')

@section('title', 'Staff Activity Log - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-serif font-bold text-plum">Staff Audit & Activity Trail</h1>
            <p class="text-xs text-plum/70 mt-1">Immutable security log of all actions taken inside the administrative management portal.</p>
        </div>
    </div>

    <div class="glass-card rounded-3xl border border-white/80 bg-white/85 shadow-md overflow-hidden">
        @if($logs->isEmpty())
            <div class="p-12 text-center text-plum/60 text-xs italic">
                No activity records logged yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px] bg-plum/5">
                            <th class="py-3 px-4">Timestamp</th>
                            <th class="py-3 px-4">Staff Member</th>
                            <th class="py-3 px-4">Action</th>
                            <th class="py-3 px-4">Target Entity</th>
                            <th class="py-3 px-4">Event Details</th>
                            <th class="py-3 px-4">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($logs as $act)
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3 px-4 text-plum/60 text-[11px] whitespace-nowrap">
                                    {{ $act->created_at->format('d M Y, h:i:s A') }}
                                </td>
                                <td class="py-3 px-4 font-bold text-plum whitespace-nowrap">
                                    {{ $act->user?->name ?? 'System' }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-plum/10 text-plum font-mono">
                                        {{ $act->action }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-plum/70 text-[11px]">
                                    @if($act->subject_type)
                                        {{ class_basename($act->subject_type) }} #{{ $act->subject_id }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-plum/80 font-mono text-[11px] max-w-xs truncate">
                                    {{ json_encode($act->details) }}
                                </td>
                                <td class="py-3 px-4 text-plum/50 font-mono text-[10px]">
                                    {{ $act->ip_address ?? '127.0.0.1' }}
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
