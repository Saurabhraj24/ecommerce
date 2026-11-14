@extends('layouts.admin')

@section('title', 'Customer Inquiries - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-plum">Customer Contact Inbox</h1>
            <p class="text-xs text-plum/70 mt-1">Queries submitted via the Bombay Fashion website contact form.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.messages.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ !request('status') ? 'bg-primary text-white' : 'border border-plum/20 text-plum' }}">All</a>
            <a href="{{ route('admin.messages.index', ['status' => 'unread']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ request('status') === 'unread' ? 'bg-primary text-white' : 'border border-plum/20 text-plum' }}">Unread Only</a>
            <a href="{{ route('admin.messages.index', ['status' => 'read']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ request('status') === 'read' ? 'bg-primary text-white' : 'border border-plum/20 text-plum' }}">Read</a>
        </div>
    </div>

    <div class="glass-card rounded-3xl border border-white/80 bg-white/85 shadow-md overflow-hidden">
        @if($messages->isEmpty())
            <div class="p-12 text-center text-plum/60 text-xs italic">
                No customer inquiries received yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px] bg-plum/5">
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Sender</th>
                            <th class="py-3 px-4">Subject</th>
                            <th class="py-3 px-4">Message Snippet</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($messages as $msg)
                            <tr class="hover:bg-plum/5 transition-colors {{ !$msg->is_read ? 'font-bold bg-primary-50/20' : '' }}">
                                <td class="py-3 px-4 text-plum/60 text-[11px] whitespace-nowrap">
                                    {{ $msg->created_at->format('d M Y, h:i A') }}
                                </td>
                                <td class="py-3 px-4 text-plum whitespace-nowrap">
                                    <span class="block text-sm">{{ $msg->name }}</span>
                                    <span class="text-[10px] text-plum/60">{{ $msg->email }}</span>
                                </td>
                                <td class="py-3 px-4 text-plum font-medium max-w-xs truncate">
                                    {{ $msg->subject }}
                                </td>
                                <td class="py-3 px-4 text-plum/70 max-w-md truncate font-normal">
                                    {{ Str::limit($msg->message, 80) }}
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $msg->is_read ? 'bg-gray-100 text-gray-700' : 'bg-primary-100 text-primary-800' }}">
                                        {{ $msg->is_read ? 'Read' : 'New' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('admin.messages.show', $msg->id) }}" class="px-3 py-1 rounded-lg bg-primary text-white font-bold hover:bg-primary-700 text-[11px]">
                                            Open
                                        </a>
                                        <form action="{{ route('admin.messages.destroy', $msg->id) }}" method="POST" onsubmit="return confirm('Delete message?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50" title="Delete">
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
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
