@extends('layouts.admin')

@section('title', 'Edit Banner - Bombay Fashion Admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <nav class="flex text-xs text-plum/60 mb-1">
            <a href="{{ route('admin.banners.index') }}" class="hover:text-primary">&larr; Back to Banners</a>
        </nav>
        <h1 class="text-2xl font-serif font-bold text-plum">Edit: {{ $banner->title }}</h1>
        <p class="text-xs text-plum/70">Update banner details or upload a new photo to Cloudinary.</p>
    </div>

    <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4 text-xs">
        @csrf
        @method('PUT')

        <div>
            <label class="block font-semibold text-plum mb-1">Banner Title *</label>
            <input 
                type="text" 
                name="title" 
                value="{{ old('title', $banner->title) }}" 
                required 
                class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
            >
        </div>

        <div>
            <label class="block font-semibold text-plum mb-1">Subtitle / Tagline</label>
            <input 
                type="text" 
                name="subtitle" 
                value="{{ old('subtitle', $banner->subtitle) }}" 
                class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
            >
        </div>

        @if($banner->image_url)
            <div>
                <label class="block font-semibold text-plum mb-1">Current Banner Image</label>
                <div class="w-48 h-24 rounded-xl overflow-hidden border border-plum/15">
                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                </div>
            </div>
        @endif

        <div>
            <label class="block font-semibold text-plum mb-1">Upload New Image (Optional)</label>
            <input 
                type="file" 
                name="image" 
                accept="image/*"
                class="w-full text-plum/70 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-700 cursor-pointer"
            >
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-plum mb-1">Target Link URL</label>
                <input 
                    type="text" 
                    name="link_url" 
                    value="{{ old('link_url', $banner->link_url) }}" 
                    class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                >
            </div>
            <div>
                <label class="block font-semibold text-plum mb-1">Button Text</label>
                <input 
                    type="text" 
                    name="button_text" 
                    value="{{ old('button_text', $banner->button_text) }}" 
                    class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-plum mb-1">Banner Position *</label>
                <select name="position" class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary">
                    <option value="hero" {{ old('position', $banner->position) == 'hero' ? 'selected' : '' }}>Hero Slider (Top)</option>
                    <option value="middle" {{ old('position', $banner->position) == 'middle' ? 'selected' : '' }}>Middle Promo Section</option>
                    <option value="popup" {{ old('position', $banner->position) == 'popup' ? 'selected' : '' }}>Notice Popup</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold text-plum mb-1">Display Order</label>
                <input 
                    type="number" 
                    name="display_order" 
                    value="{{ old('display_order', $banner->display_order) }}" 
                    min="0"
                    class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                >
            </div>
        </div>

        <div class="pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary">
                <span class="font-semibold text-plum">Active Banner</span>
            </label>
        </div>

        <div class="pt-4 flex justify-end gap-2 border-t border-plum/10">
            <a href="{{ route('admin.banners.index') }}" class="px-5 py-2.5 rounded-xl border border-plum/20 text-plum hover:bg-plum/5">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary text-white font-bold shadow hover:bg-primary-700">Update Banner</button>
        </div>
    </form>
</div>
@endsection
