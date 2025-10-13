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
            <header class="bg-white border-b border-gray-100">
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
    <div id="toast-container" class="fixed top-4 right-4 z-[9999] space-y-2 max-w-sm w-full md:max-w-md lg:max-w-lg px-4 pointer-events-none"></div>

    <script>
        // Toast notification function
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            // Determine toast styling based on type
            let bgColor, textColor, iconColor, icon;
            switch(type) {
                case 'success':
                    bgColor = 'bg-green-50 border-green-200';
                    textColor = 'text-green-800';
                    iconColor = 'text-green-500';
                    icon = '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>';
                    break;
                case 'error':
                    bgColor = 'bg-red-50 border-red-200';
                    textColor = 'text-red-800';
                    iconColor = 'text-red-500';
                    icon = '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>';
                    break;
                case 'info':
                case 'warning':
                default:
                    bgColor = 'bg-blue-50 border-blue-200';
                    textColor = 'text-blue-800';
                    iconColor = 'text-blue-500';
                    icon = '<svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>';
                    break;
            }
            
            toast.className = `max-w-full w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 border-l-4 ${bgColor} transform transition-transform duration-300 ease-out translate-x-0 opacity-100`;
            toast.innerHTML = `
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 ${iconColor} self-start">
                            ${icon}
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium ${textColor} break-words">${message}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button onclick="this.closest('.max-w-full').remove()" aria-label="Close" class="inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-md">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Add the toast to the container
            container.appendChild(toast);
            
            // Trigger enter animation
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 10);

            // Auto remove after 5 seconds
            const timeout = setTimeout(() => {
                if (toast.parentElement) {
                    removeToast(toast);
                }
            }, 5000);
            
            // Add click event to dismiss toast when clicked anywhere on it
            toast.addEventListener('click', function(e) {
                if (e.target !== toast && e.target.closest('button') !== null) return;
                removeToast(toast, timeout);
            });
        }
        
        function removeToast(toast, timeout = null) {
            if (timeout) clearTimeout(timeout);
            
            // Apply exit animation
            toast.style.transform = 'translateX(100%)';
            toast.style.opacity = '0';
            
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }

        // Show flash messages
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
        
        @if(session('warning'))
            showToast("{{ session('warning') }}", 'warning');
        @endif
        
        @if(session('info'))
            showToast("{{ session('info') }}", 'info');
        @endif
    </script>
</body>

</html>