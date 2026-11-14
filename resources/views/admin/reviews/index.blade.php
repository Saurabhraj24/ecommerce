@extends('layouts.admin')

@section('title', 'Moderate Reviews - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-plum">Customer Reviews Moderation</h1>
            <p class="text-xs text-plum/70 mt-1">Approve real verified feedback before it appears on the public storefront.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reviews.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ !request('status') ? 'bg-primary text-white' : 'border border-plum/20 text-plum' }}">All</a>
            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ request('status') === 'pending' ? 'bg-primary text-white' : 'border border-plum/20 text-plum' }}">Pending Moderation</a>
            <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold {{ request('status') === 'approved' ? 'bg-primary text-white' : 'border border-plum/20 text-plum' }}">Approved</a>
        </div>
    </div>

    <div class="glass-card rounded-3xl border border-white/80 bg-white/85 shadow-md overflow-hidden">
        @if($reviews->isEmpty())
            <div class="p-12 text-center text-plum/60 text-xs italic">
                No customer reviews submitted for moderation yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px] bg-plum/5">
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Product</th>
                            <th class="py-3 px-4">Customer</th>
                            <th class="py-3 px-4">Rating</th>
                            <th class="py-3 px-4">Review Text</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($reviews as $rev)
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3 px-4 text-plum/60 text-[11px] whitespace-nowrap">
                                    {{ $rev->created_at->format('d M Y') }}
                                </td>
                                <td class="py-3 px-4 font-bold text-plum max-w-xs truncate">
                                    {{ $rev->product?->name ?? 'Deleted Item' }}
                                </td>
                                <td class="py-3 px-4 text-plum/80 whitespace-nowrap">
                                    {{ $rev->user?->name ?? 'Customer' }}
                                </td>
                                <td class="py-3 px-4 text-gold font-bold whitespace-nowrap">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span>{{ $i <= $rev->rating ? '★' : '☆' }}</span>
                                    @endfor
                                    <span class="text-[10px] text-plum/60 ml-1">({{ $rev->rating }})</span>
                                </td>
                                <td class="py-3 px-4 text-plum/80 max-w-md leading-relaxed">
                                    @if($rev->title)
                                        <div class="font-bold text-plum text-[11px] mb-0.5">{{ $rev->title }}</div>
                                    @endif
                                    <div>{{ $rev->comment }}</div>
                                    @if($rev->fit_feedback || $rev->quality_rating)
                                        <div class="flex items-center gap-2 mt-1 text-[10px] text-plum/60">
                                            @if($rev->fit_feedback)
                                                <span class="bg-plum/5 px-1.5 py-0.5 rounded">Fit: {{ ucwords(str_replace('_', ' ', $rev->fit_feedback)) }}</span>
                                            @endif
                                            @if($rev->quality_rating)
                                                <span class="bg-plum/5 px-1.5 py-0.5 rounded">Quality: {{ ucfirst($rev->quality_rating) }}</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if(!empty($rev->images))
                                        <div class="flex gap-1.5 mt-2">
                                            @foreach($rev->images as $img)
                                                <a href="{{ $img['image_url'] }}" target="_blank" class="w-10 h-10 rounded-lg overflow-hidden border border-plum/10 block hover:scale-110 transition-transform" title="View customer photo">
                                                    <img src="{{ $img['image_url'] }}" class="w-full h-full object-cover">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $rev->is_approved ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $rev->is_approved ? 'Approved' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if(!$rev->is_approved)
                                            <form action="{{ route('admin.reviews.approve', $rev->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-bold hover:bg-emerald-700 text-[11px]">
                                                    Approve
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.reviews.destroy', $rev->id) }}" method="POST" onsubmit="return confirm('Delete review?')">
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
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
