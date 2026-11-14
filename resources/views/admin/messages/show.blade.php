@extends('layouts.admin')

@section('title', 'Inquiry from ' . $message->name . ' - Bombay Fashion Admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <nav class="flex text-xs text-plum/60 mb-1">
            <a href="{{ route('admin.messages.index') }}" class="hover:text-primary">&larr; Back to Inbox</a>
        </nav>
        <h1 class="text-2xl font-serif font-bold text-plum">Customer Inquiry</h1>
        <p class="text-xs text-plum/70">Received on {{ $message->created_at->format('d M Y, h:i A') }}</p>
    </div>

    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-md space-y-6 text-xs">
        {{-- Sender Details --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pb-4 border-b border-plum/10">
            <div>
                <span class="text-plum/50 block text-[11px]">Sender Name</span>
                <span class="font-bold text-plum text-sm">{{ $message->name }}</span>
            </div>
            <div>
                <span class="text-plum/50 block text-[11px]">Email Address</span>
                <a href="mailto:{{ $message->email }}?subject={{ urlencode('Re: ' . $message->subject) }}" class="text-primary hover:underline font-medium">
                    {{ $message->email }}
                </a>
            </div>
            <div>
                <span class="text-plum/50 block text-[11px]">Phone Number</span>
                @if($message->phone)
                    <span class="text-plum font-mono">{{ $message->phone }}</span>
                    @php
                        $wa = preg_replace('/[^0-9]/', '', $message->phone);
                    @endphp
                    <a href="https://wa.me/{{ $wa }}" target="_blank" class="text-emerald-700 hover:underline block text-[10px] font-bold">
                        WhatsApp &rarr;
                    </a>
                @else
                    <span class="text-plum/40 italic">Not provided</span>
                @endif
            </div>
        </div>

        {{-- Subject --}}
        <div>
            <span class="text-plum/50 block text-[11px]">Subject</span>
            <h2 class="text-base font-bold text-plum mt-0.5">{{ $message->subject }}</h2>
        </div>

        {{-- Body Content --}}
        <div class="p-5 rounded-2xl bg-plum/5 border border-plum/10 text-plum leading-relaxed text-sm whitespace-pre-wrap">
            {{ $message->message }}
        </div>

        {{-- Action Buttons --}}
        <div class="pt-4 border-t border-plum/10 flex flex-wrap items-center justify-between gap-3">
            <a 
                href="mailto:{{ $message->email }}?subject={{ urlencode('Re: ' . $message->subject) }}" 
                class="px-5 py-2.5 rounded-xl text-xs font-bold bg-primary text-white hover:bg-primary-700 shadow flex items-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Reply by Email
            </a>

            <form action="{{ route('admin.messages.destroy', $message->id) }}" method="POST" onsubmit="return confirm('Delete this message permanently?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-semibold border border-rose-300 text-rose-600 hover:bg-rose-50">
                    Delete Inquiry
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
