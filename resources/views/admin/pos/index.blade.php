@extends('layouts.admin')

@section('title', 'Showroom Walk-in POS Billing Counter - Bombay Fashion Admin')

@section('content')
<div 
    class="space-y-4"
    x-data="posApp()"
>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                    Live Showroom Counter
                </span>
                <span class="text-xs text-plum/50 font-medium">Bombay Fashion Amritsar</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-serif font-bold text-plum mt-0.5">
                Walk-in POS Billing Counter
            </h1>
        </div>
        <div class="flex items-center gap-2">
            <a 
                href="{{ route('admin.pos.history') }}" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold border border-plum/20 bg-white text-plum hover:bg-plum/5 shadow-xs transition-all flex items-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Daily Cash Register History
            </a>
        </div>
    </div>

    {{-- Main POS 2-Column Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        
        {{-- Left 7/12: Product Catalog & Fast Search --}}
        <div class="lg:col-span-7 xl:col-span-8 space-y-4">
            
            {{-- Search & Category Filter --}}
            <div class="glass-card rounded-2xl p-3 sm:p-4 border border-white/80 bg-white/85 shadow-xs space-y-3">
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 absolute left-3 top-3 text-plum/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <form method="GET" action="{{ route('admin.pos.index') }}">
                            @if(request('category_id'))
                                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                            @endif
                            <input 
                                type="text" 
                                name="q" 
                                value="{{ request('q') }}" 
                                placeholder="Scan barcode, SKU, or search cloth name (e.g. Bridal Saree, Silk Suit)..." 
                                class="w-full pl-9 pr-3 py-2 rounded-xl border-plum/20 bg-white text-xs text-plum focus:ring-primary"
                                autofocus
                            >
                        </form>
                    </div>
                </div>

                {{-- Category Filter Pills --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                    <a 
                        href="{{ route('admin.pos.index', request()->only('q')) }}" 
                        class="px-3 py-1.5 rounded-xl font-semibold shrink-0 transition {{ !request('category_id') ? 'bg-primary text-white shadow-xs' : 'bg-plum/5 text-plum/70 hover:bg-plum/10' }}"
                    >
                        All Clothes
                    </a>
                    @foreach($categories as $cat)
                        <a 
                            href="{{ route('admin.pos.index', array_merge(request()->only('q'), ['category_id' => $cat->id])) }}" 
                            class="px-3 py-1.5 rounded-xl font-semibold shrink-0 transition {{ request('category_id') == $cat->id ? 'bg-primary text-white shadow-xs' : 'bg-plum/5 text-plum/70 hover:bg-plum/10' }}"
                        >
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Products Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
                @forelse($products as $prod)
                    <div class="glass-card rounded-2xl p-3 border border-white/80 bg-white/90 shadow-xs flex flex-col justify-between hover:shadow-md transition">
                        <div>
                            {{-- Product Image --}}
                            <div class="relative aspect-square rounded-xl overflow-hidden bg-plum/5 mb-2">
                                @if($prod->primaryImage)
                                    <img src="{{ $prod->primaryImage->image_url }}" alt="{{ $prod->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center font-bold text-primary-600 bg-primary-50 text-xs">
                                        BF Garment
                                    </div>
                                @endif
                                <div class="absolute top-1.5 right-1.5">
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $prod->stock <= 0 ? 'bg-rose-500 text-white' : ($prod->stock <= 5 ? 'bg-amber-500 text-white' : 'bg-emerald-600 text-white') }}">
                                        {{ $prod->stock }} left
                                    </span>
                                </div>
                            </div>

                            <span class="text-[10px] uppercase font-bold text-primary-700 tracking-wider block">
                                {{ $prod->category?->name ?? 'Cloth' }}
                            </span>
                            <h3 class="font-bold text-plum text-xs leading-snug line-clamp-2 mt-0.5" title="{{ $prod->name }}">
                                {{ $prod->name }}
                            </h3>
                            <div class="flex items-baseline gap-1.5 mt-1">
                                <span class="font-serif font-bold text-primary text-sm">
                                    ₹{{ number_format($prod->effective_price ?? $prod->price, 2) }}
                                </span>
                                @if($prod->discount_price)
                                    <span class="text-[10px] text-plum/40 line-through">
                                        ₹{{ number_format($prod->price, 2) }}
                                    </span>
                                @endif
                            </div>
                            @if($prod->fabric)
                                <span class="text-[10px] text-plum/60 block mt-0.5">{{ $prod->fabric }}</span>
                            @endif
                        </div>

                        {{-- Variant Quick Selection & Add Button --}}
                        <div class="mt-2.5 pt-2 border-t border-plum/10">
                            @if($prod->variants->count() > 0)
                                <div class="text-[10px] font-semibold text-plum/60 mb-1">Select Size:</div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($prod->variants as $var)
                                        <button 
                                            @click="addToCart({{ $prod->id }}, '{{ addslashes($prod->name) }}', '{{ $prod->sku }}', {{ $var->id }}, '{{ $var->size ?: 'Standard' }} {{ $var->color }}', {{ (float)($var->effective_price ?? (($prod->discount_price ?? $prod->price) + $var->additional_price)) }}, {{ $var->stock }})"
                                            :disabled="{{ $var->stock <= 0 ? 'true' : 'false' }}"
                                            type="button"
                                            class="px-2 py-1 rounded text-[10px] font-bold border transition cursor-pointer {{ $var->stock <= 0 ? 'opacity-40 cursor-not-allowed bg-slate-100 border-slate-200' : 'bg-white hover:bg-primary hover:text-white border-plum/20 text-plum' }}"
                                            title="Stock: {{ $var->stock }}"
                                        >
                                            {{ $var->size ?: 'Std' }}
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <button 
                                    @click="addToCart({{ $prod->id }}, '{{ addslashes($prod->name) }}', '{{ $prod->sku }}', 0, 'Standard', {{ (float)($prod->effective_price ?? $prod->price) }}, {{ $prod->stock }})"
                                    :disabled="{{ $prod->stock <= 0 ? 'true' : 'false' }}"
                                    type="button"
                                    class="w-full py-1.5 rounded-xl font-bold text-xs transition cursor-pointer {{ $prod->stock <= 0 ? 'bg-slate-200 text-slate-500 cursor-not-allowed' : 'bg-primary text-white hover:bg-primary-700 shadow-xs' }}"
                                >
                                    {{ $prod->stock <= 0 ? 'Sold Out' : '+ Add to Bill' }}
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-10 text-center text-xs text-plum/50 italic bg-white/50 rounded-2xl border border-plum/10">
                        No garments found matching query. Try clearing search filters.
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>

        {{-- Right 5/12: Live POS Counter Cart & Bill Terminal --}}
        <div class="lg:col-span-5 xl:col-span-4 sticky top-4">
            <div class="glass-card rounded-3xl p-5 border border-white/90 bg-white/95 shadow-xl space-y-4">
                
                {{-- Register Header --}}
                <div class="flex items-center justify-between pb-3 border-b border-plum/10">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></div>
                        <h2 class="font-serif font-bold text-plum text-base">Showroom Counter Cart</h2>
                    </div>
                    <button 
                        @click="clearCart()" 
                        x-show="cartItems.length > 0"
                        class="text-[11px] font-bold text-rose-600 hover:underline cursor-pointer"
                    >
                        Clear Cart
                    </button>
                </div>

                {{-- Customer Info & Staff Purchase Option --}}
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-plum/70 text-[10px]">Buyer / Customer Details</span>
                        <label class="flex items-center gap-1.5 cursor-pointer text-[10px] font-bold text-primary">
                            <input 
                                type="checkbox" 
                                x-model="isStaffPurchase" 
                                @change="onStaffPurchaseToggle()"
                                class="rounded border-plum/20 text-primary focus:ring-primary w-3.5 h-3.5"
                            >
                            <span>🛍️ Staff / Self Purchase</span>
                        </label>
                    </div>

                    <div x-show="isStaffPurchase" class="p-2.5 rounded-xl bg-primary-50/70 border border-primary-100 space-y-1" style="display: none;">
                        <label class="block font-semibold text-primary text-[10px]">Select Buying Staff Member</label>
                        <select 
                            x-model="staffUserId" 
                            @change="onStaffSelected()"
                            class="w-full p-2 rounded-xl border-primary-200 bg-white text-xs text-plum font-semibold focus:ring-primary"
                        >
                            <option value="">-- Choose Staff / Cashier Account --</option>
                            @foreach($staffMembers as $sm)
                                <option value="{{ $sm->id }}" data-name="{{ $sm->name }}" data-phone="{{ $sm->phone }}" data-email="{{ $sm->email }}" {{ $sm->id === auth()->id() ? 'selected' : '' }}>
                                    {{ $sm->name }} ({{ $sm->employee_id ?: 'Staff' }}) {{ $sm->phone ? '• ' . $sm->phone : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[9px] text-primary/70">Ye bill staff ke login me "My Purchases" me immediately dikhega.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <label class="block font-semibold text-plum/70 text-[10px] mb-0.5">Customer Name</label>
                            <input 
                                type="text" 
                                x-model="customerName" 
                                placeholder="Walk-in Customer" 
                                class="w-full p-2 rounded-xl border-plum/20 bg-white text-xs text-plum focus:ring-primary"
                            >
                        </div>
                        <div>
                            <label class="block font-semibold text-plum/70 text-[10px] mb-0.5">Phone Number</label>
                            <input 
                                type="text" 
                                x-model="customerPhone" 
                                placeholder="10-digit mobile" 
                                class="w-full p-2 rounded-xl border-plum/20 bg-white text-xs text-plum focus:ring-primary"
                            >
                        </div>
                    </div>
                </div>

                {{-- Cart Items List --}}
                <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                    <template x-if="cartItems.length === 0">
                        <div class="py-8 text-center text-xs text-plum/40 italic">
                            Cart is empty. Click garments on the left to add to bill.
                        </div>
                    </template>

                    <template x-for="(item, index) in cartItems" :key="index">
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-plum/5 border border-plum/5 text-xs">
                            <div class="flex-1 pr-2">
                                <span class="font-bold text-plum block leading-tight text-[11px]" x-text="item.name"></span>
                                <span class="text-[10px] text-plum/60" x-text="item.variant_details + ' • ₹' + item.price.toFixed(2)"></span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="flex items-center border border-plum/20 rounded-lg bg-white overflow-hidden">
                                    <button 
                                        type="button"
                                        @click="decreaseQty(index)" 
                                        class="px-2 py-0.5 text-plum font-bold hover:bg-plum/5"
                                    >-</button>
                                    <span class="px-2 py-0.5 font-bold font-mono text-[11px]" x-text="item.quantity"></span>
                                    <button 
                                        type="button"
                                        @click="increaseQty(index)" 
                                        class="px-2 py-0.5 text-plum font-bold hover:bg-plum/5"
                                    >+</button>
                                </div>
                                <span class="font-serif font-bold text-plum text-xs w-16 text-right" x-text="'₹' + (item.price * item.quantity).toFixed(2)"></span>
                                <button 
                                    type="button"
                                    @click="removeItem(index)" 
                                    class="text-rose-500 hover:text-rose-700 font-bold px-1"
                                >&times;</button>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Bill Calculations --}}
                <div class="pt-3 border-t border-plum/10 space-y-1.5 text-xs">
                    <div class="flex justify-between text-plum/70">
                        <span>Subtotal</span>
                        <span class="font-mono font-semibold" x-text="'₹' + calculateSubtotal().toFixed(2)"></span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-plum/70">Counter Discount (₹)</span>
                        <input 
                            type="number" 
                            x-model.number="discountAmount" 
                            min="0" 
                            class="w-20 p-1 text-right rounded-lg border-plum/20 text-xs text-plum font-mono"
                        >
                    </div>

                    <div class="flex justify-between text-plum/70">
                        <span>GST (5% Apparel Tax)</span>
                        <span class="font-mono font-semibold" x-text="'₹' + calculateTax().toFixed(2)"></span>
                    </div>

                    <div class="flex justify-between text-sm font-bold text-plum pt-2 border-t border-plum/10">
                        <span class="font-serif text-base">Grand Total</span>
                        <span class="font-serif text-lg text-primary" x-text="'₹' + calculateGrandTotal().toFixed(2)"></span>
                    </div>
                </div>

                {{-- Payment Mode Selector --}}
                <div class="pt-2 border-t border-plum/10 space-y-2">
                    <label class="block font-semibold text-plum text-[11px]">Payment Mode</label>
                    <div class="grid grid-cols-3 gap-1.5">
                        <button 
                            type="button"
                            @click="paymentMethod = 'cash'" 
                            :class="paymentMethod === 'cash' ? 'bg-primary text-white font-bold' : 'bg-plum/5 text-plum/80'"
                            class="py-2 rounded-xl text-xs transition cursor-pointer text-center"
                        >
                            💵 Cash
                        </button>
                        <button 
                            type="button"
                            @click="paymentMethod = 'upi'" 
                            :class="paymentMethod === 'upi' ? 'bg-primary text-white font-bold' : 'bg-plum/5 text-plum/80'"
                            class="py-2 rounded-xl text-xs transition cursor-pointer text-center"
                        >
                            📱 UPI / QR
                        </button>
                        <button 
                            type="button"
                            @click="paymentMethod = 'card'" 
                            :class="paymentMethod === 'card' ? 'bg-primary text-white font-bold' : 'bg-plum/5 text-plum/80'"
                            class="py-2 rounded-xl text-xs transition cursor-pointer text-center"
                        >
                            💳 Card
                        </button>
                    </div>

                    {{-- Cash Tendered & Change Calculator --}}
                    <div x-show="paymentMethod === 'cash'" class="p-2.5 rounded-xl bg-gold-50/60 border border-gold-200/50 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-plum">Cash Tendered (₹):</span>
                            <input 
                                type="number" 
                                x-model.number="cashTendered" 
                                placeholder="Cash received" 
                                class="w-24 p-1.5 text-right rounded-lg border-gold-300 bg-white font-mono font-bold text-xs"
                            >
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-bold text-plum">Change to Return:</span>
                            <span class="font-mono font-bold text-emerald-700 text-sm" x-text="'₹' + calculateChange().toFixed(2)"></span>
                        </div>
                    </div>
                </div>

                {{-- Complete Sale Button --}}
                <button 
                    type="button"
                    @click="processCheckout()"
                    :disabled="cartItems.length === 0 || isProcessing"
                    class="w-full py-3 rounded-2xl font-bold text-sm bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white shadow-lg shadow-emerald-600/25 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2 cursor-pointer"
                >
                    <span x-show="!isProcessing">🧾 Complete Sale & Print Receipt</span>
                    <span x-show="isProcessing">Processing Sale...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function posApp() {
    return {
        cartItems: [],
        customerName: '',
        customerPhone: '',
        isStaffPurchase: false,
        staffUserId: '{{ auth()->id() }}',
        discountAmount: 0,
        paymentMethod: 'cash',
        cashTendered: 0,
        isProcessing: false,

        onStaffPurchaseToggle() {
            if (this.isStaffPurchase) {
                this.staffUserId = '{{ auth()->id() }}';
                this.customerName = '{{ auth()->user()->name }}';
                this.customerPhone = '{{ auth()->user()->phone ?? "" }}';
            } else {
                this.staffUserId = '';
                this.customerName = '';
                this.customerPhone = '';
            }
        },

        onStaffSelected() {
            const selectEl = document.querySelector('select[x-model="staffUserId"]');
            if (selectEl && selectEl.selectedOptions[0]) {
                const opt = selectEl.selectedOptions[0];
                this.customerName = opt.getAttribute('data-name') || '';
                this.customerPhone = opt.getAttribute('data-phone') || '';
            }
        },

        addToCart(productId, name, sku, variantId, variantDetails, price, stock) {
            const existing = this.cartItems.find(i => i.product_id === productId && i.variant_id === variantId);
            if (existing) {
                if (existing.quantity >= stock) {
                    alert('Cannot add more than available stock (' + stock + ' units).');
                    return;
                }
                existing.quantity++;
            } else {
                this.cartItems.push({
                    product_id: productId,
                    variant_id: variantId,
                    name: name,
                    sku: sku,
                    variant_details: variantDetails,
                    price: price,
                    quantity: 1,
                    stock: stock
                });
            }
        },

        increaseQty(index) {
            const item = this.cartItems[index];
            if (item.quantity >= item.stock) {
                alert('Cannot exceed available stock (' + item.stock + ' units).');
                return;
            }
            item.quantity++;
        },

        decreaseQty(index) {
            if (this.cartItems[index].quantity > 1) {
                this.cartItems[index].quantity--;
            } else {
                this.removeItem(index);
            }
        },

        removeItem(index) {
            this.cartItems.splice(index, 1);
        },

        clearCart() {
            if (confirm('Clear all items from the counter cart?')) {
                this.cartItems = [];
                this.discountAmount = 0;
                this.cashTendered = 0;
            }
        },

        calculateSubtotal() {
            return this.cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },

        calculateTax() {
            const subtotal = this.calculateSubtotal();
            const taxable = Math.max(0, subtotal - (this.discountAmount || 0));
            return Math.round(taxable * 0.05 * 100) / 100;
        },

        calculateGrandTotal() {
            const subtotal = this.calculateSubtotal();
            const taxable = Math.max(0, subtotal - (this.discountAmount || 0));
            return Math.round((taxable + this.calculateTax()) * 100) / 100;
        },

        calculateChange() {
            const grandTotal = this.calculateGrandTotal();
            const tendered = this.cashTendered || 0;
            return tendered > grandTotal ? (tendered - grandTotal) : 0;
        },

        async processCheckout() {
            if (this.cartItems.length === 0) return;

            this.isProcessing = true;
            try {
                const response = await fetch("{{ route('admin.pos.checkout') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        staff_user_id: this.isStaffPurchase ? this.staffUserId : null,
                        customer_name: this.customerName,
                        customer_phone: this.customerPhone,
                        payment_method: this.paymentMethod,
                        discount_amount: this.discountAmount,
                        cash_tendered: this.cashTendered,
                        items: this.cartItems.map(item => ({
                            product_id: item.product_id,
                            variant_id: item.variant_id,
                            quantity: item.quantity
                        }))
                    })
                });

                const data = await response.json();

                if (data.success) {
                    alert('Sale completed successfully! Invoice #' + data.order_number);
                    // Open thermal receipt in popup window
                    if (data.receipt_url) {
                        window.open(data.receipt_url, '_blank', 'width=450,height=650');
                    }
                    this.cartItems = [];
                    this.discountAmount = 0;
                    this.cashTendered = 0;
                    this.customerName = '';
                    this.customerPhone = '';
                    // Reload to update stock numbers
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert('Error: ' + (data.message || 'Could not process sale.'));
                }
            } catch (err) {
                alert('Checkout failed: ' + err.message);
            } finally {
                this.isProcessing = false;
            }
        }
    };
}
</script>
@endsection
