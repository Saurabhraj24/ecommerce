@extends('layouts.admin')

@section('title', 'Add Category - Bombay Fashion Admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <nav class="flex text-xs text-plum/60 mb-1">
            <a href="{{ route('admin.categories.index') }}" class="hover:text-primary">&larr; Back to Categories</a>
        </nav>
        <h1 class="text-2xl font-serif font-bold text-plum">Create New Category</h1>
        <p class="text-xs text-plum/70">Create a department (Men, Women, Sarees, Uniforms) or nested subcategory.</p>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4 text-xs">
        @csrf

        <div>
            <label class="block font-semibold text-plum mb-1">Category Name *</label>
            <input 
                type="text" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                placeholder="e.g. Bridal Sarees or Boys Uniforms"
                class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
            >
        </div>

        <div>
            <label class="block font-semibold text-plum mb-1">Parent Category (Optional)</label>
            <select name="parent_id" class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary">
                <option value="">-- None (Top Level Root Category) --</option>
                @foreach($parentCategories as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                        {{ $parent->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-semibold text-plum mb-1">Category Tile Banner (Cloudinary Upload)</label>
            <input 
                type="file" 
                name="image" 
                accept="image/*"
                class="w-full text-plum/70 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-700 cursor-pointer"
            >
            <p class="text-[10px] text-plum/50 mt-1">Image will be used for the home page category card.</p>
        </div>

        <div>
            <label class="block font-semibold text-plum mb-1">Display Order</label>
            <input 
                type="number" 
                name="display_order" 
                value="{{ old('display_order', 0) }}" 
                min="0"
                class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
            >
        </div>

        <div>
            <label class="block font-semibold text-plum mb-1">Description (Optional)</label>
            <textarea 
                name="description" 
                rows="3" 
                class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                placeholder="Short overview of items in this category..."
            >{{ old('description') }}</textarea>
        </div>

        <div class="pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="rounded text-primary focus:ring-primary">
                <span class="font-semibold text-plum">Category Active & Visible to Shoppers</span>
            </label>
        </div>

        <div class="pt-4 flex justify-end gap-2 border-t border-plum/10">
            <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 rounded-xl border border-plum/20 text-plum hover:bg-plum/5">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary text-white font-bold shadow hover:bg-primary-700">Save Category</button>
        </div>
    </form>
</div>
@endsection
