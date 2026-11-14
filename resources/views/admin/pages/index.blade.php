@extends('layouts.admin')

@section('title', 'CMS Pages - Bombay Fashion Admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-serif font-bold text-plum">Store Content & Policy Pages</h1>
            <p class="text-xs text-plum/70 mt-1">Edit About Us, Terms & Conditions, Return Policy, and Shipping Guidelines.</p>
        </div>
    </div>

    <div class="glass-card rounded-3xl border border-white/80 bg-white/85 shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-plum/10 text-plum/60 font-semibold uppercase tracking-wider text-[10px] bg-plum/5">
                        <th class="py-3 px-4">Page Title</th>
                        <th class="py-3 px-4">URL Slug</th>
                        <th class="py-3 px-4">Last Updated</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-plum/5">
                    @foreach($pages as $pg)
                        <tr class="hover:bg-plum/5 transition-colors">
                            <td class="py-3 px-4">
                                <span class="font-bold text-plum block text-sm">{{ $pg->title }}</span>
                            </td>
                            <td class="py-3 px-4 text-plum/70 font-mono text-[11px]">
                                /pages/{{ $pg->slug }}
                            </td>
                            <td class="py-3 px-4 text-[11px] text-plum/60">
                                {{ $pg->updated_at->format('d M Y, h:i A') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $pg->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-700' }}">
                                    {{ $pg->is_active ? 'Published' : 'Hidden' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('pages.show', $pg->slug) }}" target="_blank" class="text-xs text-plum/60 hover:text-primary font-medium" title="View page">
                                        View
                                    </a>
                                    <a href="{{ route('admin.pages.edit', $pg->id) }}" class="px-3 py-1 rounded-lg bg-primary text-white font-bold hover:bg-primary-700 text-[11px]">
                                        Edit Content
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-plum/10">
            {{ $pages->links() }}
        </div>
    </div>
</div>
@endsection
