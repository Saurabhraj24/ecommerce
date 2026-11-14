@extends('layouts.admin')

@section('title', 'Edit Page: ' . $page->title . ' - Bombay Fashion Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <nav class="flex text-xs text-plum/60 mb-1">
            <a href="{{ route('admin.pages.index') }}" class="hover:text-primary">&larr; Back to Pages</a>
        </nav>
        <h1 class="text-2xl font-serif font-bold text-plum">Edit: {{ $page->title }}</h1>
        <p class="text-xs text-plum/70">Update page text, policies, or meta tags.</p>
    </div>

    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4 text-xs">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-plum mb-1">Page Title *</label>
                <input 
                    type="text" 
                    name="title" 
                    value="{{ old('title', $page->title) }}" 
                    required 
                    class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary font-bold"
                >
            </div>
            <div>
                <label class="block font-semibold text-plum mb-1">URL Slug</label>
                <input 
                    type="text" 
                    value="{{ $page->slug }}" 
                    disabled 
                    class="w-full rounded-xl border-plum/10 bg-plum/5 p-3 text-plum/60 font-mono cursor-not-allowed"
                >
            </div>
        </div>

        <div>
            <label class="block font-semibold text-plum mb-1">Page Content (HTML / Text) *</label>
            <textarea 
                name="content" 
                rows="14" 
                required 
                class="w-full rounded-xl border-plum/20 bg-white p-4 text-plum focus:ring-primary font-mono leading-relaxed"
            >{{ old('content', $page->content) }}</textarea>
            <p class="text-[10px] text-plum/50 mt-1">HTML tags & formatting (paragraphs, headings, lists) are rendered directly on the customer storefront.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-plum mb-1">Meta Title (SEO)</label>
                <input 
                    type="text" 
                    name="meta_title" 
                    value="{{ old('meta_title', $page->meta_title) }}" 
                    class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                >
            </div>
            <div>
                <label class="block font-semibold text-plum mb-1">Meta Description (SEO)</label>
                <input 
                    type="text" 
                    name="meta_description" 
                    value="{{ old('meta_description', $page->meta_description) }}" 
                    class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                >
            </div>
        </div>

        <div class="pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary">
                <span class="font-semibold text-plum">Page is Published & Public</span>
            </label>
        </div>

        <div class="pt-4 flex justify-end gap-2 border-t border-plum/10">
            <a href="{{ route('admin.pages.index') }}" class="px-5 py-2.5 rounded-xl border border-plum/20 text-plum hover:bg-plum/5">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary text-white font-bold shadow hover:bg-primary-700">Save Page Changes</button>
        </div>
    </form>
</div>
@endsection
