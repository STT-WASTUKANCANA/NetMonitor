<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth h-full dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Monitoring Konektivitas Jaringan STT Wastukancana">
    <meta name="author" content="STT Wastukancana IT Department">

    <title>{{ config('app.name', 'Monitoring System') }} @yield('title', 'Sistem Monitoring Konektivitas Jaringan STT Wastukancana')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')

    <!-- Icons -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    
    <!-- Chart.js for reports -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Framer Motion for animations -->
    <script src="https://unpkg.com/framer-motion@10.16.4/dist/framer-motion.js"></script>
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 h-full flex flex-col transition-colors duration-300">
    <div class="flex flex-col flex-1 min-h-screen">
        @include('layouts.navigation')

        <main class="flex-1 pb-16">
            <!-- Page Heading -->
            @hasSection('header')
                <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 transition-colors duration-300">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <div class="py-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {{ $slot ?? $content ?? $__env->yieldContent('content') }}
                </div>
            </div>
        </main>
    </div>

    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom animations -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Initialize animations
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.style.opacity = 1;
              entry.target.style.transform = 'translateY(0)';
            }
          });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
          el.style.opacity = 0;
          el.style.transform = 'translateY(20px)';
          el.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
          observer.observe(el);
        });
      });
    </script>
</body>
</html>