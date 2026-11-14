<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Panel | Bombay Fashion Amritsar')</title>

    <!-- Google Fonts: Playfair Display + Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body 
    x-data="{ 
        sidebarOpen: window.innerWidth >= 768,
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        }
    }" 
    class="bg-[#FFF9F3] text-plum antialiased selection:bg-primary-100 selection:text-primary-800 min-h-screen flex relative"
>
    
    <!-- Decorative Ambient Blobs (Light Palette Only) -->
    <div class="fixed top-0 right-0 w-96 h-96 bg-primary-100/30 rounded-full filter blur-3xl pointer-events-none -z-10"></div>
    <div class="fixed bottom-0 left-1/3 w-96 h-96 bg-gold-100/30 rounded-full filter blur-3xl pointer-events-none -z-10"></div>

    <!-- Sidebar -->
    @include('layouts.partials.admin-sidebar')

    <!-- Main Content Column -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Topbar Header -->
        @include('layouts.partials.admin-header')

        <!-- Main Page Body -->
        <main class="flex-1 p-6 sm:p-8">
            @yield('content')
        </main>
    </div>

    <!-- Toast Notifications -->
    @include('layouts.partials.toast')

    @stack('scripts')
</body>
</html>
