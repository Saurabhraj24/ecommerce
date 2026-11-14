@extends('layouts.app')

@section('title', 'Contact Us - Bombay Fashion Amritsar')
@section('meta_description', 'Visit Bombay Fashion on Mahila College Road, Amritsar. Open daily until 9:30 PM. Call or message us for clothing and uniform queries.')

@section('content')
<div class="py-12 bg-festive-ivory/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-festive-blush text-festive-rose mb-3">
                Amritsar Physical Store & Online Help
            </span>
            <h1 class="text-3xl sm:text-4xl font-serif font-bold text-festive-plum mb-3">
                Visit Us or Get in Touch
            </h1>
            <p class="text-sm text-festive-plum/70">
                Have a question regarding bridal sarees, school uniforms, order custom fittings, or online deliveries? We are right here opposite Mahila College, Amritsar.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
            
            {{-- Left: Store Details & Timings (5 cols) --}}
            <div class="lg:col-span-5 space-y-6">
                
                {{-- Address Card --}}
                <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/85 shadow-md space-y-5">
                    <h2 class="text-xl font-serif font-bold text-festive-plum pb-3 border-b border-festive-plum/10">
                        Store Information
                    </h2>

                    {{-- Location --}}
                    <div class="flex items-start gap-3 text-xs">
                        <div class="p-2.5 rounded-xl bg-festive-blush text-festive-rose flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold text-festive-plum text-sm block">Bombay Fashion</span>
                            <p class="text-festive-plum/80 leading-relaxed mt-0.5">
                                {{ $settings['store']['address'] ?? 'Mahila College Road, opposite Mahila College, Amritsar, Punjab, India' }}
                            </p>
                        </div>
                    </div>

                    {{-- Timings --}}
                    <div class="flex items-start gap-3 text-xs">
                        <div class="p-2.5 rounded-xl bg-festive-gold/15 text-festive-plum flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold text-festive-plum text-sm block">Store Hours</span>
                            <p class="text-festive-plum/80 leading-relaxed mt-0.5 font-medium">
                                {{ $settings['store']['timings'] ?? 'Open daily, closes 9:30 PM' }}
                            </p>
                            <span class="inline-block mt-1 text-[11px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full font-bold">
                                Open 7 Days a Week
                            </span>
                        </div>
                    </div>

                    {{-- Contact Numbers --}}
                    <div class="flex items-start gap-3 text-xs">
                        <div class="p-2.5 rounded-xl bg-festive-teal/15 text-festive-teal flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-bold text-festive-plum text-sm block">Phone & WhatsApp</span>
                            <p class="text-festive-plum/80 leading-relaxed mt-0.5">
                                Phone: {{ $settings['store']['phone'] ?? '+91 98765 43210' }}<br>
                                WhatsApp: {{ $settings['store']['whatsapp'] ?? '+91 98765 43210' }}<br>
                                Email: {{ $settings['store']['email'] ?? 'contact@bombayfashion.com' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- WhatsApp Direct Help Box --}}
                <div class="p-6 rounded-3xl bg-emerald-50 border border-emerald-200 text-xs flex items-center justify-between">
                    <div>
                        <span class="font-bold text-emerald-900 block text-sm">Need Instant Saree / Uniform Help?</span>
                        <p class="text-emerald-700 mt-0.5">Chat directly with the shop owner on WhatsApp.</p>
                    </div>
                    @php
                        $cleanWa = preg_replace('/[^0-9]/', '', $settings['store']['whatsapp'] ?? '919876543210');
                    @endphp
                    <a 
                        href="https://wa.me/{{ $cleanWa }}?text={{ urlencode('Hello Bombay Fashion, I want to inquire about products in your Amritsar store.') }}" 
                        target="_blank"
                        rel="noopener"
                        class="px-4 py-2.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 shadow-sm transition-all flex items-center gap-1.5 flex-shrink-0"
                    >
                        <span>Chat Now</span>
                    </a>
                </div>

            </div>

            {{-- Right: Contact Message Form (7 cols) --}}
            <div class="lg:col-span-7">
                <div class="glass-card rounded-3xl p-6 sm:p-10 border border-white/80 bg-white/85 shadow-md">
                    <h2 class="text-xl font-serif font-bold text-festive-plum pb-3 border-b border-festive-plum/10 mb-6">
                        Send Us a Message
                    </h2>

                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-festive-plum mb-1">Your Full Name *</label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    value="{{ old('name', Auth::user()?->name) }}" 
                                    required 
                                    class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                                    placeholder="e.g. Gurpreet Kaur"
                                >
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-festive-plum mb-1">Email Address *</label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email', Auth::user()?->email) }}" 
                                    required 
                                    class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                                    placeholder="name@example.com"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-festive-plum mb-1">Phone Number (optional)</label>
                                <input 
                                    type="tel" 
                                    name="phone" 
                                    value="{{ old('phone', Auth::user()?->phone) }}" 
                                    class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                                    placeholder="10-digit mobile"
                                >
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-festive-plum mb-1">Subject *</label>
                                <input 
                                    type="text" 
                                    name="subject" 
                                    value="{{ old('subject') }}" 
                                    required 
                                    class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                                    placeholder="e.g. Saree bridal fitting or uniform query"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-festive-plum mb-1">Message *</label>
                            <textarea 
                                name="message" 
                                rows="5" 
                                required 
                                placeholder="Describe your inquiry, order question, or specific requirement..."
                                class="w-full text-xs rounded-xl border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-festive-rose"
                            >{{ old('message') }}</textarea>
                        </div>

                        <button 
                            type="submit" 
                            class="w-full sm:w-auto px-8 py-3.5 rounded-xl font-bold text-xs bg-festive-rose text-white shadow-lg shadow-festive-rose/25 hover:bg-festive-rose/90 hover:scale-[1.01] transition-all"
                        >
                            Send Message to Store
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- Google Maps Embed --}}
        <div class="glass-card rounded-3xl p-4 sm:p-6 border border-white/80 bg-white/85 shadow-md overflow-hidden">
            <h3 class="text-base font-serif font-bold text-festive-plum mb-3 px-2 flex items-center gap-2">
                <svg class="w-5 h-5 text-festive-rose" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                </svg>
                Store Location Map • Mahila College Road, Amritsar
            </h3>
            <div class="rounded-2xl overflow-hidden aspect-video sm:aspect-21/9 border border-festive-plum/10 shadow-inner">
                {!! $settings['store']['google_map_embed'] ?? '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d13586.29479361732!2d74.8624!3d31.6247!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzHCsDM3JzI5LjAiTiA3NMKwNTEnNDQuNiJF!5e0!3m2!1sen!2sin!4v1600000000000!5m2!1sen!2sin" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>' !!}
            </div>
        </div>

    </div>
</div>
@endsection
