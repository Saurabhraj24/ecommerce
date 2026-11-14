@extends('layouts.app')

@section('title', 'Secure Checkout - Bombay Fashion Amritsar')

@section('content')
<div 
    class="py-10 bg-festive-ivory/50"
    x-data="{
        useExistingAddress: {{ $addresses->isNotEmpty() ? 'true' : 'false' }},
        selectedAddressId: {{ $defaultAddress?->id ?? 'null' }},
        paymentMethod: '{{ $razorpayAllowed ? 'razorpay' : 'cod' }}',
        billingSameAsShipping: true,
        orderTotal: {{ (float)$summary['total'] }},
        codLimit: {{ (float)$codLimit }},
        codAllowed: {{ $codAllowed ? 'true' : 'false' }},

        isSubmitting: false,
        get isCodEligible() {
            return this.codAllowed && this.orderTotal <= this.codLimit;
        }
    }"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-serif font-bold text-festive-plum">Secure Checkout</h1>
            <p class="text-xs text-festive-plum/70 mt-1">Direct from Bombay Fashion, Mahila College Road, Amritsar.</p>
        </div>

        <form 
            action="{{ route('checkout.process') }}" 
            method="POST" 
            id="checkoutForm"
            @submit="if(isSubmitting) { $event.preventDefault(); return false; } isSubmitting = true;"
        >
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- Left: Customer Details & Shipping (7 cols) --}}
                <div class="lg:col-span-7 space-y-6">
                    
                    {{-- Guest Notice if unauthenticated --}}
                    @guest
                        <div class="glass-card rounded-2xl p-4 border border-white/80 bg-white/80 shadow-sm flex items-center justify-between">
                            <div class="text-xs text-festive-plum">
                                <span class="font-bold">Already have a Bombay Fashion account?</span>
                                <p class="text-festive-plum/60">Log in for saved addresses and loyalty rewards.</p>
                            </div>
                            <a href="{{ route('login') }}" class="px-3.5 py-1.5 rounded-xl border border-festive-rose text-festive-rose text-xs font-bold hover:bg-festive-blush transition-all">
                                Log In
                            </a>
                        </div>
                    @endguest

                    {{-- Shipping Address Card --}}
                    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/80 shadow-md">
                        <div class="flex items-center justify-between pb-4 border-b border-festive-plum/10 mb-6">
                            <h2 class="text-lg font-serif font-bold text-festive-plum flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-festive-rose text-white text-xs flex items-center justify-center font-sans">1</span>
                                Delivery Address
                            </h2>
                            @auth
                                @if($addresses->isNotEmpty())
                                    <button 
                                        type="button" 
                                        @click="useExistingAddress = !useExistingAddress" 
                                        class="text-xs text-festive-rose hover:underline font-bold"
                                        x-text="useExistingAddress ? '+ Add New Address' : 'Choose Saved Address'"
                                    ></button>
                                @endif
                            @endauth
                        </div>

                        {{-- Saved Addresses --}}
                        @auth
                            @if($addresses->isNotEmpty())
                                <div x-show="useExistingAddress" class="space-y-3 mb-6">
                                    <p class="text-xs text-festive-plum/70 font-medium">Select from your saved addresses:</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($addresses as $addr)
                                            <label 
                                                class="border rounded-2xl p-4 cursor-pointer transition-all flex flex-col justify-between"
                                                :class="selectedAddressId == {{ $addr->id }} ? 'border-festive-rose bg-festive-rose/5 ring-1 ring-festive-rose' : 'border-festive-plum/15 bg-white/60 hover:border-festive-rose/50'"
                                            >
                                                <div class="flex items-start justify-between">
                                                    <div class="flex items-center gap-2">
                                                        <input 
                                                            type="radio" 
                                                            name="shipping_address_id" 
                                                            value="{{ $addr->id }}" 
                                                            x-model="selectedAddressId"
                                                            class="text-festive-rose focus:ring-festive-rose"
                                                        >
                                                        <span class="text-xs font-bold text-festive-plum">{{ $addr->name }}</span>
                                                    </div>
                                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-festive-blush text-festive-rose">
                                                        {{ $addr->type }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-festive-plum/70 mt-2 leading-relaxed">
                                                    {{ $addr->address_line1 }}
                                                    @if($addr->address_line2), {{ $addr->address_line2 }}@endif
                                                    <br>{{ $addr->city }}, {{ $addr->state }} - {{ $addr->pincode }}
                                                    <br><span class="font-medium text-festive-plum">Phone: {{ $addr->phone }}</span>
                                                </p>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endauth

                        {{-- New Address Inputs --}}
                        <div x-show="!useExistingAddress || {{ $addresses->isEmpty() ? 'true' : 'false' }}" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-festive-plum mb-1">Full Recipient Name *</label>
                                    <input 
                                        type="text" 
                                        name="name" 
                                        value="{{ old('name', Auth::user()?->name) }}" 
                                        :required="!useExistingAddress"
                                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                                        placeholder="e.g. Harpreet Singh"
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-festive-plum mb-1">Phone Number *</label>
                                    <input 
                                        type="tel" 
                                        name="phone" 
                                        value="{{ old('phone', Auth::user()?->phone) }}" 
                                        :required="!useExistingAddress"
                                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                                        placeholder="10-digit mobile number"
                                    >
                                </div>
                            </div>

                            @guest
                                <div>
                                    <label class="block text-xs font-semibold text-festive-plum mb-1">Email Address (for order invoice & updates) *</label>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        value="{{ old('email') }}" 
                                        required
                                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                                        placeholder="name@example.com"
                                    >
                                </div>
                            @endguest

                            <div>
                                <label class="block text-xs font-semibold text-festive-plum mb-1">Flat / House No. / Building / Street *</label>
                                <input 
                                    type="text" 
                                    name="address_line1" 
                                    value="{{ old('address_line1') }}" 
                                    :required="!useExistingAddress"
                                    class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                                    placeholder="Street or house details"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-festive-plum mb-1">Area / Landmark (optional)</label>
                                <input 
                                    type="text" 
                                    name="address_line2" 
                                    value="{{ old('address_line2') }}" 
                                    class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                                    placeholder="Near Golden Temple, Mall Road, etc."
                                >
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-festive-plum mb-1">PIN Code *</label>
                                    <input 
                                        type="text" 
                                        name="pincode" 
                                        value="{{ old('pincode', '143001') }}" 
                                        :required="!useExistingAddress"
                                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose font-mono"
                                        placeholder="143001"
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-festive-plum mb-1">City *</label>
                                    <input 
                                        type="text" 
                                        name="city" 
                                        value="{{ old('city', 'Amritsar') }}" 
                                        :required="!useExistingAddress"
                                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                                    >
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-festive-plum mb-1">State *</label>
                                    <input 
                                        type="text" 
                                        name="state" 
                                        value="{{ old('state', 'Punjab') }}" 
                                        :required="!useExistingAddress"
                                        class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                                    >
                                </div>
                            </div>

                            @auth
                                <div class="pt-2">
                                    <label class="flex items-center gap-2 text-xs text-festive-plum cursor-pointer">
                                        <input type="checkbox" name="save_address" value="1" checked class="rounded text-festive-rose focus:ring-festive-rose">
                                        <span>Save this address to my profile for future orders</span>
                                    </label>
                                </div>
                            @endauth
                        </div>
                    </div>

                    {{-- Payment Method Selection Card --}}
                    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/80 shadow-md">
                        <div class="pb-4 border-b border-festive-plum/10 mb-6">
                            <h2 class="text-lg font-serif font-bold text-festive-plum flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-festive-rose text-white text-xs flex items-center justify-center font-sans">2</span>
                                Payment Method
                            </h2>
                            <p class="text-xs text-festive-plum/60 mt-0.5">Select your preferred payment mode</p>
                        </div>

                        <div class="space-y-3">
                            {{-- Razorpay (Online: UPI, Cards, Netbanking) --}}
                            @if($razorpayAllowed)
                                <label 
                                    class="border rounded-2xl p-4 cursor-pointer transition-all flex items-start gap-4"
                                    :class="paymentMethod === 'razorpay' ? 'border-festive-rose bg-festive-rose/5 ring-1 ring-festive-rose' : 'border-festive-plum/15 bg-white/60 hover:border-festive-rose/50'"
                                >
                                    <input 
                                        type="radio" 
                                        name="payment_method" 
                                        value="razorpay" 
                                        x-model="paymentMethod" 
                                        class="mt-1 text-festive-rose focus:ring-festive-rose"
                                    >
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-bold text-festive-plum">Online Payment (Razorpay)</span>
                                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                                Instant & Secure
                                            </span>
                                        </div>
                                        <p class="text-xs text-festive-plum/70 mt-1">
                                            Pay via Google Pay, PhonePe, Paytm, BHIM UPI, Credit/Debit Cards, or Netbanking.
                                        </p>
                                    </div>
                                </label>
                            @endif

                            {{-- Cash on Delivery --}}
                            @if($codAllowed)
                                <label 
                                    class="border rounded-2xl p-4 cursor-pointer transition-all flex items-start gap-4"
                                    :class="paymentMethod === 'cod' ? 'border-festive-rose bg-festive-rose/5 ring-1 ring-festive-rose' : 'border-festive-plum/15 bg-white/60 hover:border-festive-rose/50'"
                                >
                                    <input 
                                        type="radio" 
                                        name="payment_method" 
                                        value="cod" 
                                        x-model="paymentMethod"
                                        :disabled="!isCodEligible"
                                        class="mt-1 text-festive-rose focus:ring-festive-rose disabled:opacity-50"
                                    >
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-bold text-festive-plum">Cash on Delivery (COD)</span>
                                            <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-festive-blush text-festive-plum">
                                                Pay at Doorstep
                                            </span>
                                        </div>
                                        <p class="text-xs text-festive-plum/70 mt-1">
                                            Pay in cash upon delivery at your Amritsar address.
                                        </p>
                                        <template x-if="!isCodEligible">
                                            <p class="text-[11px] text-red-600 font-medium mt-1">
                                                COD is only available for orders up to ₹{{ number_format($codLimit, 2) }}. Please pay online via Razorpay.
                                            </p>
                                        </template>
                                    </div>
                                </label>
                            @endif
                        </div>

                        {{-- Order Notes / Customizations --}}
                        <div class="mt-6 pt-4 border-t border-festive-plum/10">
                            <label class="block text-xs font-semibold text-festive-plum mb-1">
                                Order Notes / Instructions for Delivery (Optional)
                            </label>
                            <textarea 
                                name="customer_notes" 
                                rows="2" 
                                placeholder="Special tailoring requests, delivery timing, or gate instructions..."
                                class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                            ></textarea>
                        </div>
                    </div>

                </div>

                {{-- Right: Order Summary Breakdown (5 cols) --}}
                <div class="lg:col-span-5">
                    <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/80 shadow-md sticky top-24 space-y-6">
                        <h2 class="text-lg font-serif font-bold text-festive-plum pb-4 border-b border-festive-plum/10">
                            Your Order Items ({{ $summary['item_count'] }})
                        </h2>

                        {{-- Items compact list --}}
                        <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                            @foreach($cart->items as $item)
                                @php
                                    $img = $item->product?->primaryImage?->image_url ?? $item->product?->images->first()?->image_url ?? null;
                                @endphp
                                <div class="flex items-center gap-3 text-xs">
                                    <div class="w-12 h-14 rounded-lg bg-festive-blush/20 overflow-hidden flex-shrink-0 border border-festive-plum/10">
                                        @if($img)
                                            <img src="{{ $img }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-festive-plum truncate">{{ $item->product->name }}</h4>
                                        <p class="text-[11px] text-festive-plum/60">
                                            Qty: {{ $item->quantity }}
                                            @if($item->variant)
                                                ({{ $item->variant->size ?? $item->variant->color }})
                                            @endif
                                        </p>
                                    </div>
                                    <span class="font-bold font-serif text-festive-plum">
                                        ₹{{ number_format($item->subtotal, 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Price Math --}}
                        <div class="border-t border-festive-plum/10 pt-4 space-y-2 text-xs text-festive-plum/80">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span class="font-bold text-festive-plum">₹{{ number_format($summary['subtotal'], 2) }}</span>
                            </div>
                            @if($summary['discount'] > 0)
                                <div class="flex justify-between text-emerald-700">
                                    <span>Coupon Discount</span>
                                    <span class="font-bold">-₹{{ number_format($summary['discount'], 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <span>GST ({{ $summary['tax_rate'] }}%)</span>
                                <span class="font-bold text-festive-plum">₹{{ number_format($summary['tax'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Delivery Charge</span>
                                @if($summary['shipping'] == 0)
                                    <span class="font-bold text-emerald-700 uppercase tracking-wider text-[11px]">Free</span>
                                @else
                                    <span class="font-bold text-festive-plum">₹{{ number_format($summary['shipping'], 2) }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Total Highlight --}}
                        <div class="pt-4 border-t border-festive-plum/15 flex justify-between items-baseline">
                            <span class="text-sm font-bold text-festive-plum">Payable Amount</span>
                            <span class="text-2xl font-bold font-serif text-festive-rose">
                                ₹{{ number_format($summary['total'], 2) }}
                            </span>
                        </div>

                        {{-- Place Order Submit Button with Double-Click Prevention --}}
                        <div>
                            <button 
                                type="submit" 
                                id="submitOrderBtn"
                                :disabled="isSubmitting"
                                :class="isSubmitting ? 'opacity-70 cursor-not-allowed pointer-events-none' : 'hover:scale-[1.01] active:scale-[0.99]'"
                                class="w-full py-4 px-6 rounded-xl font-bold text-sm bg-festive-rose text-white shadow-xl shadow-festive-rose/30 hover:bg-festive-rose/90 transition-all flex items-center justify-center gap-2"
                            >
                                <template x-if="!isSubmitting">
                                    <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </template>
                                <template x-if="isSubmitting">
                                    <svg class="w-5 h-5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </template>
                                <span x-show="!isSubmitting" x-text="paymentMethod === 'razorpay' ? 'Proceed to Pay with Razorpay' : 'Confirm Cash on Delivery Order'"></span>
                                <span x-show="isSubmitting">Securing Order... Please wait</span>
                            </button>
                        </div>

                        <p class="text-[11px] text-center text-festive-plum/50">
                            By placing this order, you agree to Bombay Fashion's 
                            <a href="{{ route('pages.show', 'terms-and-conditions') }}" class="underline hover:text-festive-rose">Terms & Conditions</a> 
                            and 
                            <a href="{{ route('pages.show', 'return-policy') }}" class="underline hover:text-festive-rose">Return Policy</a>.
                        </p>
                    </div>
                </div>

            </div>
        </form>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('checkoutForm');
        if (form) {
            let submitted = false;
            form.addEventListener('submit', function (e) {
                if (submitted) {
                    e.preventDefault();
                    return false;
                }
                submitted = true;
                const btn = document.getElementById('submitOrderBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.style.opacity = '0.7';
                    btn.style.pointerEvents = 'none';
                }
            });
        }
    });
</script>
@endsection
