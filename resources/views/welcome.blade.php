<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Monitoring Konektivitas') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @php
            $hasBuild = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'));
        @endphp

        @if ($hasBuild)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /* Fallback minimal style */
                body {
                    font-family: 'Instrument Sans', sans-serif;
                    background-color: #f9fafb;
                    color: #111827;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                }
            </style>
        @endif
    </head>

    <body class="bg-gray-50 text-black flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-block px-5 py-1.5 text-black border border-gray-300 hover:border-gray-400 rounded-sm text-sm leading-normal"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="relative inline-flex items-center justify-center px-6 py-2 overflow-hidden font-medium text-gray-800 border border-gray-300 rounded-md shadow-sm transition-all duration-300 ease-out group hover:shadow-lg hover:border-gray-400"
                        >
                            <span
                                class="absolute inset-0 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 translate-x-[-100%] group-hover:translate-x-0 transition-transform duration-500 ease-in-out"
                            ></span>
                            <span
                                class="relative z-10 flex items-center gap-2 font-semibold tracking-wide"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-9A2.25 2.25 0 002.25 5.25v13.5A2.25 2.25 0 004.5 21h9a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                </svg>
                                Log in
                            </span>
                        </a>
                    @endauth
                </nav>
            @endif
        </header>

        <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-700 lg:grow">
            <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
                <!-- Left Panel -->
                <div class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:p-20 bg-white text-black border border-gray-200 rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none">
                    <h1 class="mb-1 font-medium">Monitoring Konektivitas Jaringan</h1>
                    <p class="mb-4 text-gray-600">
                        Sistem monitoring jaringan real-time untuk memantau kesehatan dan performa perangkat jaringan secara real-time.
                    </p>

                    <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium">Status Jaringan</span>
                            <span class="text-sm font-medium text-green-600" id="networkStatus">Memeriksa...</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-green-500 h-2.5 rounded-full" id="statusBar" style="width: 0%"></div>
                        </div>
                        <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                            <div class="bg-green-100 p-2 rounded">
                                <span class="block text-lg font-bold" id="activeDevices">0</span>
                                <span class="text-xs text-gray-600">Aktif</span>
                            </div>
                            <div class="bg-red-100 p-2 rounded">
                                <span class="block text-lg font-bold" id="downDevices">0</span>
                                <span class="text-xs text-gray-600">Tidak Aktif</span>
                            </div>
                            <div class="bg-blue-100 p-2 rounded">
                                <span class="block text-lg font-bold" id="totalDevices">0</span>
                                <span class="text-xs text-gray-600">Total</span>
                            </div>
                        </div>
                    </div>

                    <ul class="flex flex-col mb-4 lg:mb-6">
                        <li class="flex items-center gap-4 py-2 relative before:border-l before:border-gray-300 before:top-1/2 before:bottom-0 before:left-[0.4rem] before:absolute">
                            <span class="relative py-1 bg-white">
                                <span class="flex items-center justify-center rounded-full bg-white shadow w-3.5 h-3.5 border border-gray-300">
                                    <span class="rounded-full bg-gray-400 w-1.5 h-1.5"></span>
                                </span>
                            </span>
                            <span>
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center space-x-1 font-medium underline underline-offset-4 text-red-600">
                                    <span>Dashboard</span>
                                    <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5">
                                        <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square" />
                                    </svg>
                                </a>
                                <span class="text-gray-600"> - Pantau status perangkat jaringan</span>
                            </span>
                        </li>
                        <li class="flex items-center gap-4 py-2 relative before:border-l before:border-gray-300 before:bottom-1/2 before:top-0 before:left-[0.4rem] before:absolute">
                            <span class="relative py-1 bg-white">
                                <span class="flex items-center justify-center rounded-full bg-white shadow w-3.5 h-3.5 border border-gray-300">
                                    <span class="rounded-full bg-gray-400 w-1.5 h-1.5"></span>
                                </span>
                            </span>
                            <span>
                                <a href="{{ route('devices.index') }}" class="inline-flex items-center space-x-1 font-medium underline underline-offset-4 text-red-600">
                                    <span>Manajemen Perangkat</span>
                                    <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5">
                                        <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square" />
                                    </svg>
                                </a>
                                <span class="text-gray-600"> - Kelola perangkat jaringan</span>
                            </span>
                        </li>
                        <li class="flex items-center gap-4 py-2 relative before:border-l before:border-gray-300 before:bottom-1/2 before:top-0 before:left-[0.4rem] before:absolute">
                            <span class="relative py-1 bg-white">
                                <span class="flex items-center justify-center rounded-full bg-white shadow w-3.5 h-3.5 border border-gray-300">
                                    <span class="rounded-full bg-gray-400 w-1.5 h-1.5"></span>
                                </span>
                            </span>
                            <span>
                                <a href="{{ route('reports.index') }}" class="inline-flex items-center space-x-1 font-medium underline underline-offset-4 text-red-600">
                                    <span>Laporan</span>
                                    <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5">
                                        <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square" />
                                    </svg>
                                </a>
                                <span class="text-gray-600"> - Lihat laporan performa jaringan</span>
                            </span>
                        </li>
                    </ul>

                    <ul class="flex gap-3 text-sm leading-normal">
                        <li>
                            <a href="{{ route('dashboard') }}" class="inline-block hover:bg-black hover:border-black px-5 py-1.5 bg-black rounded-sm border border-black text-white text-sm leading-normal">
                                Mulai Monitoring
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Right Panel -->
                <div class="bg-gradient-to-br from-green-50 to-blue-50 relative lg:-ml-px -mb-px lg:mb-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg aspect-[335/376] lg:aspect-auto w-full lg:w-[438px] shrink-0 overflow-hidden">
                    <div class="absolute inset-0 flex items-center justify-center p-6">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-green-100 to-blue-200 mb-4">
                                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800 mb-2">Monitoring Konektivitas</h2>
                            <p class="text-gray-600 text-sm">Sistem pemantauan jaringan real-time</p>
                        </div>
                    </div>
                    <div class="absolute inset-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg border border-gray-200"></div>
                </div>
            </main>
        </div>

        @if (Route::has('login'))
            <div class="h-14 hidden lg:block"></div>
        @endif

        <script>
            async function fetchMonitorData() {
                try {
                    const response = await fetch('/api/devices/stats');
                    const data = await response.json();

                    const totalDevices = data.total_devices || 0;
                    const activeDevices = data.active_devices || 0;
                    const downDevices = data.down_devices || 0;

                    document.getElementById('totalDevices').textContent = totalDevices;
                    document.getElementById('activeDevices').textContent = activeDevices;
                    document.getElementById('downDevices').textContent = downDevices;

                    const statusPercentage = totalDevices > 0 ? (activeDevices / totalDevices * 100) : 0;
                    document.getElementById('statusBar').style.width = statusPercentage + '%';

                    const statusLabel = document.getElementById('networkStatus');
                    if (totalDevices === 0) {
                        statusLabel.textContent = 'Tidak Ada Perangkat';
                        statusLabel.className = 'text-sm font-medium text-gray-500';
                    } else if (downDevices === 0) {
                        statusLabel.textContent = 'Semua Aktif';
                        statusLabel.className = 'text-sm font-medium text-green-600';
                    } else if (activeDevices === 0) {
                        statusLabel.textContent = 'Semua Tidak Aktif';
                        statusLabel.className = 'text-sm font-medium text-red-600';
                    } else {
                        statusLabel.textContent = 'Sebagian Aktif';
                        statusLabel.className = 'text-sm font-medium text-yellow-600';
                    }
                } catch (error) {
                    console.error('Error fetching monitoring data:', error);
                    const statusLabel = document.getElementById('networkStatus');
                    statusLabel.textContent = 'Error Koneksi';
                    statusLabel.className = 'text-sm font-medium text-red-600';
                }
            }

            fetchMonitorData();
            setInterval(fetchMonitorData, 30000);
        </script>
    </body>
</html>
