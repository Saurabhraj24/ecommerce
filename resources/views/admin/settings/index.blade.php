@extends('layouts.admin')

@section('title', 'Store Settings - Bombay Fashion Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-serif font-bold text-plum">Store & Gateway Configuration</h1>
        <p class="text-xs text-plum/70 mt-1">Configure shop timings, physical Amritsar address, WhatsApp number, Razorpay, COD, and GST tax.</p>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Section 1: Store Profile & Address --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4 text-xs">
            <h2 class="text-base font-serif font-bold text-plum pb-3 border-b border-plum/10">
                1. Store Profile & Physical Amritsar Location
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-plum mb-1">Store Name *</label>
                    <input 
                        type="text" 
                        name="store_name" 
                        value="{{ old('store_name', $settings['store_name']) }}" 
                        required 
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary font-bold"
                    >
                </div>
                <div>
                    <label class="block font-semibold text-plum mb-1">Tagline</label>
                    <input 
                        type="text" 
                        name="store_tagline" 
                        value="{{ old('store_tagline', $settings['store_tagline']) }}" 
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Store Address *</label>
                <input 
                    type="text" 
                    name="store_address" 
                    value="{{ old('store_address', $settings['store_address']) }}" 
                    required 
                    class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                >
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-plum mb-1">Phone Number *</label>
                    <input 
                        type="text" 
                        name="store_phone" 
                        value="{{ old('store_phone', $settings['store_phone']) }}" 
                        required 
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>
                <div>
                    <label class="block font-semibold text-plum mb-1">WhatsApp Number *</label>
                    <input 
                        type="text" 
                        name="store_whatsapp" 
                        value="{{ old('store_whatsapp', $settings['store_whatsapp']) }}" 
                        required 
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>
                <div>
                    <label class="block font-semibold text-plum mb-1">Customer Support Email *</label>
                    <input 
                        type="email" 
                        name="store_email" 
                        value="{{ old('store_email', $settings['store_email']) }}" 
                        required 
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Opening Hours / Timings *</label>
                <input 
                    type="text" 
                    name="store_timings" 
                    value="{{ old('store_timings', $settings['store_timings']) }}" 
                    required 
                    placeholder="Open daily, closes 9:30 PM"
                    class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary font-bold"
                >
            </div>

            <div>
                <label class="block font-semibold text-plum mb-1">Google Maps Embed URL</label>
                <textarea 
                    name="store_map_embed" 
                    rows="2" 
                    class="w-full rounded-xl border-plum/20 bg-white p-2.5 text-plum focus:ring-primary font-mono text-[11px]"
                >{{ old('store_map_embed', $settings['store_map_embed']) }}</textarea>
            </div>
        </div>

        {{-- Section 2: Payments (Razorpay & COD) --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4 text-xs">
            <h2 class="text-base font-serif font-bold text-plum pb-3 border-b border-plum/10">
                2. Payment Gateway & COD Limits
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="p-4 rounded-2xl bg-plum/5 border border-plum/10 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer font-bold text-plum text-sm">
                        <input type="checkbox" name="razorpay_enabled" value="1" {{ old('razorpay_enabled', $settings['razorpay_enabled']) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary">
                        <span>Enable Razorpay Online Payments</span>
                    </label>
                    <p class="text-plum/60 text-[11px]">
                        Allows UPI (Google Pay, PhonePe, Paytm), Debit/Credit Cards, and Netbanking.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-plum/5 border border-plum/10 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer font-bold text-plum text-sm">
                        <input type="checkbox" name="cod_enabled" value="1" {{ old('cod_enabled', $settings['cod_enabled']) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary">
                        <span>Enable Cash on Delivery (COD)</span>
                    </label>
                    <p class="text-plum/60 text-[11px]">
                        Allows customers to pay in cash upon doorstep delivery in Amritsar.
                    </p>
                </div>
            </div>

            <div class="max-w-xs">
                <label class="block font-semibold text-plum mb-1">Maximum COD Order Limit (₹) *</label>
                <input 
                    type="number" 
                    step="0.01" 
                    name="cod_max_limit" 
                    value="{{ old('cod_max_limit', $settings['cod_max_limit']) }}" 
                    required 
                    min="0"
                    class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary font-bold"
                >
                <p class="text-[10px] text-plum/50 mt-1">Orders above this amount must pay online via Razorpay.</p>
            </div>
        </div>

        {{-- Section 3: Taxes & Shipping --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-sm space-y-4 text-xs">
            <h2 class="text-base font-serif font-bold text-plum pb-3 border-b border-plum/10">
                3. Taxes (GST) & Shipping Charges
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-plum mb-1">Tax Name *</label>
                    <input 
                        type="text" 
                        name="tax_name" 
                        value="{{ old('tax_name', $settings['tax_name']) }}" 
                        required 
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>
                <div>
                    <label class="block font-semibold text-plum mb-1">Tax Rate (%) *</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="tax_rate" 
                        value="{{ old('tax_rate', $settings['tax_rate']) }}" 
                        required 
                        min="0" 
                        max="100"
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary font-bold"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-plum mb-1">Flat Shipping Charge (₹) *</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="shipping_charge" 
                        value="{{ old('shipping_charge', $settings['shipping_charge']) }}" 
                        required 
                        min="0"
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary"
                    >
                </div>
                <div>
                    <label class="block font-semibold text-plum mb-1">Free Delivery Minimum Order (₹) *</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="free_shipping_threshold" 
                        value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold']) }}" 
                        required 
                        min="0"
                        class="w-full rounded-xl border-plum/20 bg-white p-3 text-plum focus:ring-primary font-bold"
                    >
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <button 
                type="submit" 
                class="px-8 py-3.5 rounded-xl font-bold text-xs bg-primary text-white shadow-lg shadow-primary/25 hover:bg-primary-700 transition-all"
            >
                Save All Store Settings
            </button>
        </div>
    </form>
</div>
@endsection
