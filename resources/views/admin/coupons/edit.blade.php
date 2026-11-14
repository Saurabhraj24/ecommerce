@extends('layouts.admin')

@section('title', 'Edit Coupon - Bombay Fashion Admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <nav class="flex text-xs text-plum/60 mb-1">
            <a href="{{ route('admin.coupons.index') }}" class="hover:text-primary">&larr; Back to Coupons</a>
        </nav>
        <h1 class="text-2xl font-serif font-bold text-plum">Edit: {{ $coupon->code }}</h1>
        <p class="text-xs text-plum/70">Modify coupon limits, discount rates, or toggle active status.</p>
    </div>

    <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST" class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4 text-xs">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-plum mb-1">Coupon Code *</label>
                <input 
                    type="text" 
                    name="code" 
                    value="{{ old('code', $coupon->code) }}" 
                    required 
                    class="w-full font-mono uppercase rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                >
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Discount Type *</label>
                <select name="discount_type" class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary">
                    <option value="percentage" {{ old('discount_type', $coupon->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage Discount (%)</option>
                    <option value="fixed" {{ old('discount_type', $coupon->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-semibold text-plum mb-1">Discount Value *</label>
                <input 
                    type="number" 
                    step="0.01" 
                    name="discount_value" 
                    value="{{ old('discount_value', $coupon->discount_value) }}" 
                    required 
                    min="0"
                    class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary font-bold"
                >
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Min. Order Amount (₹) *</label>
                <input 
                    type="number" 
                    step="0.01" 
                    name="min_order_amount" 
                    value="{{ old('min_order_amount', $coupon->min_order_amount) }}" 
                    required 
                    min="0"
                    class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                >
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Max Discount Cap (₹)</label>
                <input 
                    type="number" 
                    step="0.01" 
                    name="max_discount_amount" 
                    value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}" 
                    min="0"
                    class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-semibold text-plum mb-1">Usage Limit</label>
                <input 
                    type="number" 
                    name="usage_limit" 
                    value="{{ old('usage_limit', $coupon->usage_limit) }}" 
                    min="1"
                    class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                >
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Start Date</label>
                <input 
                    type="date" 
                    name="start_date" 
                    value="{{ old('start_date', $coupon->start_date?->format('Y-m-d')) }}" 
                    class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                >
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Expiry Date</label>
                <input 
                    type="date" 
                    name="end_date" 
                    value="{{ old('end_date', $coupon->end_date?->format('Y-m-d')) }}" 
                    class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                >
            </div>
        </div>

        <div>
            <label class="block font-semibold text-plum mb-1">Description</label>
            <input 
                type="text" 
                name="description" 
                value="{{ old('description', $coupon->description) }}" 
                class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
            >
        </div>

        <div class="pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary">
                <span class="font-semibold text-plum">Coupon is Active</span>
            </label>
        </div>

        <div class="pt-4 flex justify-end gap-2 border-t border-plum/10">
            <a href="{{ route('admin.coupons.index') }}" class="px-5 py-2.5 rounded-xl border border-plum/20 text-plum hover:bg-plum/5">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary text-white font-bold shadow hover:bg-primary-700">Update Coupon</button>
        </div>
    </form>
</div>
@endsection
