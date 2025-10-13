<x-app-layout>
    <x-slot name="header">
        <div class="mb-2">
            <!-- Judul Halaman -->
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Devices') }}
            </h2>

            <!-- Breadcrumb -->
            <nav class="flex mt-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-600 transition-colors">
                            <svg class="w-4 h-4 mr-2 text-gray-400 hover:text-blue-500 transition-colors" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-black mx-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium text-black">Manage Devices</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <h3 class="text-lg font-medium">Daftar Perangkat</h3>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('devices.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Perangkat
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($devices as $device)
                            <div class="border border-gray-200 rounded-xl p-5 shadow hover:shadow-md transition-shadow duration-200 bg-white">
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
                                    
                                    @can('edit devices')
                                        <div class="grid grid-cols-3 gap-2">
                                            <button type="button" 
                                                onclick="pingDevice({{ $device->id }})" 
                                                title="Ping Connection" 
                                                class="text-green-500 hover:text-green-700 ping-btn mb-2" 
                                                data-device-id="{{ $device->id }}">
                                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M320 160C229.1 160 146.8 196 86.3 254.6C73.6 266.9 53.3 266.6 41.1 253.9C28.9 241.2 29.1 220.9 41.8 208.7C113.7 138.9 211.9 96 320 96C428.1 96 526.3 138.9 598.3 208.7C611 221 611.3 241.3 599 253.9C586.7 266.5 566.4 266.9 553.8 254.6C493.2 196 410.9 160 320 160zM272 496C272 469.5 293.5 448 320 448C346.5 448 368 469.5 368 496C368 522.5 346.5 544 320 544C293.5 544 272 522.5 272 496zM200 390.2C188.3 403.5 168.1 404.7 154.8 393C141.5 381.3 140.3 361.1 152 347.8C193 301.4 253.1 272 320 272C386.9 272 447 301.4 488 347.8C499.7 361.1 498.4 381.3 485.2 393C472 404.7 451.7 403.4 440 390.2C410.6 356.9 367.8 336 320 336C272.2 336 229.4 356.9 200 390.2z"/>
                                                </svg>
                                            </button>
                                            <a href="{{ route('devices.edit', $device) }}" class="text-blue-500 hover:text-blue-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            @can('delete devices')
                                                <form action="{{ route('devices.destroy', $device) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this device?')">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    @endcan
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

                                @if($device->parent)
                                    <div class="mt-2 text-sm text-gray-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                        </svg>
                                        <span>Parent: {{ $device->parent->name }}</span>
                                    </div>
                                @endif
                                
                                @if($device->children->count() > 0)
                                    <div class="mt-4 border-t border-gray-200 pt-4">
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
                                            Last checked: {{ $device->last_checked_at->diffForHumans() }}
                                        @else
                                            Never checked
                                        @endif
                                    </span>
                                    <a href="{{ route('devices.show', $device) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Detail →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($devices->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No devices</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Get started by creating a new device.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('devices.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    Add Device
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <script>
        async function pingDevice(deviceId) {
            const pingBtn = document.querySelector(`button[data-device-id="${deviceId}"]`);
            const originalIcon = pingBtn.innerHTML;
            
            // Show loading state
            pingBtn.innerHTML = `
                <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            `;
            pingBtn.disabled = true;
            
            try {
                const response = await fetch(`/api/device/${deviceId}/ping`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Update device status display
                    const deviceElement = pingBtn.closest('.border');
                    const statusSpan = deviceElement.querySelector('span[title], span.font-medium');
                    const statusDot = deviceElement.querySelector('.bg-green-500, .bg-red-500, .bg-gray-500');
                    
                    // Update status text and badge
                    if (statusSpan) {
                        // Find the status span and update it
                        const statusText = deviceElement.querySelector('.bg-green-100, .bg-red-100, .bg-gray-100');
                        if (statusText) {
                            // Remove old classes
                            statusText.className = statusText.className.replace(/\s*(bg-green-100|bg-red-100|bg-gray-100|text-green-800|text-red-800|text-gray-800)/g, '');
                            // Add new classes based on result
                            statusText.className += ` ${data.result.status === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
                            statusText.textContent = data.result.status;
                        }
                        
                        // Update status dot
                        if (statusDot) {
                            statusDot.className = statusDot.className.replace(/\s*(bg-green-500|bg-red-500|bg-gray-500)/g, '');
                            statusDot.className += ` ${data.result.status === 'up' ? 'bg-green-500' : 'bg-red-500'}`;
                        }
                    }
                    
                    // Update last checked time
                    const lastCheckedElements = document.querySelectorAll('span');
                    lastCheckedElements.forEach(el => {
                        if (el.textContent.includes('Last checked')) {
                            // Update the text after the colon to show "just now"
                            const text = el.textContent;
                            const colonIndex = text.indexOf(':');
                            if (colonIndex !== -1) {
                                el.textContent = text.substring(0, colonIndex + 1) + ' just now';
                            } else {
                                el.textContent = 'Last checked: just now';
                            }
                        }
                    });
                    
                    // Show success message using existing toast function
                    showToast(`Device pinged successfully. Status: ${data.result.status}`, 'success');
                } else {
                    showToast(data.message || 'Error pinging device', 'error');
                }
            } catch (error) {
                showToast('Network error occurred while pinging device', 'error');
                console.error('Error:', error);
            } finally {
                // Restore original icon
                pingBtn.innerHTML = originalIcon;
                pingBtn.disabled = false;
            }
        }
    </script>
</x-app-layout>