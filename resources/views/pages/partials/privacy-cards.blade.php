{{-- Attractive Point-by-Point Cards for Privacy Policy --}}
<div class="space-y-8">

    {{-- Highlight Trust Badges --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="glass-card rounded-2xl p-4 bg-white/90 border border-emerald-100 shadow-sm text-center">
            <div class="w-10 h-10 mx-auto rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg mb-2">
                🔒
            </div>
            <h4 class="font-bold text-xs text-plum">256-Bit SSL Encrypted</h4>
            <p class="text-[11px] text-plum/60 mt-0.5">End-to-end checkout</p>
        </div>

        <div class="glass-card rounded-2xl p-4 bg-white/90 border border-primary-100 shadow-sm text-center">
            <div class="w-10 h-10 mx-auto rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center text-lg mb-2">
                💳
            </div>
            <h4 class="font-bold text-xs text-plum">Zero Card Stored</h4>
            <p class="text-[11px] text-plum/60 mt-0.5">PCI-DSS Compliant</p>
        </div>

        <div class="glass-card rounded-2xl p-4 bg-white/90 border border-gold-200/80 shadow-sm text-center">
            <div class="w-10 h-10 mx-auto rounded-xl bg-gold-50 text-gold-700 flex items-center justify-center text-lg mb-2">
                🛡️
            </div>
            <h4 class="font-bold text-xs text-plum">No Spam Policy</h4>
            <p class="text-[11px] text-plum/60 mt-0.5">Zero third-party ads</p>
        </div>

        <div class="glass-card rounded-2xl p-4 bg-white/90 border border-teal-100 shadow-sm text-center">
            <div class="w-10 h-10 mx-auto rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center text-lg mb-2">
                🇮🇳
            </div>
            <h4 class="font-bold text-xs text-plum">Indian IT Act 2000</h4>
            <p class="text-[11px] text-plum/60 mt-0.5">Full SPDI Compliance</p>
        </div>
    </div>

    {{-- Interactive Quick Jumper Pills --}}
    <div class="flex flex-wrap items-center gap-2 pt-2">
        <span class="text-xs font-semibold text-plum/60">Quick Jump:</span>
        <a href="#data-collection" class="px-3 py-1 rounded-full text-xs font-medium bg-plum/5 hover:bg-primary-50 hover:text-primary transition">1. What We Collect</a>
        <a href="#data-usage" class="px-3 py-1 rounded-full text-xs font-medium bg-plum/5 hover:bg-primary-50 hover:text-primary transition">2. How We Use It</a>
        <a href="#payment-safety" class="px-3 py-1 rounded-full text-xs font-medium bg-plum/5 hover:bg-primary-50 hover:text-primary transition">3. Payment Safety</a>
        <a href="#logistics-sharing" class="px-3 py-1 rounded-full text-xs font-medium bg-plum/5 hover:bg-primary-50 hover:text-primary transition">4. Third Parties</a>
        <a href="#cookies-session" class="px-3 py-1 rounded-full text-xs font-medium bg-plum/5 hover:bg-primary-50 hover:text-primary transition">5. Cookies</a>
        <a href="#customer-rights" class="px-3 py-1 rounded-full text-xs font-medium bg-plum/5 hover:bg-primary-50 hover:text-primary transition">6. Your Rights</a>
        <a href="#grievance-contact" class="px-3 py-1 rounded-full text-xs font-medium bg-plum/5 hover:bg-primary-50 hover:text-primary transition">7. Grievance Officer</a>
    </div>

    {{-- POINT 1: What Information We Collect --}}
    <div id="data-collection" class="glass-card rounded-3xl p-6 sm:p-8 bg-white/95 border border-primary-100/80 shadow-md space-y-4 scroll-mt-24">
        <div class="flex items-center justify-between pb-3 border-b border-plum/10">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-sm">01</span>
                <div>
                    <h3 class="font-serif font-bold text-base sm:text-lg text-plum">Information We Collect (डेटा संग्रह)</h3>
                    <p class="text-[11px] text-plum/60">Details collected when you browse, create an account, or order clothing</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 text-[10px] font-bold uppercase tracking-wider">Transparent</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="p-4 rounded-2xl bg-cream-100/50 border border-primary-100/50 space-y-2">
                <h4 class="font-bold text-plum flex items-center gap-2">
                    <span class="text-primary">👤</span> Personal Identification & Contact
                </h4>
                <ul class="space-y-1.5 text-plum/80 list-disc list-inside">
                    <li>Full customer name, mobile calling number, and WhatsApp number.</li>
                    <li>Valid email address for order receipts, tracking links, and reset tokens.</li>
                    <li>Account password (always hashed using industry-standard bcrypt encryption).</li>
                </ul>
            </div>

            <div class="p-4 rounded-2xl bg-cream-100/50 border border-primary-100/50 space-y-2">
                <h4 class="font-bold text-plum flex items-center gap-2">
                    <span class="text-primary">📍</span> Shipping & Delivery Address
                </h4>
                <ul class="space-y-1.5 text-plum/80 list-disc list-inside">
                    <li>Complete postal address, apartment/house number, and landmark.</li>
                    <li>City, district, state, and 6-digit postal PIN code across India.</li>
                    <li>Optional delivery notes (e.g., "call before arrival" or "leave at reception").</li>
                </ul>
            </div>

            <div class="p-4 rounded-2xl bg-cream-100/50 border border-primary-100/50 space-y-2">
                <h4 class="font-bold text-plum flex items-center gap-2">
                    <span class="text-primary">👗</span> Order, Sizing & Customization Details
                </h4>
                <ul class="space-y-1.5 text-plum/80 list-disc list-inside">
                    <li>Purchased sarees, salwar suits, kurtas, fabrics, and school uniform sizes.</li>
                    <li>Custom tailoring measurements and alteration instructions provided by you.</li>
                    <li>Billing transaction totals, discount coupons applied, and invoices.</li>
                </ul>
            </div>

            <div class="p-4 rounded-2xl bg-cream-100/50 border border-primary-100/50 space-y-2">
                <h4 class="font-bold text-plum flex items-center gap-2">
                    <span class="text-primary">💻</span> Device, IP & Session Data
                </h4>
                <ul class="space-y-1.5 text-plum/80 list-disc list-inside">
                    <li>IP address, browser type, and operating system for security and fraud protection.</li>
                    <li>Active session cookies to maintain your shopping cart items between visits.</li>
                    <li>Timestamps of customer login and order placement for audit compliance.</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- POINT 2: How We Use Your Information --}}
    <div id="data-usage" class="glass-card rounded-3xl p-6 sm:p-8 bg-white/95 border border-primary-100/80 shadow-md space-y-4 scroll-mt-24">
        <div class="flex items-center justify-between pb-3 border-b border-plum/10">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-gold-100 text-gold-800 flex items-center justify-center font-bold text-sm">02</span>
                <div>
                    <h3 class="font-serif font-bold text-base sm:text-lg text-plum">How We Use Your Information (उपयोग के उद्देश्य)</h3>
                    <p class="text-[11px] text-plum/60">Strictly utilized to fulfill retail orders and enhance showroom service</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-gold-50 text-gold-800 text-[10px] font-bold uppercase tracking-wider">Purpose-Driven</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div class="p-4 rounded-2xl bg-white border border-plum/10 shadow-xs space-y-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">📦</div>
                <h4 class="font-bold text-plum">Order Fulfillment & Tailoring</h4>
                <p class="text-plum/70 leading-relaxed">To cut, stitch, package, and dispatch your ethnic wear orders through our partner couriers to your doorstep.</p>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-plum/10 shadow-xs space-y-2">
                <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-sm">📲</div>
                <h4 class="font-bold text-plum">Live SMS & WhatsApp Alerts</h4>
                <p class="text-plum/70 leading-relaxed">To notify you regarding order confirmation, dispatch tracking number, OTP verification, and delivery milestones.</p>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-plum/10 shadow-xs space-y-2">
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-sm">🤝</div>
                <h4 class="font-bold text-plum">Customer Service & Returns</h4>
                <p class="text-plum/70 leading-relaxed">To provide dedicated help for size exchanges, bridal blouse styling questions, or refund settlement via UPI/Bank.</p>
            </div>
        </div>
    </div>

    {{-- POINT 3: Payment Security & Non-Storage of Card Data --}}
    <div id="payment-safety" class="glass-card rounded-3xl p-6 sm:p-8 bg-gradient-to-br from-emerald-50/40 via-white to-white border border-emerald-200/80 shadow-md space-y-4 scroll-mt-24">
        <div class="flex items-center justify-between pb-3 border-b border-emerald-100">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm">03</span>
                <div>
                    <h3 class="font-serif font-bold text-base sm:text-lg text-plum">Payment Security & Zero Card Storage (भुगतान सुरक्षा)</h3>
                    <p class="text-[11px] text-emerald-800/80">Direct tokenized integration via RBI-certified payment aggregators</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold uppercase tracking-wider">Bank-Grade</span>
        </div>

        <div class="space-y-3 text-xs text-plum/85 leading-relaxed">
            <p>
                <strong>No Financial Secrets Stored on Bombay Fashion:</strong> When you pay via Credit Card, Debit Card, Net Banking, or UPI (GPay, PhonePe, Paytm), the transaction takes place entirely on encrypted, PCI-DSS Level 1 certified gateway servers (e.g. Razorpay).
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <div class="p-3.5 rounded-xl bg-white border border-emerald-200/60 flex items-start gap-2.5">
                    <span class="text-emerald-600 font-bold text-sm">✓</span>
                    <div>
                        <strong class="text-plum block">What We Never Touch or Store:</strong>
                        <span class="text-plum/70">Full 16-digit card numbers, CVV security codes, ATM PINs, or UPI MPINs.</span>
                    </div>
                </div>
                <div class="p-3.5 rounded-xl bg-white border border-emerald-200/60 flex items-start gap-2.5">
                    <span class="text-emerald-600 font-bold text-sm">✓</span>
                    <div>
                        <strong class="text-plum block">What We Store for Invoicing:</strong>
                        <span class="text-plum/70">Only the gateway transaction ID (e.g., pay_XXXXX), payment method (e.g. COD / UPI), and payment status.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- POINT 4: Third-Party Sharing & Logistics --}}
    <div id="logistics-sharing" class="glass-card rounded-3xl p-6 sm:p-8 bg-white/95 border border-primary-100/80 shadow-md space-y-4 scroll-mt-24">
        <div class="flex items-center justify-between pb-3 border-b border-plum/10">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-primary-100 text-primary-700 flex items-center justify-center font-bold text-sm">04</span>
                <div>
                    <h3 class="font-serif font-bold text-base sm:text-lg text-plum">Data Sharing & Logistics Disclosures (तीसरे पक्ष के नियम)</h3>
                    <p class="text-[11px] text-plum/60">Strictly limited to couriers for doorstep delivery</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 text-[10px] font-bold uppercase tracking-wider">Zero Ad Spam</span>
        </div>

        <div class="space-y-3 text-xs text-plum/80">
            <div class="p-4 rounded-2xl bg-rose-50/50 border border-rose-200/60 flex items-center gap-3">
                <span class="text-2xl">🚫</span>
                <div>
                    <strong class="text-rose-900 block">Strict No-Sale Policy:</strong>
                    <span class="text-rose-800/80">Bombay Fashion will never sell, rent, lease, or monetize your contact information, phone numbers, or order histories to any advertising brokers or marketing telemarketers.</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <div class="p-3.5 rounded-xl bg-cream-100/40 border border-primary-100/50">
                    <strong class="text-plum block mb-1">🚚 National Courier Partners</strong>
                    <p class="text-plum/70">Name, shipping address, and phone number are shared solely with logistics services (Delhivery, BlueDart, DTDC, India Post) to print shipping labels and execute delivery.</p>
                </div>
                <div class="p-3.5 rounded-xl bg-cream-100/40 border border-primary-100/50">
                    <strong class="text-plum block mb-1">⚖️ Legal & Government Inquiries</strong>
                    <p class="text-plum/70">Information will be disclosed only if formally mandated by judicial order, police warrant, or statutory compliance under Indian laws.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- POINT 5: Cookies & Browsing Sessions --}}
    <div id="cookies-session" class="glass-card rounded-3xl p-6 sm:p-8 bg-white/95 border border-primary-100/80 shadow-md space-y-4 scroll-mt-24">
        <div class="flex items-center justify-between pb-3 border-b border-plum/10">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center font-bold text-sm">05</span>
                <div>
                    <h3 class="font-serif font-bold text-base sm:text-lg text-plum">Cookies & Browsing Session Policy (कुकीज़ नीति)</h3>
                    <p class="text-[11px] text-plum/60">How small memory tokens make your shopping effortless</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-teal-50 text-teal-800 text-[10px] font-bold uppercase tracking-wider">Functional Only</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div class="p-3.5 rounded-2xl bg-white border border-plum/10">
                <strong class="text-plum block mb-1">🛒 Shopping Cart Cookie</strong>
                <p class="text-plum/70 leading-relaxed">Remembers which suits or sarees you added to your bag so your selection is not lost while browsing other catalogs.</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-white border border-plum/10">
                <strong class="text-plum block mb-1">🔐 Secure Auth Token</strong>
                <p class="text-plum/70 leading-relaxed">Keeps your account authenticated safely so you don't have to enter your password on every page click.</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-white border border-plum/10">
                <strong class="text-plum block mb-1">⚙️ Browser Control</strong>
                <p class="text-plum/70 leading-relaxed">You have full autonomy to delete or block cookies anytime through your Chrome, Safari, or Firefox browser preferences.</p>
            </div>
        </div>
    </div>

    {{-- POINT 6: Customer Rights & Data Management --}}
    <div id="customer-rights" class="glass-card rounded-3xl p-6 sm:p-8 bg-white/95 border border-primary-100/80 shadow-md space-y-4 scroll-mt-24">
        <div class="flex items-center justify-between pb-3 border-b border-plum/10">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-sm">06</span>
                <div>
                    <h3 class="font-serif font-bold text-base sm:text-lg text-plum">Your Customer Privacy Rights (ग्राहक के अधिकार)</h3>
                    <p class="text-[11px] text-plum/60">You maintain complete authority over your personal information</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 text-[10px] font-bold uppercase tracking-wider">Your Control</span>
        </div>

        <div class="space-y-2.5 text-xs text-plum/80">
            <div class="p-3 rounded-xl bg-cream-100/50 border border-primary-100/40 flex items-start gap-2.5">
                <span class="font-bold text-primary">✓</span>
                <div>
                    <strong class="text-plum">Right to Review & Update:</strong>
                    <span class="ml-1">You can log into your account dashboard anytime to update your shipping addresses, mobile phone, and contact details.</span>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-cream-100/50 border border-primary-100/40 flex items-start gap-2.5">
                <span class="font-bold text-primary">✓</span>
                <div>
                    <strong class="text-plum">Right to Erasure / Account Deletion:</strong>
                    <span class="ml-1">You may request complete deactivation of your account and deletion of your stored profile by emailing us at <code class="font-mono text-primary font-bold">care@bombayfashionamritsar.com</code>.</span>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-cream-100/50 border border-primary-100/40 flex items-start gap-2.5">
                <span class="font-bold text-primary">✓</span>
                <div>
                    <strong class="text-plum">Right to Opt-Out of Promotional Alerts:</strong>
                    <span class="ml-1">You can reply "STOP" on promotional WhatsApp notifications or click unsubscribe in email newsletters with a single click.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- POINT 7: Grievance Redressal & Showroom Authority --}}
    <div id="grievance-contact" class="glass-card rounded-3xl p-6 sm:p-8 bg-gradient-to-br from-primary-50/50 via-white to-gold-50/40 border border-primary-200 shadow-lg space-y-4 scroll-mt-24">
        <div class="flex items-center justify-between pb-3 border-b border-primary-100">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center font-bold text-sm">07</span>
                <div>
                    <h3 class="font-serif font-bold text-base sm:text-lg text-plum">Grievance Redressal Officer (शिकायत निवारण अधिकारी)</h3>
                    <p class="text-[11px] text-plum/60">Designated contact person under the Information Technology (Reasonable Security Practices) Rules</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-primary-100 text-primary-800 text-[10px] font-bold uppercase tracking-wider">Official DPO</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-4 rounded-2xl bg-white/90 border border-primary-100 shadow-xs space-y-1.5">
                <span class="text-[10px] uppercase font-bold text-primary tracking-wider">Grievance Officer</span>
                <h4 class="font-bold text-sm text-plum">Customer Relations & Data Officer</h4>
                <p class="text-plum/80 leading-relaxed">
                    <strong>Store:</strong> Bombay Fashion Showroom<br>
                    <strong>Address:</strong> Mahila College Road, Opposite Mahila College, Amritsar, Punjab - 143001, India<br>
                    <strong>Operating Hours:</strong> 10:00 AM – 8:30 PM (Daily)
                </p>
            </div>

            <div class="p-4 rounded-2xl bg-white/90 border border-primary-100 shadow-xs space-y-2">
                <span class="text-[10px] uppercase font-bold text-primary tracking-wider">Direct Resolution Channels</span>
                <p class="text-plum/80 leading-relaxed">
                    <strong>Email:</strong> <a href="mailto:care@bombayfashionamritsar.com" class="text-primary font-bold hover:underline">care@bombayfashionamritsar.com</a><br>
                    <strong>Calling Helpline:</strong> <a href="tel:+919876543210" class="text-primary font-bold hover:underline">+91 98765 43210</a><br>
                    <strong>WhatsApp Support:</strong> <a href="https://wa.me/919876543210" target="_blank" class="text-emerald-700 font-bold hover:underline">+91 98765 43210 (Chat)</a><br>
                    <span class="text-[11px] text-plum/60 mt-1 block">Guaranteed dispute acknowledgment within 48 business hours.</span>
                </p>
            </div>
        </div>
    </div>

</div>
