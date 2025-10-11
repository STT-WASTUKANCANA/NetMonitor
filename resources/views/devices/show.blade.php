<x-app-layout>
    <x-slot name="header">
        <div class="mb-2">
            <!-- Judul Halaman -->
            <h2 class="font-semibold text-xl text-black leading-tight">
                {{ __('Device Details') }}
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
                            <a href="{{ route('devices.index') }}"
                                class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-600 transition-colors">
                                Manage Devices
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-black mx-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium text-black">View Device: {{ $device->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-black">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-xl font-bold">{{ $device->name }}</h3>
                            <div class="flex items-center mt-2">
                                <span class="inline-block w-3 h-3 rounded-full 
                                    {{ $device->status === 'up' ? 'bg-green-500' : 
                                       ($device->status === 'down' ? 'bg-red-500' : 'bg-gray-500') }} mr-2">
                                </span>
                                <span class="font-medium">{{ $device->status }}</span>
                            </div>
                        </div>
                        
                        <div class="flex space-x-2">
                            @can('edit devices')
                                <button type="button" 
                                    onclick="pingDevice({{ $device->id }})" 
                                    title="Ping Connection" 
                                    class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 ping-btn" 
                                    data-device-id="{{ $device->id }}">
                                    Ping
                                </button>
                                <a href="{{ route('devices.edit', $device) }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    Edit
                                </a>
                            @endcan
                            @can('delete devices')
                                <form action="{{ route('devices.destroy', $device) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600" onclick="return confirm('Are you sure?')">
                                        Delete
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium mb-2">Informasi Perangkat</h4>
                            <ul class="space-y-1">
                                <li class="flex">
                                    <span class="w-32 text-gray-900">IP Address</span>
                                    <span class="font-medium">{{ $device->ip_address }}</span>
                                </li>
                                <li class="flex">
                                    <span class="w-32 text-gray-900">Tipe</span>
                                    <span class="font-medium">{{ $device->type }}</span>
                                </li>
                                <li class="flex">
                                    <span class="w-32 text-gray-900">Lokasi</span>
                                    <span class="font-medium">{{ $device->location ?: '-' }}</span>
                                </li>
                                <li class="flex">
                                    <span class="w-32 text-gray-900">Status</span>
                                    <span class="font-medium">
                                        <span class="{{ $device->status === 'up' ? 'text-green-600' : 
                                           ($device->status === 'down' ? 'text-red-600' : 'text-gray-600') }}">
                                            {{ $device->status }}
                                        </span>
                                    </span>
                                </li>
                                <li class="flex">
                                    <span class="w-32 text-gray-900 ">Aktif</span>
                                    <span class="font-medium">{{ $device->is_active ? 'Ya' : 'Tidak' }}</span>
                                </li>
                                <li class="flex">
                                    <span class="w-32 text-gray-900">Terakhir Dicek</span>
                                    <span class="font-medium">{{ $device->last_checked_at ? $device->last_checked_at->diffForHumans() : 'Belum pernah dicek' }}</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium mb-2">Deskripsi</h4>
                            <p class="text-gray-700">{{ $device->description ?: '-' }}</p>
                            
                            @if($device->parent)
                                <div class="mt-4">
                                    <h4 class="font-medium mb-2">Perangkat Induk</h4>
                                    <a href="{{ route('devices.show', $device->parent) }}" class="text-blue-500 hover:text-blue-700">
                                        {{ $device->parent->name }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($device->children->count() > 0)
                        <div class="mb-8">
                            <h4 class="text-lg font-medium mb-4">Perangkat Terkait ({{ $device->children->count() }})</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($device->children as $child)
                                    <div class="border rounded-lg p-3 shadow-sm">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h5 class="font-medium">{{ $child->name }}</h5>
                                                <p class="text-sm text-gray-600">{{ $child->ip_address }}</p>
                                                <span class="inline-block mt-1 px-2 py-1 text-xs rounded-full 
                                                    {{ $child->status === 'up' ? 'bg-green-100 text-green-800' : 
                                                       ($child->status === 'down' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                                    {{ $child->status }}
                                                </span>
                                            </div>
                                            <a href="{{ route('devices.show', $child) }}" class="text-blue-500 hover:text-blue-700">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Recent logs -->
                    @if($device->logs->count() > 0)
                        <div>
                            <h4 class="text-lg font-medium mb-4">Riwayat Terbaru</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Waktu Respons</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Waktu</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500  uppercase tracking-wider">Pesan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($device->logs->sortByDesc('logged_at')->take(10) as $log)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $log->status === 'up' ? 'bg-green-100 text-green-800' : 
                                                           ($log->status === 'down' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                                        {{ $log->status }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $log->response_time ? $log->response_time . ' ms' : '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $log->logged_at->format('d M Y H:i:s') }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                                                    {{ Str::limit($log->message, 50) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
            const originalText = pingBtn.innerHTML;
            
            // Show loading state
            pingBtn.innerHTML = `
                <svg class="w-4 h-4 inline mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Pinging...
            `;
            pingBtn.disabled = true;
            
            try {
                const response = await fetch(`/api/devices/${deviceId}/ping`, {
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
                    const statusDot = document.querySelector('.inline-block.w-3.h-3.rounded-full');
                    const statusText = document.querySelector('.font-medium.text-gray-600, .text-green-600, .text-red-600, .font-medium');
                    const statusBadge = document.querySelector('.bg-green-100, .bg-red-100, .bg-gray-100');
                    
                    // Update status dot
                    if (statusDot) {
                        statusDot.className = statusDot.className.replace(/\s*(bg-green-500|bg-red-500|bg-gray-500)/g, '');
                        statusDot.className += ` ${data.result.status === 'up' ? 'bg-green-500' : 'bg-red-500'}`;
                    }
                    
                    // Update status text
                    if (statusText) {
                        // Find the element that contains the status text
                        const statusElements = document.querySelectorAll('.font-medium');
                        statusElements.forEach(el => {
                            if (el.textContent === 'up' || el.textContent === 'down') {
                                el.innerHTML = `<span class="${data.result.status === 'up' ? 'text-green-600' : 
                                                (data.result.status === 'down' ? 'text-red-600' : 'text-gray-600')}">
                                    ${data.result.status}
                                </span>`;
                            }
                        });
                    }
                    
                    // Update status badge if exists
                    if (statusBadge) {
                        statusBadge.className = statusBadge.className.replace(/\s*(bg-green-100|bg-red-100|bg-gray-100|text-green-800|text-red-800|text-gray-800)/g, '');
                        statusBadge.className += ` ${data.result.status === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
                        statusBadge.textContent = data.result.status;
                    }
                    
                    // Update last checked time
                    const lastCheckedElements = document.querySelectorAll('span');
                    lastCheckedElements.forEach(el => {
                        if (el.textContent.includes('Terakhir Dicek') || el.textContent.includes('Last checked')) {
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
                // Restore original button text
                pingBtn.innerHTML = originalText;
                pingBtn.disabled = false;
            }
        }
    </script>
</x-app-layout>