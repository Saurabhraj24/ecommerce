{{-- Categories --}}
<div>
    <h3 class="text-xs font-bold uppercase tracking-wider text-festive-plum/70 mb-3">Categories</h3>
    <div class="space-y-1.5 max-h-52 overflow-y-auto pr-1">
        @forelse($categories as $cat)
            <label class="flex items-center gap-2 text-xs text-festive-plum hover:text-festive-rose cursor-pointer py-0.5">
                <input 
                    type="radio" 
                    name="category" 
                    value="{{ $cat->slug }}" 
                    {{ request('category') == $cat->slug ? 'checked' : '' }}
                    onchange="this.form.submit()"
                    class="rounded text-festive-rose focus:ring-festive-rose/50"
                >
                <span class="flex-1 font-medium">{{ $cat->name }}</span>
                @if($cat->products_count)
                    <span class="text-[11px] text-festive-plum/40">({{ $cat->products_count }})</span>
                @endif
            </label>
            {{-- Subcategories if category selected --}}
            @if(request('category') == $cat->slug && $cat->children->isNotEmpty())
                <div class="pl-4 space-y-1 border-l-2 border-festive-rose/20 my-1">
                    @foreach($cat->children as $sub)
                        <label class="flex items-center gap-2 text-[11px] text-festive-plum/80 hover:text-festive-rose cursor-pointer">
                            <input 
                                type="radio" 
                                name="subcategory" 
                                value="{{ $sub->slug }}" 
                                {{ request('subcategory') == $sub->slug ? 'checked' : '' }}
                                onchange="this.form.submit()"
                                class="rounded text-festive-rose focus:ring-festive-rose/50"
                            >
                            <span>{{ $sub->name }}</span>
                        </label>
                    @endforeach
                </div>
            @endif
        @empty
            <p class="text-xs text-festive-plum/50 italic">No categories available</p>
        @endforelse
    </div>
</div>

{{-- Season Filter --}}
<div class="border-t border-festive-plum/10 pt-4">
    <h3 class="text-xs font-bold uppercase tracking-wider text-festive-plum/70 mb-3">Season</h3>
    <div class="flex flex-wrap gap-2">
        @foreach(['winter' => 'Winter Wear', 'summer' => 'Summer Wear', 'all' => 'All Season'] as $val => $label)
            <label class="cursor-pointer">
                <input 
                    type="radio" 
                    name="season" 
                    value="{{ $val }}" 
                    {{ request('season') == $val ? 'checked' : '' }}
                    onchange="this.form.submit()"
                    class="sr-only peer"
                >
                <span class="inline-block px-3 py-1 rounded-xl text-xs font-medium border border-festive-plum/15 peer-checked:bg-festive-rose peer-checked:text-white peer-checked:border-festive-rose transition-all">
                    {{ $label }}
                </span>
            </label>
        @endforeach
    </div>
</div>

{{-- Price Range --}}
<div class="border-t border-festive-plum/10 pt-4">
    <h3 class="text-xs font-bold uppercase tracking-wider text-festive-plum/70 mb-3">Price Range (₹)</h3>
    <div class="grid grid-cols-2 gap-2">
        <div>
            <input 
                type="number" 
                name="min_price" 
                placeholder="Min" 
                value="{{ request('min_price') }}"
                min="0"
                class="w-full text-xs rounded-lg border-festive-plum/15 bg-white/60 p-2 text-festive-plum focus:ring-festive-rose"
            >
        </div>
        <div>
            <input 
                type="number" 
                name="max_price" 
                placeholder="Max" 
                value="{{ request('max_price') }}"
                min="0"
                class="w-full text-xs rounded-lg border-festive-plum/15 bg-white/60 p-2 text-festive-plum focus:ring-festive-rose"
            >
        </div>
    </div>
</div>

{{-- Sizes --}}
@if(!empty($availableSizes))
    <div class="border-t border-festive-plum/10 pt-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-festive-plum/70 mb-3">Available Sizes</h3>
        <div class="flex flex-wrap gap-1.5">
            @foreach($availableSizes as $sz)
                <label class="cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="size[]" 
                        value="{{ $sz }}"
                        {{ is_array(request('size')) && in_array($sz, request('size')) ? 'checked' : '' }}
                        onchange="this.form.submit()"
                        class="sr-only peer"
                    >
                    <span class="inline-flex items-center justify-center min-w-[2.2rem] h-8 px-2 rounded-lg text-xs font-semibold border border-festive-plum/15 peer-checked:bg-festive-rose peer-checked:text-white peer-checked:border-festive-rose transition-all">
                        {{ $sz }}
                    </span>
                </label>
            @endforeach
        </div>
    </div>
@endif

{{-- Availability & Discount --}}
<div class="border-t border-festive-plum/10 pt-4 space-y-2">
    <label class="flex items-center gap-2 text-xs text-festive-plum cursor-pointer">
        <input 
            type="checkbox" 
            name="in_stock" 
            value="1" 
            {{ request('in_stock') ? 'checked' : '' }}
            onchange="this.form.submit()"
            class="rounded text-festive-rose focus:ring-festive-rose/50"
        >
        <span>In Stock Only</span>
    </label>

    <label class="flex items-center gap-2 text-xs text-festive-plum cursor-pointer">
        <input 
            type="checkbox" 
            name="discount" 
            value="1" 
            {{ request('discount') ? 'checked' : '' }}
            onchange="this.form.submit()"
            class="rounded text-festive-rose focus:ring-festive-rose/50"
        >
        <span>Discounted Items</span>
    </label>
</div>

<button 
    type="submit" 
    class="w-full py-2.5 rounded-xl bg-festive-rose text-white text-xs font-bold shadow-md hover:bg-festive-rose/90 transition-all cursor-pointer"
>
    Apply Filters
</button>
