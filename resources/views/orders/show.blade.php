@extends('layouts.app')

@section('title', 'Order #' . $order->order_number . ' - Bombay Fashion Amritsar')

@section('content')
@php
    $orderStatus = $order->order_status ?? 'pending';
    $steps = [
        ['key' => 'pending', 'label' => 'Order Placed', 'icon' => 'receipt', 'desc' => 'Order submitted by customer'],
        ['key' => 'confirmed', 'label' => 'Confirmed & Paid', 'icon' => 'check-circle', 'desc' => 'Inventory reserved & payment confirmed'],
        ['key' => 'packed', 'label' => 'Hand Packed', 'icon' => 'package', 'desc' => 'Inspected & packaged with boutique seal'],
        ['key' => 'shipped', 'label' => 'Out for Delivery', 'icon' => 'truck', 'desc' => 'Courier dispatched across Amritsar / Punjab'],
        ['key' => 'delivered', 'label' => 'Delivered', 'icon' => 'gift', 'desc' => 'Received by customer'],
    ];

    $stepKeys = array_column($steps, 'key');
    $currentStatusIndex = array_search($orderStatus, $stepKeys);
    if ($currentStatusIndex === false) {
        $currentStatusIndex = 0;
    }

    $isCancelled = $orderStatus === 'cancelled';
    $isReturned = $orderStatus === 'returned';
    $isDelivered = $orderStatus === 'delivered';
    $isDeliveredRecently = $order->canReturn();

    $progressPercent = $isCancelled || $isReturned ? 0 : round(($currentStatusIndex / (count($steps) - 1)) * 100);
@endphp

<div 
    class="py-8 sm:py-12 bg-gradient-to-b from-festive-ivory/60 via-festive-blush/20 to-festive-ivory/80 min-h-screen"
    x-data="{
        returnModalOpen: false,
        cancelModalOpen: false,
        copiedOrder: false,
        copyOrderNumber(num) {
            navigator.clipboard.writeText(num);
            this.copiedOrder = true;
            setTimeout(() => this.copiedOrder = false, 2500);
        }
    }"
>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Breadcrumb & Quick Back --}}
        <nav class="flex items-center gap-2 text-xs text-festive-plum/60 mb-4 sm:mb-6" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-festive-rose transition-colors">Home</a>
            <span class="text-festive-plum/30">/</span>
            <a href="{{ route('orders.index') }}" class="hover:text-festive-rose transition-colors">My Orders</a>
            <span class="text-festive-plum/30">/</span>
            <span class="text-festive-rose font-medium truncate">{{ $order->order_number }}</span>
        </nav>

        {{-- Hero Order Header Card --}}
        <div class="glass-card rounded-3xl p-5 sm:p-8 border border-white/90 bg-white/85 shadow-xl mb-8 relative overflow-hidden">
            {{-- Festive ambient glow background decoration --}}
            <div class="absolute -top-16 -right-16 w-48 h-48 rounded-full bg-gradient-to-br from-festive-rose/15 to-festive-gold/15 blur-2xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -left-16 w-48 h-48 rounded-full bg-gradient-to-tr from-festive-blush/30 to-festive-lavender/30 blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                
                {{-- Order Meta & Status --}}
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-festive-plum/60 bg-festive-blush/60 px-2.5 py-1 rounded-lg border border-festive-plum/5">
                            Bombay Fashion Boutique Order
                        </span>

                        {{-- Dynamic Status Pill --}}
                        @if($isCancelled)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                Cancelled
                            </span>
                        @elseif($isReturned)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-300">
                                <span class="w-2 h-2 rounded-full bg-gray-500"></span>
                                Returned
                            </span>
                        @elseif($isDelivered)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-xs">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Delivered & Verified
                            </span>
                        @elseif($orderStatus === 'shipped')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200 shadow-xs">
                                <span class="w-2 h-2 rounded-full bg-sky-500 animate-ping"></span>
                                On the Way in Amritsar
                            </span>
                        @elseif($orderStatus === 'packed')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                Packed & Ready
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                                Confirmed & Processing
                            </span>
                        @endif
                    </div>

                    {{-- Order Title with Copy Action --}}
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-serif font-bold text-festive-plum tracking-tight">
                            #{{ $order->order_number }}
                        </h1>
                        <button 
                            type="button" 
                            @click="copyOrderNumber('{{ $order->order_number }}')"
                            class="p-1.5 rounded-lg border border-festive-plum/15 text-festive-plum/60 hover:text-festive-rose hover:border-festive-rose/50 bg-white transition-all shadow-xs flex items-center gap-1 text-xs font-semibold cursor-pointer"
                            title="Copy Order ID"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span x-text="copiedOrder ? 'Copied! ✓' : 'Copy'"></span>
                        </button>
                    </div>

                    {{-- Timestamps & Notice --}}
                    <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-xs text-festive-plum/70">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-festive-rose" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Placed on {{ $order->created_at->format('d M Y, h:i A') }}
                        </span>
                        <span class="text-festive-plum/20 hidden sm:inline">•</span>
                        <span class="flex items-center gap-1 text-emerald-700 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Mahila College Road Store Verified
                        </span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 shrink-0">
                    {{-- Invoice Download --}}
                    <a 
                        href="{{ route('orders.invoice', $order->order_number) }}" 
                        class="px-4 py-3 rounded-xl text-xs font-bold bg-gradient-to-r from-festive-rose to-primary-700 text-white shadow-md shadow-festive-rose/25 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Download Tax Invoice (PDF)</span>
                    </a>

                    {{-- WhatsApp Support --}}
                    @php
                        $waText = urlencode("Hello Bombay Fashion, I have a query regarding my Order #{$order->order_number} ({$order->customer_name}). Please help!");
                    @endphp
                    <a 
                        href="https://wa.me/919876543210?text={{ $waText }}" 
                        target="_blank" 
                        rel="noopener"
                        class="px-3.5 py-3 rounded-xl text-xs font-bold bg-emerald-600 text-white shadow-md hover:bg-emerald-700 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-1.5 cursor-pointer"
                        title="Chat with Amritsar Store"
                    >
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.071.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/></svg>
                        <span>WhatsApp Help</span>
                    </a>

                    {{-- Cancel Button if Pending/Confirmed --}}
                    @if($order->canCancel())
                        <button 
                            type="button" 
                            @click="cancelModalOpen = true"
                            class="px-3.5 py-3 rounded-xl text-xs font-semibold border-2 border-rose-300 text-rose-700 bg-rose-50/50 hover:bg-rose-100 transition-all cursor-pointer"
                        >
                            Cancel
                        </button>
                    @endif

                    {{-- Return Button if eligible --}}
                    @if($isDeliveredRecently)
                        <button 
                            type="button" 
                            @click="returnModalOpen = true"
                            class="px-3.5 py-3 rounded-xl text-xs font-semibold border-2 border-festive-plum/30 text-festive-plum bg-white hover:bg-festive-blush transition-all cursor-pointer"
                        >
                            Return / Exchange
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Order Status Timeline Card --}}
        @if(!$isCancelled && !$isReturned)
            <div class="glass-card rounded-3xl p-5 sm:p-8 border border-white/80 bg-white/95 shadow-xl mb-8 overflow-hidden relative">
                {{-- Decorative ambient background gradient --}}
                <div class="absolute -top-20 -right-20 w-60 h-60 rounded-full bg-gradient-to-br from-festive-rose/10 via-festive-gold/10 to-transparent blur-3xl pointer-events-none"></div>

                {{-- Header Bar --}}
                <div class="flex flex-wrap items-center justify-between gap-4 pb-5 mb-6 border-b border-festive-plum/10 relative z-10">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-festive-rose/10 text-festive-rose font-bold text-xs">
                                📍
                            </span>
                            <h2 class="text-base sm:text-lg font-serif font-bold text-festive-plum tracking-tight">
                                Live Delivery Progress
                            </h2>
                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-gradient-to-r from-festive-rose to-primary-700 text-white shadow-xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                {{ ucfirst($orderStatus) }}
                            </span>
                        </div>
                        <p class="text-xs text-festive-plum/70 pl-9">
                            Track your boutique garments from our Amritsar showroom to your doorstep
                        </p>
                    </div>

                    {{-- ETA / Tracking Info Badge --}}
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        @if($order->tracking_number)
                            <div class="flex items-center gap-2 bg-festive-blush/60 px-3.5 py-1.5 rounded-xl border border-festive-plum/10 shadow-xs">
                                <span class="text-festive-plum/60 font-medium">Tracking AWB:</span>
                                <span class="font-mono font-bold text-festive-rose tracking-wider">{{ $order->tracking_number }}</span>
                                @if($order->courier_name)
                                    <span class="text-[11px] text-festive-plum/70 font-semibold">({{ $order->courier_name }})</span>
                                @endif
                            </div>
                        @else
                            <div class="flex items-center gap-2 bg-emerald-50 text-emerald-800 px-3.5 py-1.5 rounded-xl border border-emerald-200 shadow-xs">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="font-bold">Amritsar Showroom Dispatch Active</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- DESKTOP & TABLET: Sleek Horizontal Animated Timeline (hidden on mobile) --}}
                <div class="hidden md:block py-6 px-4 relative z-10">
                    {{-- Progress Bar Rail with animated vehicle/marker --}}
                    <div class="relative mb-8" style="margin-left: 28px; margin-right: 28px;">
                        {{-- Background Inactive Track --}}
                        <div class="h-2 rounded-full bg-festive-plum/10 w-full"></div>
                        
                        {{-- Filled Glowing Gradient Track --}}
                        <div 
                            class="absolute top-0 left-0 h-2 rounded-full bg-gradient-to-r from-festive-rose via-festive-gold to-emerald-500 transition-all duration-1000 ease-out shadow-xs"
                            style="width: {{ $progressPercent }}%;"
                        ></div>

                        {{-- Floating Delivery Truck / Status Beacon --}}
                        <div 
                            class="absolute -top-3 transition-all duration-1000 ease-out flex flex-col items-center pointer-events-none"
                            style="left: {{ $progressPercent }}%; transform: translateX(-50%);"
                        >
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold text-white bg-festive-plum shadow-md whitespace-nowrap mb-1">
                                @if($isDelivered) Arrived ✓ @elseif($orderStatus === 'shipped') In Transit 🚚 @else In Progress ⚡ @endif
                            </span>
                            <div class="w-7 h-7 rounded-full bg-white border-2 border-festive-rose text-festive-rose flex items-center justify-center shadow-lg text-xs ring-4 ring-festive-rose/20">
                                @if($isDelivered) 🎁 @elseif($orderStatus === 'shipped') 🚚 @elseif($orderStatus === 'packed') 📦 @else 🛍️ @endif
                            </div>
                        </div>
                    </div>

                    {{-- 5 Steps Flex Row (Evenly Distributed) --}}
                    <div class="flex items-start justify-between gap-2 relative">
                        @foreach($steps as $idx => $step)
                            @php
                                $isCompleted = $idx < $currentStatusIndex;
                                $isActive = $idx === $currentStatusIndex;
                                $isUpcoming = $idx > $currentStatusIndex;
                            @endphp
                            
                            <div class="flex flex-col items-center text-center flex-1 max-w-[180px]">
                                {{-- Node Circle --}}
                                <div class="relative mb-3 flex items-center justify-center">
                                    @if($isActive)
                                        <div class="absolute -inset-2 rounded-2xl bg-festive-rose/20 animate-ping"></div>
                                    @endif

                                    <div 
                                        class="w-11 h-11 rounded-2xl flex items-center justify-center font-bold text-xs transition-all duration-300 shadow-md {{ $isActive ? 'bg-gradient-to-tr from-festive-rose to-primary-700 text-white ring-4 ring-festive-rose/30 scale-110 shadow-festive-rose/30' : ($isCompleted ? 'bg-emerald-600 text-white shadow-emerald-600/20' : 'bg-white border-2 border-festive-plum/15 text-festive-plum/40') }}"
                                    >
                                        @if($isCompleted)
                                            <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @elseif($isActive)
                                            @if($step['key'] === 'delivered')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            @elseif($step['key'] === 'shipped')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                            @elseif($step['key'] === 'packed')
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @endif
                                        @else
                                            <span>{{ $idx + 1 }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Node Content --}}
                                <h4 class="text-xs font-bold leading-tight {{ $isActive ? 'text-festive-rose font-serif text-sm' : ($isCompleted ? 'text-festive-plum font-semibold' : 'text-festive-plum/40') }}">
                                    {{ $step['label'] }}
                                </h4>
                                <p class="text-[11px] text-festive-plum/65 mt-1 leading-normal">
                                    {{ $step['desc'] }}
                                </p>
                                @if($step['key'] === 'delivered' && $order->delivered_at)
                                    <span class="inline-block mt-1.5 text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                        {{ $order->delivered_at->format('d M, h:i A') }}
                                    </span>
                                @elseif($step['key'] === 'pending' && $order->created_at)
                                    <span class="inline-block mt-1.5 text-[10px] text-festive-plum/60 font-medium">
                                        {{ $order->created_at->format('d M, h:i A') }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- MOBILE: Dedicated Connected Vertical Timeline (Clean, readable, no overflow) --}}
                <div class="block md:hidden py-2 relative z-10">
                    <div class="relative pl-6 space-y-6 before:absolute before:left-[19px] before:top-3 before:bottom-3 before:w-0.5 before:bg-festive-plum/15">
                        @foreach($steps as $idx => $step)
                            @php
                                $isCompleted = $idx < $currentStatusIndex;
                                $isActive = $idx === $currentStatusIndex;
                                $isUpcoming = $idx > $currentStatusIndex;
                            @endphp

                            <div class="relative flex items-start gap-4">
                                {{-- Node Icon on the left line --}}
                                <div class="relative -ml-6 flex items-center justify-center shrink-0">
                                    @if($isActive)
                                        <div class="absolute -inset-1 rounded-full bg-festive-rose/30 animate-ping"></div>
                                    @endif
                                    <div 
                                        class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-xs transition-all shadow-md {{ $isActive ? 'bg-gradient-to-tr from-festive-rose to-primary-700 text-white ring-4 ring-festive-rose/25' : ($isCompleted ? 'bg-emerald-600 text-white' : 'bg-white border-2 border-festive-plum/20 text-festive-plum/40') }}"
                                    >
                                        @if($isCompleted)
                                            <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @elseif($isActive)
                                            <span class="text-xs">⚡</span>
                                        @else
                                            <span class="text-[11px]">{{ $idx + 1 }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Step Details on the right --}}
                                <div class="flex-1 min-w-0 pt-0.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="text-xs font-bold {{ $isActive ? 'text-festive-rose text-sm font-serif' : ($isCompleted ? 'text-festive-plum' : 'text-festive-plum/40') }}">
                                            {{ $step['label'] }}
                                        </h4>
                                        @if($isActive)
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-festive-rose bg-festive-rose/10 px-2 py-0.5 rounded-full">
                                                Active Stage
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-festive-plum/70 mt-0.5">
                                        {{ $step['desc'] }}
                                    </p>
                                    @if($step['key'] === 'delivered' && $order->delivered_at)
                                        <p class="text-[10px] font-bold text-emerald-700 mt-1">
                                            Delivered at {{ $order->delivered_at->format('d M Y, h:i A') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Status Highlight & Showroom Guarantee Banner --}}
                <div class="mt-6 p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-festive-blush/80 via-white to-amber-50/60 border border-festive-plum/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs shadow-xs relative z-10">
                    <div class="flex items-start sm:items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-festive-rose/15 to-festive-gold/20 text-festive-rose flex items-center justify-center shrink-0 text-lg shadow-xs">
                            🛍️
                        </div>
                        <div class="space-y-0.5">
                            @if($isDelivered)
                                <span class="font-bold text-festive-plum text-sm">Package Delivered Successfully!</span>
                                <p class="text-festive-plum/75 text-[11px]">We hope you cherish your Bombay Fashion garments. Please share your review below!</p>
                            @elseif($orderStatus === 'shipped')
                                <span class="font-bold text-festive-plum text-sm">Out for Delivery in Amritsar</span>
                                <p class="text-festive-plum/75 text-[11px]">Our boutique courier is en route. Contact number on file: <span class="font-bold">{{ $order->shipping_phone }}</span></p>
                            @elseif($orderStatus === 'packed')
                                <span class="font-bold text-festive-plum text-sm">Hand-Packed with Boutique Seal</span>
                                <p class="text-festive-plum/75 text-[11px]">Ready for dispatch from Mahila College Road showroom.</p>
                            @else
                                <span class="font-bold text-festive-plum text-sm">Order Confirmed & Being Prepared</span>
                                <p class="text-festive-plum/75 text-[11px]">Dispatched from Mahila College Road showroom, Amritsar. Estimated delivery in 24-48 hours.</p>
                            @endif
                        </div>
                    </div>

                    <div class="shrink-0 flex sm:flex-col items-center sm:items-end justify-between sm:justify-center border-t sm:border-t-0 pt-2 sm:pt-0 border-festive-plum/10">
                        <span class="text-[11px] font-bold text-festive-plum flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Boutique Guarantee
                        </span>
                        <span class="text-[10px] text-festive-rose font-semibold">100% Genuine Fabrics</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- 3-Column Info Cards (Delivery Address, Payment, Heritage Shop) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 text-xs">
            
            {{-- Delivery Address Card --}}
            <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-md flex flex-col justify-between hover:shadow-lg transition-all duration-300">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-festive-plum/10 mb-3">
                        <h3 class="font-serif font-bold text-festive-plum text-sm flex items-center gap-2">
                            <span class="p-1 rounded-md bg-festive-rose/10 text-festive-rose">📍</span>
                            <span>Delivery Address</span>
                        </h3>
                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Verified
                        </span>
                    </div>
                    <div class="space-y-1.5 text-festive-plum/80 leading-relaxed">
                        <p class="font-bold text-festive-plum text-sm">{{ $order->shipping_name }}</p>
                        <p>{{ $order->shipping_address_line1 }}</p>
                        @if($order->shipping_address_line2)
                            <p>{{ $order->shipping_address_line2 }}</p>
                        @endif
                        <p class="font-medium">{{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}</p>
                        <p class="flex items-center gap-1.5 pt-1 text-festive-plum font-semibold">
                            <svg class="w-3.5 h-3.5 text-festive-rose" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $order->shipping_phone }}
                        </p>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-festive-plum/5 text-[11px] text-festive-plum/60 flex items-center gap-1">
                    <span>Direct Amritsar doorstep dispatch</span>
                </div>
            </div>

            {{-- Payment Information Card --}}
            <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-md flex flex-col justify-between hover:shadow-lg transition-all duration-300">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-festive-plum/10 mb-3">
                        <h3 class="font-serif font-bold text-festive-plum text-sm flex items-center gap-2">
                            <span class="p-1 rounded-md bg-festive-gold/20 text-festive-gold">💳</span>
                            <span>Payment Summary</span>
                        </h3>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>

                    <div class="space-y-2 text-festive-plum/80">
                        <div class="flex justify-between items-center">
                            <span class="text-festive-plum/60">Method:</span>
                            <span class="font-bold text-festive-plum uppercase bg-festive-blush/40 px-2 py-0.5 rounded border border-festive-plum/5">
                                {{ $order->payment_method === 'cod' ? 'Cash on Delivery (COD)' : strtoupper($order->payment_method) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-festive-plum/60">Amount Paid:</span>
                            <span class="font-serif font-bold text-festive-rose text-sm">
                                ₹{{ number_format($order->grand_total, 2) }}
                            </span>
                        </div>

                        @if($order->payment && $order->payment->razorpay_payment_id)
                            <div class="pt-2 border-t border-festive-plum/10">
                                <span class="text-festive-plum/50 text-[10px] block">Razorpay Transaction ID:</span>
                                <span class="font-mono text-[11px] font-semibold text-festive-plum truncate block select-all">{{ $order->payment->razorpay_payment_id }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-festive-plum/5 text-[11px] text-emerald-700 flex items-center gap-1 font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>256-Bit Encrypted & Verified</span>
                </div>
            </div>

            {{-- Bombay Fashion Boutique Heritage Card --}}
            <div class="glass-card rounded-2xl p-5 border border-white/80 bg-white/85 shadow-md flex flex-col justify-between hover:shadow-lg transition-all duration-300">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-festive-plum/10 mb-3">
                        <h3 class="font-serif font-bold text-festive-plum text-sm flex items-center gap-2">
                            <span class="p-1 rounded-md bg-festive-lavender/30 text-festive-plum">🏬</span>
                            <span>Store & Fitting Care</span>
                        </h3>
                        <span class="text-[10px] font-bold text-festive-gold bg-festive-gold/10 px-2 py-0.5 rounded-full border border-festive-gold/20">
                            Since 1998
                        </span>
                    </div>

                    <div class="space-y-1.5 text-festive-plum/80 leading-relaxed">
                        <p class="font-bold text-festive-plum">Bombay Fashion Amritsar</p>
                        <p class="text-[11px] text-festive-plum/70">Opposite Mahila College, Mahila College Road, Amritsar, Punjab, India</p>
                        <p class="text-[11px] text-festive-plum/70">Open Daily • Closes 9:30 PM</p>
                        
                        @if($order->customer_notes)
                            <div class="mt-2 p-2 rounded-lg bg-festive-blush/30 border border-festive-plum/5 text-[11px] italic">
                                Note: "{{ $order->customer_notes }}"
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-festive-plum/5 text-[11px] text-festive-rose font-semibold flex items-center gap-1">
                    <span>✨ Complimentary trial alteration at store</span>
                </div>
            </div>

        </div>

        {{-- Order Items Table Card --}}
        <div class="glass-card rounded-3xl p-6 sm:p-8 border border-white/80 bg-white/90 shadow-xl mb-8">
            <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-festive-plum/10 mb-6">
                <div>
                    <h3 class="font-serif font-bold text-festive-plum text-lg sm:text-xl">
                        Garments in this Order
                    </h3>
                    <p class="text-xs text-festive-plum/60">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }} total</p>
                </div>
                <div class="text-xs text-festive-plum/70">
                    Need styling advice? <a href="{{ route('contact') }}" class="text-festive-rose hover:underline font-bold">Contact Tailor</a>
                </div>
            </div>

            <div class="divide-y divide-festive-plum/10">
                @foreach($order->items as $item)
                    <div class="py-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 group hover:bg-festive-blush/10 p-3 rounded-2xl transition-all">
                        
                        {{-- Image & Product Meta --}}
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            {{-- Product Thumbnail --}}
                            <div class="w-16 h-20 sm:w-20 sm:h-24 rounded-2xl bg-festive-blush/30 overflow-hidden border border-festive-plum/10 shrink-0 shadow-xs group-hover:scale-105 transition-transform duration-300">
                                @if($item->product && $item->product->primaryImage)
                                    <img src="{{ $item->product->primaryImage->image_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                @elseif($item->product && $item->product->main_image_url)
                                    <img src="{{ $item->product->main_image_url }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-festive-plum/40 font-serif font-bold text-xs">
                                        <span>BF</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Title, Size & Options --}}
                            <div class="min-w-0 flex-1">
                                <h4 class="font-serif font-bold text-festive-plum text-sm sm:text-base leading-snug">
                                    @if($item->product)
                                        <a href="{{ route('products.show', $item->product->slug) }}" class="hover:text-festive-rose transition-colors">
                                            {{ $item->product_name }}
                                        </a>
                                    @else
                                        {{ $item->product_name }}
                                    @endif
                                </h4>

                                <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                    @if($item->variant)
                                        @if($item->variant->size)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-festive-rose/10 text-festive-rose border border-festive-rose/20">
                                                Size: {{ $item->variant->size }}
                                            </span>
                                        @endif
                                        @if($item->variant->color)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-white text-festive-plum border border-festive-plum/15 shadow-xs">
                                                @if($item->variant->color_code)
                                                    <span class="w-2.5 h-2.5 rounded-full border border-black/10 inline-block shrink-0" style="background-color: {{ $item->variant->color_code }}"></span>
                                                @endif
                                                {{ $item->variant->color }}
                                            </span>
                                        @endif
                                    @elseif($item->variant_info)
                                        <span class="text-xs text-festive-plum/60">{{ $item->variant_info }}</span>
                                    @endif

                                    <span class="text-[11px] text-festive-plum/60 bg-gray-50 px-2 py-0.5 rounded border border-gray-200">
                                        Qty: {{ $item->quantity }}
                                    </span>
                                </div>

                                <div class="text-xs text-festive-plum/70 mt-1.5">
                                    Unit Price: <span class="font-semibold text-festive-plum">₹{{ number_format($item->unit_price, 2) }}</span>
                                </div>

                                {{-- Delivered Order Review Action CTA --}}
                                @if($orderStatus === 'delivered' && $item->product)
                                    <div class="mt-3">
                                        <a 
                                            href="{{ route('products.show', $item->product->slug) }}#reviews" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-festive-rose bg-gradient-to-r from-festive-rose/10 to-festive-gold/15 hover:from-festive-rose/20 hover:to-festive-gold/25 border border-festive-rose/30 shadow-xs hover:scale-105 transition-all cursor-pointer"
                                        >
                                            <span class="text-festive-gold">★</span>
                                            <span>Add Review & Photos &rarr;</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Line Price --}}
                        <div class="text-right sm:pl-4">
                            <span class="text-xs text-festive-plum/50 block sm:hidden">Total:</span>
                            <div class="text-base sm:text-lg font-serif font-bold text-festive-plum">
                                ₹{{ number_format($item->total_price, 2) }}
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>

            {{-- Financial Receipt Breakdown --}}
            <div class="pt-6 mt-6 border-t border-festive-plum/10 flex flex-col items-end gap-2 text-xs text-festive-plum/80">
                <div class="w-full sm:w-72 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-festive-plum/70">Subtotal:</span>
                        <span class="font-bold text-festive-plum">₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>

                    @if($order->discount_amount > 0)
                        <div class="flex justify-between items-center text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                            <span class="font-medium">Festive Savings:</span>
                            <span class="font-bold">-₹{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between">
                        <span class="text-festive-plum/70">GST (Taxes Included):</span>
                        <span class="font-bold text-festive-plum">₹{{ number_format($order->tax_amount, 2) }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-festive-plum/70">Doorstep Delivery:</span>
                        @if($order->shipping_amount <= 0)
                            <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-[11px]">FREE Delivery</span>
                        @else
                            <span class="font-bold text-festive-plum">₹{{ number_format($order->shipping_amount, 2) }}</span>
                        @endif
                    </div>

                    <div class="pt-3 border-t-2 border-festive-plum/10 flex justify-between items-baseline text-sm">
                        <span class="font-bold text-festive-plum">Grand Total:</span>
                        <span class="font-serif text-xl sm:text-2xl font-bold text-festive-rose">
                            ₹{{ number_format($order->grand_total, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Cancel Order Modal --}}
    <div 
        x-show="cancelModalOpen" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-festive-plum/60 backdrop-blur-md"
        @click.self="cancelModalOpen = false"
        @keydown.escape.window="cancelModalOpen = false"
    >
        <div 
            x-show="cancelModalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="glass-card rounded-3xl p-6 sm:p-8 max-w-md w-full bg-white shadow-2xl border border-white"
        >
            <div class="flex items-center justify-between pb-3 border-b border-festive-plum/10 mb-4">
                <h3 class="text-lg font-serif font-bold text-festive-plum">Cancel Order #{{ $order->order_number }}</h3>
                <button @click="cancelModalOpen = false" class="text-festive-plum/40 hover:text-festive-rose font-bold text-xl cursor-pointer">&times;</button>
            </div>
            
            <p class="text-xs text-festive-plum/70 mb-4 leading-relaxed">
                Are you sure you want to cancel this order? Any reserved inventory will be restocked to our Amritsar boutique immediately.
            </p>
            
            <form action="{{ route('orders.cancel', $order->order_number) }}" method="POST">
                @csrf
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-festive-plum mb-1.5">Reason for Cancellation *</label>
                    <textarea 
                        name="reason" 
                        rows="3" 
                        required 
                        placeholder="Please share why you wish to cancel this order..." 
                        class="w-full text-xs rounded-xl border border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-2 focus:ring-festive-rose focus:border-transparent transition-all"
                    ></textarea>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button 
                        type="button" 
                        @click="cancelModalOpen = false" 
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold border border-festive-plum/20 text-festive-plum hover:bg-festive-blush transition-colors cursor-pointer"
                    >
                        Keep Order
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2.5 rounded-xl text-xs font-bold bg-rose-600 text-white hover:bg-rose-700 shadow-md transition-all cursor-pointer"
                    >
                        Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Return / Exchange Modal --}}
    <div 
        x-show="returnModalOpen" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-festive-plum/60 backdrop-blur-md"
        @click.self="returnModalOpen = false"
        @keydown.escape.window="returnModalOpen = false"
    >
        <div 
            x-show="returnModalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="glass-card rounded-3xl p-6 sm:p-8 max-w-md w-full bg-white shadow-2xl border border-white"
        >
            <div class="flex items-center justify-between pb-3 border-b border-festive-plum/10 mb-4">
                <h3 class="text-lg font-serif font-bold text-festive-plum">Return or Exchange Request</h3>
                <button @click="returnModalOpen = false" class="text-festive-plum/40 hover:text-festive-rose font-bold text-xl cursor-pointer">&times;</button>
            </div>
            
            <p class="text-xs text-festive-plum/70 mb-4 leading-relaxed">
                Eligible within 7 days of delivery. Our master tailors in Amritsar can also perform quick size adjustments!
            </p>
            
            <form action="{{ route('orders.return', $order->order_number) }}" method="POST">
                @csrf
                <div class="space-y-3 mb-5">
                    <div>
                        <label class="block text-xs font-semibold text-festive-plum mb-1">Primary Reason *</label>
                        <select name="reason" required class="w-full text-xs rounded-xl border border-festive-plum/20 bg-white p-2.5 text-festive-plum focus:ring-2 focus:ring-festive-rose">
                            <option value="Size does not fit">Size does not fit (Exchange needed)</option>
                            <option value="Color or fabric different from expectation">Color or fabric different from expectation</option>
                            <option value="Damaged or defective item received">Damaged or defective item received</option>
                            <option value="Wrong item delivered">Wrong item delivered</option>
                            <option value="Changed mind">Changed mind</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-festive-plum mb-1">Details & Preferred Exchange Size</label>
                        <textarea 
                            name="details" 
                            rows="3" 
                            placeholder="Specify preferred size, color, or issue details..." 
                            class="w-full text-xs rounded-xl border border-festive-plum/20 bg-white p-3 text-festive-plum focus:ring-2 focus:ring-festive-rose"
                        ></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <button 
                        type="button" 
                        @click="returnModalOpen = false" 
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold border border-festive-plum/20 text-festive-plum hover:bg-festive-blush cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="px-5 py-2.5 rounded-xl text-xs font-bold bg-festive-rose text-white hover:bg-festive-rose/90 shadow-md cursor-pointer"
                    >
                        Submit Return Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
