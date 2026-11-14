@extends('layouts.app')

@section('title', ($page->title ?? 'Store Information') . ' - Bombay Fashion Amritsar')

@section('content')
<div class="py-10 sm:py-14 bg-festive-ivory/50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        {{-- Breadcrumb --}}
        <nav class="flex text-xs text-festive-plum/60 overflow-hidden" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-1.5 text-[11px] sm:text-xs">
                <li><a href="{{ route('home') }}" class="hover:text-primary transition-colors">Home</a></li>
                <li><span class="text-festive-plum/30">/</span></li>
                <li><span class="text-festive-plum/50">Store Policies</span></li>
                <li><span class="text-festive-plum/30">/</span></li>
                <li class="text-primary font-semibold truncate max-w-[200px] sm:max-w-none">{{ $page->title }}</li>
            </ol>
        </nav>

        {{-- Page Header Glass Card --}}
        <div class="glass-card rounded-3xl p-6 sm:p-10 border border-white/90 bg-white/90 shadow-md relative overflow-hidden">
            <div class="absolute -right-8 -top-8 w-36 h-36 bg-primary-100/40 rounded-full blur-2xl pointer-events-none"></div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold tracking-wider bg-primary-100 text-primary-700">
                            Verified Store Policy
                        </span>
                        <span class="text-[11px] text-festive-plum/50">
                            Updated: {{ $page->updated_at?->format('F d, Y') ?? date('F d, Y') }}
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-serif font-bold text-festive-plum">
                        {{ $page->title }}
                    </h1>
                    <p class="text-xs sm:text-sm text-festive-plum/70 max-w-2xl leading-relaxed">
                        @if($page->slug === 'privacy-policy')
                            Transparent guidelines on how Bombay Fashion Amritsar collects, uses, protects, and respects your personal, transaction, and sizing data.
                        @elseif($page->slug === 'terms-conditions')
                            Clear, customer-centric terms governing orders, fabrics, Cash on Delivery, custom stitching, 7-day returns, and showroom services.
                        @else
                            {{ $page->meta_description ?? 'Information, policies, and terms of service from Bombay Fashion Amritsar.' }}
                        @endif
                    </p>
                </div>

                {{-- Action Controls --}}
                <div class="shrink-0 flex sm:flex-col items-center sm:items-end gap-2">
                    <button 
                        type="button" 
                        onclick="window.print()" 
                        class="px-3.5 py-2 rounded-xl border border-plum/20 text-xs font-semibold text-plum hover:bg-plum/5 transition-all flex items-center gap-1.5 cursor-pointer bg-white"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Print Policy</span>
                    </button>
                    <a 
                        href="{{ route('contact') }}" 
                        class="px-3.5 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-700 transition-all shadow-sm flex items-center gap-1.5"
                    >
                        <span>Ask Questions</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Main Body: Point-by-Point Cards or Standard Content --}}
        @if($page->slug === 'privacy-policy')
            @include('pages.partials.privacy-cards')
        @elseif($page->slug === 'terms-conditions')
            @include('pages.partials.terms-cards')
        @else
            <div class="glass-card rounded-3xl p-6 sm:p-10 border border-white/80 bg-white/95 shadow-md">
                <div class="prose max-w-none text-festive-plum/85 leading-relaxed text-sm">
                    {!! $page->content !!}
                </div>
            </div>
        @endif

        {{-- Policy Footer Support Helpline Card --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 bg-gradient-to-r from-cream-100/70 via-white to-primary-50/50 border border-primary-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
            <div class="space-y-1 text-center sm:text-left">
                <h4 class="font-serif font-bold text-sm text-festive-plum">Need help understanding our policy or order process?</h4>
                <p class="text-festive-plum/70">Our Amritsar retail support team is available daily on Mahila College Road.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="https://wa.me/919876543210" target="_blank" class="px-4 py-2.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition flex items-center gap-1.5 shadow-sm">
                    <span>WhatsApp Chat</span>
                </a>
                <a href="tel:+919876543210" class="px-4 py-2.5 rounded-xl bg-white border border-plum/20 text-festive-plum font-bold hover:bg-plum/5 transition">
                    Call: +91 98765 43210
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
