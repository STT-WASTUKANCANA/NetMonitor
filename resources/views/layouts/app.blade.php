<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth h-full">

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

    <!-- Icons -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="font-sans antialiased bg-white text-gray-900 h-full flex flex-col">
    <div class="flex flex-col flex-1 min-h-screen">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @hasSection('title')
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                @yield('title')
                            </h2>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Quick Stats -->
                            @auth
                                <div class="hidden md:flex items-center space-x-4 text-sm">
                                    <div class="flex items-center text-green-600">
                                        <div class="w-2 h-2 rounded-full bg-green-500 mr-1"></div>
                                        <span>{{ \App\Models\Device::where('status', 'up')->count() }}</span>
                                    </div>
                                    <div class="flex items-center text-red-600">
                                        <div class="w-2 h-2 rounded-full bg-green-500 mr-1"></div>
                                        <span>{{ \App\Models\Device::where('status', 'down')->count() }}</span>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>
        @elseif(isset($header) && $header)
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between">
                        <div>
                            {{ $header }}
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Quick Stats -->
                            @auth
                                <div class="hidden md:flex items-center space-x-4 text-sm">
                                    <div class="flex items-center text-green-600">
                                        <div class="w-2 h-2 rounded-full bg-green-500 mr-1"></div>
                                        <span>{{ \App\Models\Device::where('status', 'up')->count() }}</span>
                                    </div>
                                    <div class="flex items-center text-red-600">
                                        <div class="w-2 h-2 rounded-full bg-red-500 mr-1"></div>
                                        <span>{{ \App\Models\Device::where('status', 'down')->count() }}</span>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="py-6 flex-grow">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </div>
        </main>

        {{-- <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="flex justify-center md:justify-start">
                        <div class="flex items-center">
                            <x-application-logo
                                class="block h-8 w-auto fill-current text-gray-800" />
                            <span class="ml-2 text-lg font-semibold text-gray-800">
                                Monitoring System
                            </span>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0 md:order-1">
                        <p class="text-center text-sm text-gray-500">
                            &copy; {{ date('Y') }} STT Wastukancana. All rights reserved.
                        </p>
                    </div>
                </div>
            </div>
        </footer> --}}
    </div>

    <!-- Global Toast Notifications -->
    <div id="toast-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 space-y-2"></div>

    <script>
        // Toast notification function
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `max-w-md w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 transition duration-300 ease-in-out transform translate-y-0 opacity-100`;
            toast.innerHTML = `
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                ${type === 'success' ?
                    '<svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' :
                    (type === 'info' ?
                    '<svg class="h-6 w-6 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' :
                    '<svg class="h-6 w-6 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>')
                }
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-gray-900">${message}</p>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex">
                                <button onclick="this.parentElement.parentElement.parentElement.parentElement.remove()" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            container.appendChild(toast);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 5000);
        }

        // Show flash messages
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    </script>
</body>

</html>