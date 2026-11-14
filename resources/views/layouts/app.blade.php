<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', \App\Models\Setting::get('store_name', 'Bombay Fashion') . ' | Amritsar Clothing Store')</title>
    <meta name="description" content="@yield('meta_description', 'Bombay Fashion - Amritsar\'s leading store for Sarees, Punjabi Suits, Men\'s Kurtas, Kids Wear, and School Uniforms on Mahila College Road.')">

    <!-- Google Fonts: Playfair Display + Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,400&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-[#FFF9F3] text-plum antialiased selection:bg-primary-100 selection:text-primary-800 min-h-screen flex flex-col relative overflow-x-hidden">
    
    <!-- Decorative Ambient Blurred Gradient Blobs for Depth (Light Theme Only) -->
    <div class="fixed top-0 left-0 w-96 h-96 bg-primary-200/25 rounded-full filter blur-3xl pointer-events-none -z-10 -translate-x-1/2 -translate-y-1/2"></div>
    <div class="fixed top-1/3 right-0 w-96 h-96 bg-gold-200/20 rounded-full filter blur-3xl pointer-events-none -z-10 translate-x-1/3"></div>
    <div class="fixed bottom-0 left-1/4 w-96 h-96 bg-blush/40 rounded-full filter blur-3xl pointer-events-none -z-10"></div>

    <!-- Header -->
    @include('layouts.partials.header')

    <!-- Main Content Area -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.partials.footer')

    <!-- Slide-over Cart Drawer -->
    @include('layouts.partials.cart-drawer')

    <!-- Toast Notifications -->
    @include('layouts.partials.toast')

    <!-- WhatsApp Click-to-Chat Button -->
    @include('layouts.partials.whatsapp-button')

    <!-- Mobile Bottom Navigation Bar -->
    @include('layouts.partials.mobile-nav')

    <!-- Back to Top Button -->
    <div x-data="{ showTop: false }"
         @scroll.window="showTop = (window.pageYOffset > 400)"
         class="fixed bottom-36 md:bottom-22 right-4 md:right-6 z-30">
        <button x-show="showTop"
                x-transition
                @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                class="p-2.5 sm:p-3 rounded-full bg-white/90 text-plum/70 hover:text-primary-600 shadow-md hover:shadow-lg border border-primary-100 transition"
                aria-label="Back to top">
            <svg class="w-5 h-5 shrink-0" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
        </button>
    </div>

    @stack('scripts')
</body>
</html>
