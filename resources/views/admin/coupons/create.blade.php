@extends('layouts.admin')

@section('title', 'Create Coupon - Bombay Fashion Admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <nav class="flex text-xs text-plum/60 mb-1">
            <a href="{{ route('admin.coupons.index') }}" class="hover:text-primary">&larr; Back to Coupons</a>
        </nav>
        <h1 class="text-2xl font-serif font-bold text-plum">Create Discount Coupon</h1>
        <p class="text-xs text-plum/70">Configure promo code terms, minimum cart values, and expiration dates.</p>
    </div>

    <form action="{{ route('admin.coupons.store') }}" method="POST" class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4 text-xs">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-plum mb-1">Coupon Code *</label>
                <input 
                    type="text" 
                    name="code" 
                    value="{{ old('code') }}" 
                    required 
                    placeholder="e.g. FESTIVE20"
                    class="w-full font-mono uppercase rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                >
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Discount Type *</label>
                <select name="discount_type" class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary">
                    <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage Discount (%)</option>
                    <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
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
                    value="{{ old('discount_value') }}" 
                    required 
                    min="0"
                    placeholder="e.g. 10 or 200"
                    class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary font-bold"
                >
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Min. Order Amount (₹) *</label>
                <input 
                    type="number" 
                    step="0.01" 
                    name="min_order_amount" 
                    value="{{ old('min_order_amount', 0) }}" 
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
                    value="{{ old('max_discount_amount') }}" 
                    min="0"
                    placeholder="Optional cap"
                    class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                >
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block font-semibold text-plum mb-1">Usage Limit (Total times)</label>
                <input 
                    type="number" 
                    name="usage_limit" 
                    value="{{ old('usage_limit') }}" 
                    min="1"
                    placeholder="e.g. 100"
                    class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                >
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Start Date</label>
                <input 
                    type="date" 
                    name="start_date" 
                    value="{{ old('start_date') }}" 
                    class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                >
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Expiry Date</label>
                <input 
                    type="date" 
                    name="end_date" 
                    value="{{ old('end_date') }}" 
                    class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary"
                >
            </div>
        </div>

        <div>
            <label class="block font-semibold text-plum mb-1">Description (Internal or customer notice)</label>
            <input 
                type="text" 
                name="description" 
                value="{{ old('description') }}" 
                placeholder="e.g. Diwali celebration 10% discount on sarees"
                class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
            >
        </div>

        <div class="pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="rounded text-primary focus:ring-primary">
                <span class="font-semibold text-plum">Coupon is Active</span>
            </label>
        </div>

        <div class="pt-4 flex justify-end gap-2 border-t border-plum/10">
            <a href="{{ route('admin.coupons.index') }}" class="px-5 py-2.5 rounded-xl border border-plum/20 text-plum hover:bg-plum/5">Cancel</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary text-white font-bold shadow hover:bg-primary-700">Save Coupon</button>
        </div>
    </form>
</div>
@endsection
