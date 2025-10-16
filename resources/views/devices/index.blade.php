@extends('layouts.app')

@section('title', 'Manage Devices')

@section('header')
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Manage Devices</h1>
                    <p class="mt-1 text-sm text-gray-500">Monitor and manage your network devices</p>
                </div>
                <div class="mt-4 sm:mt-0 flex flex-wrap items-center gap-3">
                    <button id="scan-all-btn" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Scan All
                    </button>
                    <a href="{{ route('devices.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Device
                    </a>
                </div>
            </div>
        </div>
    </header>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-card border border-gray-200 overflow-hidden">
    <!-- Search and Filter Bar -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" placeholder="Search devices..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" id="device-search">
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <select class="block px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" id="status-filter">
                    <option value="">All Status</option>
                    <option value="up">Online</option>
                    <option value="down">Offline</option>
                </select>
                <select class="block px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" id="type-filter">
                    <option value="">All Types</option>
                    <option value="router">Router</option>
                    <option value="switch">Switch</option>
                    <option value="access_point">Access Point</option>
                    <option value="server">Server</option>
                    <option value="other">Other</option>
                </select>
                <select class="block px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" id="hierarchy-filter">
                    <option value="">All Hierarchies</option>
                    <option value="utama">Utama</option>
                    <option value="sub">Sub</option>
                    <option value="device">Device</option>
                </select>
                <button id="refresh-devices-btn" class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Devices Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hierarchy</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Checked</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="devices-table-body">
                @forelse($devices ?? [] as $device)
                <tr class="hover:bg-gray-50 transition-colors" data-device-id="{{ $device->id }}" data-status="{{ $device->status }}" data-type="{{ $device->type }}" data-hierarchy="{{ $device->hierarchy_level }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <x-avatar :user="null" :showName="false" :size="'md'" class="mr-3" />
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $device->name }}</div>
                                <div class="text-sm text-gray-500">{{ $device->location ?? 'Location not set' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $device->ip_address }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst($device->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $device->hierarchy_level === 'utama' ? 'bg-green-100 text-green-800' : 
                               ($device->hierarchy_level === 'sub' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800') }}">
                            {{ ucfirst($device->hierarchy_level) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $device->status === 'up' ? 'bg-green-100 text-green-800' : 
                               ($device->status === 'down' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}
                            status-badge" data-status="{{ $device->status }}">
                            {{ ucfirst($device->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 response-time" data-device-id="{{ $device->id }}">
                        {{ $device->response_time ? $device->response_time . ' ms' : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 last-checked" data-device-id="{{ $device->id }}">
                        {{ $device->last_checked_at ? $device->last_checked_at->diffForHumans() : 'Never' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <button type="button" 
                                class="text-indigo-600 hover:text-indigo-900 scan-device-btn" 
                                data-device-id="{{ $device->id }}"
                                title="Scan this device">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </button>
                            <a href="{{ route('devices.show', $device) }}" class="text-blue-600 hover:text-blue-900" title="View details">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('devices.edit', $device) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit device">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('devices.destroy', $device) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete device" onclick="return confirm('Are you sure you want to delete this device?')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                            </svg>
                            <p class="text-lg text-gray-500">No devices found</p>
                            <p class="text-gray-400 mt-1">Get started by creating a new device</p>
                            <a href="{{ route('devices.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                                Add Device
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($devices) && method_exists($devices, 'links'))
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $devices->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.pusher.com/js/7.0.3/pusher.min.js"></script>
<script>
    // Device management class
    class DeviceManager {
        constructor() {
            this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            this.init();
        }
        
        init() {
            this.initEventListeners();
            this.initBroadcasting();
            this.startAutoRefresh();
        }
        
        initEventListeners() {
            // Search functionality
            document.getElementById('device-search').addEventListener('input', this.filterDevices.bind(this));
            
            // Filter functionality
            document.getElementById('status-filter').addEventListener('change', this.filterDevices.bind(this));
            document.getElementById('type-filter').addEventListener('change', this.filterDevices.bind(this));
            document.getElementById('hierarchy-filter').addEventListener('change', this.filterDevices.bind(this));
            
            // Refresh devices button
            document.getElementById('refresh-devices-btn').addEventListener('click', this.refreshDevices.bind(this));
            
            // Scan all button
            document.getElementById('scan-all-btn').addEventListener('click', this.scanAllDevices.bind(this));
            
            // Individual device scan buttons
            document.querySelectorAll('.scan-device-btn').forEach(button => {
                button.addEventListener('click', (e) => {
                    const deviceId = e.currentTarget.dataset.deviceId;
                    this.scanDevice(deviceId);
                });
            });
        }
        
        filterDevices() {
            const searchTerm = document.getElementById('device-search').value.toLowerCase();
            const statusFilter = document.getElementById('status-filter').value;
            const typeFilter = document.getElementById('type-filter').value;
            const hierarchyFilter = document.getElementById('hierarchy-filter').value;
            
            const rows = document.querySelectorAll('#devices-table-body tr');
            
            rows.forEach(row => {
                const deviceName = row.querySelector('td:first-child .text-gray-900').textContent.toLowerCase();
                const status = row.dataset.status;
                const type = row.dataset.type;
                const hierarchy = row.dataset.hierarchy;
                
                const matchesSearch = deviceName.includes(searchTerm);
                const matchesStatus = !statusFilter || status === statusFilter;
                const matchesType = !typeFilter || type === typeFilter;
                const matchesHierarchy = !hierarchyFilter || hierarchy === hierarchyFilter;
                
                if (matchesSearch && matchesStatus && matchesType && matchesHierarchy) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        initBroadcasting() {
            try {
                const pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
                    cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
                    authEndpoint: '/broadcasting/auth',
                    auth: {
                        headers: {
                            'X-CSRF-Token': this.csrfToken
                        }
                    }
                });
                
                // Listen for device status updates
                const statusChannel = pusher.subscribe('device-status');
                statusChannel.bind('DeviceStatusUpdated', (data) => {
                    this.updateDeviceRow(data);
                });
                
            } catch (e) {
                console.log('Pusher not configured, using polling fallback');
            }
            
            // Fallback to polling every 15 seconds
            setInterval(() => {
                this.refreshDevices();
            }, 15000);
        }
        
        startAutoRefresh() {
            // Refresh data every 30 seconds
            setInterval(() => {
                this.refreshDevices();
            }, 30000);
        }
        
        async refreshDevices() {
            try {
                const response = await fetch('/api/devices', {
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const devices = await response.json();
                    this.updateDeviceTable(devices);
                }
            } catch (error) {
                console.error('Error refreshing devices:', error);
            }
        }
        
        updateDeviceTable(devices) {
            // This would update the entire table, but we'll update individual rows as they change
            // For now, we'll just keep the individual updates from broadcast
        }
        
        updateDeviceRow(data) {
            const row = document.querySelector(`tr[data-device-id="${data.device_id}"]`);
            if (!row) return;
            
            // Update status badge
            const statusBadge = row.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                statusBadge.className = `px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                    ${data.status === 'up' ? 'bg-green-100 text-green-800' : 
                      (data.status === 'down' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')} 
                    status-badge`;
                statusBadge.dataset.status = data.status;
                
                // Update the row's data attribute for filtering
                row.dataset.status = data.status;
            }
            
            // Update response time
            const responseTimeCell = row.querySelector('.response-time');
            if (responseTimeCell) {
                responseTimeCell.textContent = data.response_time ? data.response_time + ' ms' : 'N/A';
                responseTimeCell.dataset.deviceId = data.device_id;
            }
            
            // Update last checked time
            const lastCheckedCell = row.querySelector('.last-checked');
            if (lastCheckedCell) {
                lastCheckedCell.textContent = this.timeAgo(new Date(data.updated_at));
                lastCheckedCell.dataset.deviceId = data.device_id;
            }
        }
        
        async scanDevice(deviceId) {
            try {
                const response = await fetch(`/api/device/scan`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ device_id: deviceId })
                });
                
                if (response.ok) {
                    const result = await response.json();
                    this.showNotification(`Device ${result.device.name} scanned successfully`, 'success');
                } else {
                    const error = await response.json();
                    this.showNotification(`Error scanning device: ${error.message}`, 'error');
                }
            } catch (error) {
                this.showNotification(`Error connecting to server: ${error.message}`, 'error');
            }
        }
        
        async scanAllDevices() {
            // This would scan all devices - for now we'll just show a notification
            this.showNotification('Scanning all devices...', 'info');
            
            try {
                // Get all devices first
                const response = await fetch('/api/devices', {
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const devices = await response.json();
                    // We could implement batch scanning here if needed
                    this.showNotification(`${devices.length} devices being monitored`, 'success');
                }
            } catch (error) {
                this.showNotification(`Error getting devices: ${error.message}`, 'error');
            }
        }
        
        timeAgo(date) {
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) return 'Just now';
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
            return `${Math.floor(diffInSeconds / 86400)} days ago`;
        }
        
        showNotification(message, type) {
            // Simple notification implementation
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg text-white z-50 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    }
    
    // Initialize device manager when page loads
    document.addEventListener('DOMContentLoaded', function() {
        new DeviceManager();
    });
</script>
@endpush
@endsection