@extends('layouts.admin')

@section('title', 'Add New Product - Bombay Fashion Admin')

@section('content')
<div 
    class="max-w-5xl mx-auto space-y-6"
    x-data="{
        isUniform: {{ old('is_uniform') ? 'true' : 'false' }},
        variants: [],
        addVariant() {
            this.variants.push({ size: '', color: '', color_code: '#000000', sku: '', additional_price: 0, stock: 10 });
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
            <h1 class="text-2xl font-serif font-bold text-plum">Create New Product</h1>
            <p class="text-xs text-plum/70">Upload images to Cloudinary, configure pricing, variants, and school uniforms.</p>
        </div>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Section 1: Basic Information --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4">
            <h2 class="text-base font-serif font-bold text-plum pb-3 border-b border-plum/10">
                1. Basic Product Information
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-plum mb-1">Product Title *</label>
                    <input 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        placeholder="e.g. Pure Banarasi Silk Bridal Saree"
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Category *</label>
                    <select name="category_id" required class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary">
                        <option value="">-- Choose Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-plum mb-1">SKU (Unique Code) *</label>
                    <input 
                        type="text" 
                        name="sku" 
                        value="{{ old('sku', 'BF-' . strtoupper(Str::random(6))) }}" 
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
                        value="{{ old('price') }}" 
                        required 
                        min="0"
                        placeholder="0.00"
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Discount Price (₹, optional)</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="discount_price" 
                        value="{{ old('discount_price') }}" 
                        min="0"
                        placeholder="Leave empty if none"
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-semibold text-plum mb-1">Initial Stock Count *</label>
                    <input 
                        type="number" 
                        name="stock" 
                        value="{{ old('stock', 10) }}" 
                        required 
                        min="0"
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Season Wear *</label>
                    <select name="season" required class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary">
                        <option value="all_season" {{ old('season') == 'all_season' ? 'selected' : '' }}>All Season</option>
                        <option value="winter" {{ old('season') == 'winter' ? 'selected' : '' }}>Winter Wear</option>
                        <option value="summer" {{ old('season') == 'summer' ? 'selected' : '' }}>Summer Wear</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Fabric (e.g. Georgette, Silk, Cotton)</label>
                    <input 
                        type="text" 
                        name="fabric" 
                        value="{{ old('fabric') }}" 
                        placeholder="e.g. Chanderi Silk"
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>
            </div>

            <div class="text-xs">
                <label class="block font-semibold text-plum mb-1">Short Summary (Optional)</label>
                <input 
                    type="text" 
                    name="short_description" 
                    value="{{ old('short_description') }}" 
                    placeholder="Brief 1-line highlight"
                    class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                >
            </div>

            <div class="text-xs">
                <label class="block font-semibold text-plum mb-1">Full Description & Details</label>
                <textarea 
                    name="description" 
                    rows="4" 
                    placeholder="Describe weave, zari work, embroidery, care instructions, fitting details..."
                    class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                >{{ old('description') }}</textarea>
            </div>
        </div>

        {{-- Section 2: Cloudinary Image Uploads --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4">
            <h2 class="text-base font-serif font-bold text-plum pb-3 border-b border-plum/10">
                2. Product Images (Cloudinary CDN Storage)
            </h2>

            <div class="text-xs">
                <label class="block font-semibold text-plum mb-1.5">Select Photos (First photo is primary)</label>
                <input 
                    type="file" 
                    name="images[]" 
                    multiple 
                    accept="image/*"
                    class="w-full text-xs text-plum/70 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-700 cursor-pointer"
                >
                <p class="text-[11px] text-plum/50 mt-1.5">
                    Images are automatically optimized and uploaded directly to Cloudinary (or local storage fallback). Supported formats: JPG, PNG, WEBP.
                </p>
            </div>
        </div>

        {{-- Section 3: School Uniform Option --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-plum/10">
                <div>
                    <h2 class="text-base font-serif font-bold text-plum">
                        3. Is this an Amritsar School Uniform?
                    </h2>
                    <p class="text-xs text-plum/60">Enable if this item belongs to a specific school's dress code.</p>
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
                        value="{{ old('school_name') }}" 
                        placeholder="e.g. Sacred Heart Senior Secondary School"
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Class / Grade *</label>
                    <input 
                        type="text" 
                        name="class_grade" 
                        value="{{ old('class_grade') }}" 
                        placeholder="e.g. 1st to 5th"
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Gender *</label>
                    <select name="gender" class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary">
                        <option value="boys">Boys</option>
                        <option value="girls">Girls</option>
                        <option value="unisex">Unisex</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-plum mb-1">Uniform Item Type *</label>
                    <input 
                        type="text" 
                        name="item_type" 
                        value="{{ old('item_type') }}" 
                        placeholder="e.g. Shirt, Skirt, Blazer, Tie"
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>
            </div>
        </div>

        {{-- Section 4: Variants Repeater (Size / Color) --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-plum/10">
                <div>
                    <h2 class="text-base font-serif font-bold text-plum">
                        4. Size & Color Variants (Optional)
                    </h2>
                    <p class="text-xs text-plum/60">Add multiple sizes (S, M, L, XL, 38, 40) or colors with separate stock levels.</p>
                </div>
                <button 
                    type="button" 
                    @click="addVariant()"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold bg-primary text-white hover:bg-primary-700 shadow-sm transition-all"
                >
                    + Add Variant Row
                </button>
            </div>

            <div x-show="variants.length === 0" class="py-4 text-center text-xs text-plum/50 italic">
                No custom variants added. Standard product stock count will be used.
            </div>

            <template x-for="(variant, idx) in variants" :key="idx">
                <div class="p-4 rounded-2xl bg-plum/5 border border-plum/10 grid grid-cols-1 sm:grid-cols-6 gap-3 items-end text-xs">
                    <div>
                        <label class="block font-semibold text-plum mb-1">Size</label>
                        <input type="text" :name="'variants[' + idx + '][size]'" x-model="variant.size" placeholder="e.g. 38 or XL" class="w-full rounded-xl border-plum/20 bg-white p-2 text-plum">
                    </div>
                    <div>
                        <label class="block font-semibold text-plum mb-1">Color</label>
                        <input type="text" :name="'variants[' + idx + '][color]'" x-model="variant.color" placeholder="e.g. Royal Blue" class="w-full rounded-xl border-plum/20 bg-white p-2 text-plum">
                    </div>
                    <div>
                        <label class="block font-semibold text-plum mb-1">Variant SKU</label>
                        <input type="text" :name="'variants[' + idx + '][sku]'" x-model="variant.sku" placeholder="Auto-generated if empty" class="w-full font-mono uppercase rounded-xl border-plum/20 bg-white p-2 text-plum text-[11px]">
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
                        <button type="button" @click="removeVariant(idx)" class="w-full py-2 rounded-xl text-xs font-bold text-rose-600 border border-rose-200 hover:bg-rose-50 transition-colors">
                            Remove
                        </button>
                    </div>
                </div>
            </template>
        </div>

        {{-- Section 5: Visibility & Merchandising Flags --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4">
            <h2 class="text-base font-serif font-bold text-plum pb-3 border-b border-plum/10">
                5. Visibility & Badges
            </h2>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-primary focus:ring-primary">
                    <span class="font-semibold text-plum">Active on Storefront</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" class="rounded text-primary focus:ring-primary">
                    <span class="font-semibold text-plum">Featured Item</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_new_arrival" value="1" checked class="rounded text-primary focus:ring-primary">
                    <span class="font-semibold text-plum">New Arrival Badge</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_best_seller" value="1" class="rounded text-primary focus:ring-primary">
                    <span class="font-semibold text-plum">Best Seller Badge</span>
                </label>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-3 rounded-xl text-xs font-semibold border border-plum/20 text-plum hover:bg-plum/5">
                Cancel
            </a>
            <button 
                type="submit" 
                class="px-8 py-3.5 rounded-xl font-bold text-xs bg-primary text-white shadow-lg shadow-primary/25 hover:bg-primary-700 transition-all"
            >
                Publish Product to Catalog
            </button>
        </div>

    </form>
</div>
@endsection
