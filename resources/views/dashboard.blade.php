<x-app-layout>
    <x-slot name="header">
        <div class="flex items-cemter text-left justify-between flex-col">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard Monitoring') }}
            </h2>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500" id="last-updated">Memuat...</span>
                <button id="refresh-btn" class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-100 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-lg p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt class="text-sm font-medium text-blue-500 truncate">Total Perangkat</dt>
                            <dd class="text-2xl font-bold text-gray-900" id="total-devices-stat">{{ $totalDevices }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-50 to-green-100 border border-green-100 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-lg p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt class="text-sm font-medium text-green-500 truncate">Perangkat Aktif</dt>
                            <dd class="text-2xl font-bold text-gray-900" id="active-devices-stat">{{ $activeDevices }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-red-50 to-red-100 border border-red-100 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-500 rounded-lg p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2M12 12L10 10m2 2l-2 2m2-2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt class="text-sm font-medium text-red-500 truncate">Perangkat Tidak Aktif</dt>
                            <dd class="text-2xl font-bold text-gray-900" id="down-devices-stat">{{ $downDevices }}</dd>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 border border-yellow-100 rounded-xl p-5 shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-lg p-3">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt class="text-sm font-medium text-yellow-500 truncate">Peringatan Aktif</dt>
                            <dd class="text-2xl font-bold text-gray-900" id="active-alerts-stat">{{ $activeAlerts }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Recent Alerts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Chart Placeholder -->
                <div class="bg-white border border-gray-200 overflow-hidden shadow-sm rounded-xl">
                    <div class="px-5 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Tren Waktu Respons</h3>
                    </div>
                    <div class="px-5 py-6">
                        <div class="h-72 flex items-center justify-center bg-gray-50 rounded-lg">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Grafik Tren Waktu Respons</h3>
                                <p class="mt-1 text-sm text-gray-500">Grafik interaktif untuk data respons waktu perangkat</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Alerts -->
                <div class="bg-white border border-gray-200 overflow-hidden shadow-sm rounded-xl">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Peringatan Terbaru</h3>
                        <a href="{{ route('alerts.index') }}" class="text-sm text-blue-600 hover:text-blue-900">Lihat semua →</a>
                    </div>
                    <div class="px-5 py-4">
                        <div id="recent-alerts-list">
                            @if($recentAlerts->count() > 0)
                                <ul class="space-y-3">
                                    @foreach($recentAlerts as $alert)
                                        <li class="flex items-start p-3 bg-gray-50 rounded-lg">
                                            <div class="flex-shrink-0">
                                                @if($alert->status === 'active')
                                                    <span class="inline-flex items-center p-1.5 rounded-full bg-red-100">
                                                        <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center p-1.5 rounded-full bg-green-100">
                                                        <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ $alert->device->name }}</p>
                                                <p class="text-sm text-gray-500 truncate max-w-xs">{{ $alert->message }}</p>
                                                <div class="mt-1 flex items-center justify-between">
                                                    <span class="text-xs text-gray-500">{{ $alert->created_at->diffForHumans() }}</span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        {{ $alert->status === 'active' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                        {{ $alert->status === 'active' ? 'Aktif' : 'Selesai' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada peringatan</h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Semua perangkat dalam status normal.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Devices Status -->
            <div class="bg-white border border-gray-200 overflow-hidden shadow-sm rounded-xl">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Status Perangkat Utama</h3>
                </div>
                <div class="px-5 py-6">
                    <div id="main-devices-container">
                        @if($mainDevices->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                                @foreach($mainDevices as $device)
                                    <div class="border rounded-xl p-5 shadow-sm bg-white hover:shadow-md transition-shadow duration-200" id="device-{{ $device->id }}">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <h4 class="font-bold text-lg truncate">{{ $device->name }}</h4>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        {{ $device->status === 'up' ? 'bg-green-100 text-green-800' : 
                                                           ($device->status === 'down' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                                        {{ $device->status }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-600 mt-1">{{ $device->ip_address }}</p>
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ ucfirst($device->hierarchy_level) }}
                                                    </span>
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                        {{ ucfirst($device->type) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <a href="{{ route('devices.show', $device) }}" class="text-blue-500 hover:text-blue-700 flex-shrink-0 ml-3">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                        
                                        @if($device->location)
                                            <p class="mt-3 text-sm text-gray-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ $device->location }}
                                            </p>
                                        @endif
                                        
                                        @if($device->children->count() > 0)
                                            <div class="mt-4 pt-4 border-t border-gray-200">
                                                <p class="text-sm font-medium text-gray-700">Sub Perangkat ({{ $device->children->count() }})</p>
                                                <div class="mt-2 space-y-2 max-h-40 overflow-y-auto pr-2">
                                                    @foreach($device->children as $child)
                                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                                            <div class="flex items-center">
                                                                <span class="inline-block w-2 h-2 rounded-full mr-2 
                                                                    {{ $child->status === 'up' ? 'bg-green-500' : 
                                                                       ($child->status === 'down' ? 'bg-red-500' : 'bg-gray-500') }}">
                                                                </span>
                                                                <span class="text-sm font-medium">{{ $child->name }}</span>
                                                            </div>
                                                            <span class="text-xs px-2 py-1 rounded-full 
                                                                {{ $child->status === 'up' ? 'bg-green-100 text-green-800' : 
                                                                   ($child->status === 'down' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                                                {{ $child->status }}
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between">
                                            <span class="text-xs text-gray-500">
                                                @if($device->last_checked_at)
                                                    Diperiksa: {{ $device->last_checked_at->diffForHumans() }}
                                                @else
                                                    Belum diperiksa
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada perangkat</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Perangkat akan ditampilkan di sini setelah ditambahkan.
                                </p>
                                <div class="mt-6">
                                    <a href="{{ route('devices.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                        Tambah Perangkat
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Real-time Updates -->
    <script>
        // Update dashboard data every 30 seconds
        let refreshInterval = setInterval(updateDashboardData, 30000);
        
        // Initial load
        updateDashboardData();
        
        // Manual refresh button
        document.getElementById('refresh-btn').addEventListener('click', function() {
            updateDashboardData();
        });
        
        // Update dashboard data from the API
        async function updateDashboardData() {
            try {
                const response = await fetch('{{ route("dashboard.realtime") }}', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    
                    // Update stats cards
                    document.getElementById('total-devices-stat').textContent = data.totalDevices;
                    document.getElementById('active-devices-stat').textContent = data.activeDevices;
                    document.getElementById('down-devices-stat').textContent = data.downDevices;
                    document.getElementById('active-alerts-stat').textContent = data.activeAlerts;
                    
                    // Update last updated time
                    document.getElementById('last-updated').textContent = 'Terakhir diperbarui: ' + new Date().toLocaleTimeString();
                    
                    // Update main devices status
                    updateMainDevices(data.mainDevices);
                    
                    console.log('Dashboard updated at: ' + new Date().toLocaleTimeString());
                } else {
                    console.error('Failed to fetch dashboard data:', response.status);
                }
            } catch (error) {
                console.error('Error updating dashboard:', error);
                document.getElementById('last-updated').textContent = 'Gagal memperbarui data';
            }
        }
        
        // Update main devices display
        function updateMainDevices(devices) {
            const container = document.getElementById('main-devices-container');
            if (!container) return;
            
            // Clear existing content except for the fallback message
            if (devices.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada perangkat</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Perangkat akan ditampilkan di sini setelah ditambahkan.
                        </p>
                        <div class="mt-6">
                            <a href="/devices/create" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Tambah Perangkat
                            </a>
                        </div>
                    </div>
                `;
                return;
            }
            
            // Create grid container
            let gridContent = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">';
            
            devices.forEach(device => {
                // Determine status color classes
                let statusColorClass = '';
                if (device.status === 'up') {
                    statusColorClass = 'bg-green-100 text-green-800';
                } else if (device.status === 'down') {
                    statusColorClass = 'bg-red-100 text-red-800';
                } else {
                    statusColorClass = 'bg-gray-100 text-gray-800';
                }
                
                // Build the device card HTML
                let cardHtml = `
                    <div class="border rounded-xl p-5 shadow-sm bg-white hover:shadow-md transition-shadow duration-200" id="device-${device.id}">
                        <div class="flex justify-between items-start">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-lg truncate">${device.name}</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColorClass}">
                                        ${device.status}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">${device.ip_address}</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        ${device.hierarchy_level.charAt(0).toUpperCase() + device.hierarchy_level.slice(1)}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                        ${device.type.charAt(0).toUpperCase() + device.type.slice(1)}
                                    </span>
                                </div>
                            </div>
                            <a href="/devices/${device.id}" class="text-blue-500 hover:text-blue-700 flex-shrink-0 ml-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                        </div>
                `;
                
                if (device.children && device.children.length > 0) {
                    cardHtml += `
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm font-medium text-gray-700">Sub Perangkat (${device.children.length})</p>
                            <div class="mt-2 space-y-2 max-h-40 overflow-y-auto pr-2">
                    `;
                    
                    device.children.forEach(child => {
                        let childStatusColor = '';
                        if (child.status === 'up') {
                            childStatusColor = 'bg-green-500';
                        } else if (child.status === 'down') {
                            childStatusColor = 'bg-red-500';
                        } else {
                            childStatusColor = 'bg-gray-500';
                        }
                        
                        let childStatusColorClass = '';
                        if (child.status === 'up') {
                            childStatusColorClass = 'bg-green-100 text-green-800';
                        } else if (child.status === 'down') {
                            childStatusColorClass = 'bg-red-100 text-red-800';
                        } else {
                            childStatusColorClass = 'bg-gray-100 text-gray-800';
                        }
                        
                        cardHtml += `
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <span class="inline-block w-2 h-2 rounded-full mr-2 ${childStatusColor}"></span>
                                    <span class="text-sm font-medium">${child.name}</span>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full ${childStatusColorClass}">
                                    ${child.status}
                                </span>
                            </div>
                        `;
                    });
                    
                    cardHtml += `
                            </div>
                        </div>
                    `;
                }
                
                cardHtml += `
                    <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between">
                        <span class="text-xs text-gray-500">
                            Diperiksa: ${device.last_checked_at}
                        </span>
                    </div>
                </div>
                `;
                
                gridContent += cardHtml;
            });
            
            gridContent += '</div>';
            container.innerHTML = gridContent;
        }
        
        // Handle page visibility to pause/resume updates
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                clearInterval(refreshInterval);
            } else {
                refreshInterval = setInterval(updateDashboardData, 30000);
                updateDashboardData(); // Update immediately when page becomes visible
            }
        });
    </script>
</x-app-layout>
