@extends('layouts.admin')

@section('title', 'Edit ' . $product->name . ' - Bombay Fashion Admin')

@section('content')
@php
    $uniform = $product->schoolUniform;
    $existingVariants = $product->allVariants->map(function($v) {
        return [
            'id' => $v->id,
            'size' => $v->size,
            'color' => $v->color,
            'color_code' => $v->color_code,
            'sku' => $v->sku,
            'stock' => $v->stock,
            'additional_price' => $v->additional_price,
        ];
    });
@endphp

<div 
    class="max-w-5xl mx-auto space-y-6"
    x-data="{
        isUniform: {{ old('is_uniform', $product->is_school_uniform) ? 'true' : 'false' }},
        variants: {{ $existingVariants->toJson() }},
        addVariant() {
            this.variants.push({ id: null, size: '', color: '', color_code: '#000000', sku: '', additional_price: 0, stock: 10 });
        },
        removeVariant(index) {
            this.variants.splice(index, 1);
        }
    }"
>
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <nav class="flex text-xs text-plum/60 mb-1">
                <a href="{{ route('admin.products.index') }}" class="hover:text-primary">&larr; Back to Products</a>
            </nav>
            <h1 class="text-2xl font-serif font-bold text-plum">Edit: {{ $product->name }}</h1>
            <p class="text-xs text-plum/70">Update inventory stock, Cloudinary images, variants, or pricing.</p>
        </div>
        <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="px-4 py-2 rounded-xl text-xs font-bold border border-primary text-primary hover:bg-primary-50">
            View Live on Site &rarr;
        </a>
    </div>

    {{-- Creator & Updater Attribution Banner --}}
    @if($product->creator_name)
        <div class="glass-card rounded-2xl p-3.5 border border-primary/20 bg-primary-50/60 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-plum">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                    {{ strtoupper(substr($product->creator_name, 0, 1)) }}
                </span>
                <div>
                    <div>
                        Uploaded by: <strong class="font-bold text-plum">{{ $product->creator_name }}</strong>
                        @if($product->creator_email)
                            <span class="text-plum/60">({{ $product->creator_email }})</span>
                        @endif
                    </div>
                    <div class="text-[10px] text-plum/50">
                        Date: {{ $product->created_at?->format('d M Y, h:i A') }}
                    </div>
                </div>
            </div>
            @if($product->updater_name)
                <div class="text-[11px] text-plum/70 self-end sm:self-auto bg-white/70 px-2.5 py-1 rounded-lg border border-plum/10">
                    Last edited by: <strong class="text-plum">{{ $product->updater_name }}</strong> ({{ $product->updated_at?->diffForHumans() }})
                </div>
            @endif
        </div>
    @endif

    {{-- Main Update Form --}}
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Section 1: Basic Information --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4">
            <h2 class="text-base font-serif font-bold text-plum pb-3 border-b border-plum/10">
                1. Basic Information
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-plum mb-1">Product Title *</label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name', $product->name) }}" 
                        required 
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Category *</label>
                    <select name="category_id" required class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-plum mb-1">SKU *</label>
                    <input 
                        type="text" 
                        name="sku" 
                        value="{{ old('sku', $product->sku) }}" 
                        required 
                        class="w-full font-mono uppercase rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Regular Price (₹) *</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="price" 
                        value="{{ old('price', $product->price) }}" 
                        required 
                        min="0"
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Discount Price (₹)</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="discount_price" 
                        value="{{ old('discount_price', $product->discount_price) }}" 
                        min="0"
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-plum mb-1">Base Stock Count *</label>
                    <input 
                        type="number" 
                        name="stock" 
                        value="{{ old('stock', $product->stock) }}" 
                        required 
                        min="0"
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Season *</label>
                    <select name="season" required class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary">
                        <option value="all_season" {{ old('season', $product->season) == 'all_season' ? 'selected' : '' }}>All Season</option>
                        <option value="winter" {{ old('season', $product->season) == 'winter' ? 'selected' : '' }}>Winter Wear</option>
                        <option value="summer" {{ old('season', $product->season) == 'summer' ? 'selected' : '' }}>Summer Wear</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Fabric</label>
                    <input 
                        type="text" 
                        name="fabric" 
                        value="{{ old('fabric', $product->fabric) }}" 
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>
            </div>

            <div class="text-xs">
                <label class="block font-semibold text-plum mb-1">Description</label>
                <textarea 
                    name="description" 
                    rows="4" 
                    class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                >{{ old('description', $product->description) }}</textarea>
            </div>
        </div>

        {{-- Section 2: Manage Existing Cloudinary Images & Upload New --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4">
            <h2 class="text-base font-serif font-bold text-plum pb-3 border-b border-plum/10">
                2. Product Images (Cloudinary CDN)
            </h2>

            @if($product->images->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3 mb-4">
                    @foreach($product->images as $img)
                        <div class="relative group rounded-xl overflow-hidden border border-plum/15 aspect-3/4 bg-plum/5">
                            <img src="{{ $img->image_url }}" alt="Product Image" class="w-full h-full object-cover">
                            
                            @if($img->is_primary)
                                <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-primary text-white">
                                    Primary
                                </span>
                            @endif

                            <div class="absolute inset-0 bg-plum/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-1.5 p-2">
                                @if(!$img->is_primary)
                                    <button 
                                        type="button" 
                                        onclick="document.getElementById('set-primary-{{ $img->id }}').submit()"
                                        class="px-2 py-1 rounded bg-white text-plum text-[10px] font-bold hover:bg-gold-100"
                                    >
                                        Set Primary
                                    </button>
                                @endif
                                @if(auth()->user()->isAdmin())
                                    <button 
                                        type="button" 
                                        onclick="if(confirm('Delete image from Cloudinary?')) document.getElementById('delete-img-{{ $img->id }}').submit()"
                                        class="px-2 py-1 rounded bg-rose-600 text-white text-[10px] font-bold hover:bg-rose-700"
                                    >
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="text-xs">
                <label class="block font-semibold text-plum mb-1.5">Upload Additional Photos</label>
                <input 
                    type="file" 
                    name="new_images[]" 
                    multiple 
                    accept="image/*"
                    class="w-full text-xs text-plum/70 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-700 cursor-pointer"
                >
            </div>
        </div>

        {{-- Section 3: School Uniform --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-plum/10">
                <div>
                    <h2 class="text-base font-serif font-bold text-plum">
                        3. School Uniform Details
                    </h2>
                    <p class="text-xs text-plum/60">Toggle if this product belongs to an Amritsar school dress code.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_uniform" value="1" x-model="isUniform" class="sr-only peer">
                    <div class="w-11 h-6 bg-plum/20 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>

            <div x-show="isUniform" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs pt-2">
                <div>
                    <label class="block font-semibold text-plum mb-1">School Name *</label>
                    <input 
                        type="text" 
                        name="school_name" 
                        value="{{ old('school_name', $uniform?->school_name) }}" 
                        placeholder="e.g. DAV Public School"
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Class / Grade *</label>
                    <input 
                        type="text" 
                        name="class_grade" 
                        value="{{ old('class_grade', $uniform?->class_grade) }}" 
                        placeholder="e.g. 6th to 10th"
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Gender *</label>
                    <select name="gender" class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary">
                        <option value="boys" {{ old('gender', $uniform?->gender) == 'boys' ? 'selected' : '' }}>Boys</option>
                        <option value="girls" {{ old('gender', $uniform?->gender) == 'girls' ? 'selected' : '' }}>Girls</option>
                        <option value="unisex" {{ old('gender', $uniform?->gender) == 'unisex' ? 'selected' : '' }}>Unisex</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Item Type *</label>
                    <input 
                        type="text" 
                        name="item_type" 
                        value="{{ old('item_type', $uniform?->item_type) }}" 
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>
            </div>
        </div>

        {{-- Section 4: Variants Repeater --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-plum/10">
                <div>
                    <h2 class="text-base font-serif font-bold text-plum">4. Size & Color Variants</h2>
                    <p class="text-xs text-plum/60">Configure per-size stock counts.</p>
                </div>
                <button type="button" @click="addVariant()" class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-primary text-white hover:bg-primary-700">
                    + Add Variant Row
                </button>
            </div>

            <template x-for="(variant, idx) in variants" :key="idx">
                <div class="p-4 rounded-2xl bg-plum/5 border border-plum/10 grid grid-cols-1 sm:grid-cols-6 gap-3 items-end text-xs">
                    <input type="hidden" :name="'variants[' + idx + '][id]'" x-model="variant.id">
                    <div>
                        <label class="block font-semibold text-plum mb-1">Size</label>
                        <input type="text" :name="'variants[' + idx + '][size]'" x-model="variant.size" class="w-full rounded-xl border-plum/20 bg-white p-2 text-plum">
                    </div>
                    <div>
                        <label class="block font-semibold text-plum mb-1">Color</label>
                        <input type="text" :name="'variants[' + idx + '][color]'" x-model="variant.color" class="w-full rounded-xl border-plum/20 bg-white p-2 text-plum">
                    </div>
                    <div>
                        <label class="block font-semibold text-plum mb-1">Variant SKU</label>
                        <input type="text" :name="'variants[' + idx + '][sku]'" x-model="variant.sku" class="w-full font-mono uppercase rounded-xl border-plum/20 bg-white p-2 text-plum text-[11px]">
                    </div>
                    <div>
                        <label class="block font-semibold text-plum mb-1">Stock</label>
                        <input type="number" :name="'variants[' + idx + '][stock]'" x-model="variant.stock" min="0" class="w-full rounded-xl border-plum/20 bg-white p-2 text-plum">
                    </div>
                    <div>
                        <label class="block font-semibold text-plum mb-1">Extra Price (+₹)</label>
                        <input type="number" step="0.01" :name="'variants[' + idx + '][additional_price]'" x-model="variant.additional_price" min="0" class="w-full rounded-xl border-plum/20 bg-white p-2 text-plum">
                    </div>
                    <div>
                        <button type="button" @click="removeVariant(idx)" class="w-full py-2 rounded-xl text-xs font-bold text-rose-600 border border-rose-200 hover:bg-rose-50">
                            Remove
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Section 5: Merchandising Flags --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4">
            <h2 class="text-base font-serif font-bold text-plum pb-3 border-b border-plum/10">
                5. Visibility
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary">
                    <span class="font-semibold text-plum">Active on Storefront</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary">
                    <span class="font-semibold text-plum">Featured Item</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_new_arrival" value="1" {{ old('is_new_arrival', $product->is_new_arrival) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary">
                    <span class="font-semibold text-plum">New Arrival</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_best_seller" value="1" {{ old('is_best_seller', $product->is_best_seller) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary">
                    <span class="font-semibold text-plum">Best Seller</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-3 rounded-xl text-xs font-semibold border border-plum/20 text-plum hover:bg-plum/5">
                Cancel
            </a>
            <button 
                type="submit" 
                class="px-8 py-3.5 rounded-xl font-bold text-xs bg-primary text-white shadow-lg shadow-primary/25 hover:bg-primary-700 transition-all"
            >
                Update Product Changes
            </button>
        </div>

    </form>

    {{-- Hidden form helpers for images --}}
    @foreach($product->images as $img)
        <form id="set-primary-{{ $img->id }}" action="{{ route('admin.products.setPrimaryImage', $img->id) }}" method="POST" class="hidden">
            @csrf
        </form>
        <form id="delete-img-{{ $img->id }}" action="{{ route('admin.products.deleteImage', $img->id) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach
</div>
@endsection
