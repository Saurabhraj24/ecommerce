@extends('layouts.admin')

@section('title', 'Manage Banners - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-serif font-bold text-plum">Store Banners & Hero Slider</h1>
            <p class="text-xs text-plum/70 mt-1">Upload high-resolution promotional hero sliders to Cloudinary.</p>
        </div>
        <a 
            href="{{ route('admin.banners.create') }}" 
            class="px-4 py-2.5 rounded-xl text-xs font-bold bg-primary text-white shadow hover:bg-primary-700 transition-all flex items-center gap-1.5 self-start sm:self-auto"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Banner
        </a>
    </div>

    <div class="glass-card rounded-3xl border border-white/80 bg-white/85 shadow-md overflow-hidden">
        @if($banners->isEmpty())
            <div class="p-12 text-center text-plum/60 text-xs italic">
                No custom promotional banners configured. Click "Add New Banner" to add hero slider slides.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px] bg-plum/5">
                            <th class="py-3 px-4">Banner Preview</th>
                            <th class="py-3 px-4">Title & Subtitle</th>
                            <th class="py-3 px-4">Position</th>
                            <th class="py-3 px-4">Link URL</th>
                            <th class="py-3 px-4 text-center">Order</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-plum/5">
                        @foreach($banners as $ban)
                            <tr class="hover:bg-plum/5 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="w-24 h-12 rounded-lg bg-plum/10 overflow-hidden border border-plum/10">
                                        <img src="{{ $ban->image_url }}" alt="{{ $ban->title }}" class="w-full h-full object-cover">
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="font-bold text-plum block">{{ $ban->title }}</span>
                                    @if($ban->subtitle)
                                        <span class="text-[11px] text-plum/60 block">{{ $ban->subtitle }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-plum/10 text-plum">
                                        {{ $ban->position }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-[11px] text-plum/70 font-mono truncate max-w-xs">
                                    {{ $ban->link_url ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-center font-mono">
                                    {{ $ban->display_order }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $ban->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-700' }}">
                                        {{ $ban->is_active ? 'Active' : 'Disabled' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.banners.edit', $ban->id) }}" class="p-1.5 rounded-lg border border-plum/20 text-plum hover:bg-plum/5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('admin.banners.destroy', $ban->id) }}" method="POST" onsubmit="return confirm('Delete this banner from Cloudinary?')">
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
                {{ $banners->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
