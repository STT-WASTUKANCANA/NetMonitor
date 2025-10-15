<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Monitoring System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Icons -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">
    
    <!-- Framer Motion for animations -->
    <script src="https://unpkg.com/framer-motion@10.16.4/dist/framer-motion.js"></script>
</head>

<body class="font-sans antialiased bg-gradient-to-br from-gray-900 to-gray-800 text-gray-100 min-h-screen flex flex-col items-center justify-center p-4 transition-colors duration-300">
    <!-- Animated Background -->
    <div class="fixed inset-0 z-0 overflow-hidden">
        <div class="absolute -top-48 -left-48 w-96 h-96 bg-purple-600 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
        <div class="absolute -bottom-48 -right-48 w-96 h-96 bg-blue-600 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-2000"></div>
        <div class="absolute top-48 left-1/2 w-96 h-96 bg-indigo-600 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-4000"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 w-full max-w-md">
        <!-- Logo -->
        <div class="flex justify-center mb-8 animate-fade-in">
            <div class="bg-gray-800 p-4 rounded-2xl shadow-xl border border-gray-700">
                <a href="/" class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-purple-600 to-blue-500 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 640 640">
                            <path fill="currentColor" d="M320 160C229.1 160 146.8 196 86.3 254.6C73.6 266.9 53.3 266.6 41.1 253.9C28.9 241.2 29.1 220.9 41.8 208.7C113.7 138.9 211.9 96 320 96C428.1 96 526.3 138.9 598.3 208.7C611 221 611.3 241.3 599 253.9C586.7 266.5 566.4 266.9 553.8 254.6C493.2 196 410.9 160 320 160zM272 496C272 469.5 293.5 448 320 448C346.5 448 368 469.5 368 496C368 522.5 346.5 544 320 544C293.5 544 272 522.5 272 496zM200 390.2C188.3 403.5 168.1 404.7 154.8 393C141.5 381.3 140.3 361.1 152 347.8C193 301.4 253.1 272 320 272C386.9 272 447 301.4 488 347.8C499.7 361.1 498.4 381.3 485.2 393C472 404.7 451.7 403.4 440 390.2C410.6 356.9 367.8 336 320 336C272.2 336 229.4 356.9 200 390.2z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-400 to-blue-500">
                        NetMonitor
                    </span>
                </a>
            </div>
        </div>

        <!-- Card -->
        <div class="w-full bg-gray-800/80 backdrop-blur-lg border border-gray-700 rounded-2xl shadow-2xl overflow-hidden backdrop-filter transition-all duration-300 hover:shadow-2xl animate-slide-up">
            {{ $slot }}
        </div>
    </div>

    <!-- Custom animations -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Add fade-in animation to elements
        const fadeElements = document.querySelectorAll('.animate-fade-in');
        fadeElements.forEach((el, index) => {
          el.style.opacity = 0;
          el.style.transform = 'translateY(10px)';
          el.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
          setTimeout(() => {
            el.style.opacity = 1;
            el.style.transform = 'translateY(0)';
          }, index * 100);
        });
        
        // Add slide-up animation to card
        const card = document.querySelector('.animate-slide-up');
        if (card) {
          card.style.opacity = 0;
          card.style.transform = 'translateY(20px)';
          setTimeout(() => {
            card.style.opacity = 1;
            card.style.transform = 'translateY(0)';
          }, 200);
        }
      });
    </script>
</body>
</html>