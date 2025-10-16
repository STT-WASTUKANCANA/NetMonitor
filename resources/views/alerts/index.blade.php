@extends('layouts.app')

@section('title', 'Alert Notifications')

@section('header')
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Alert Notifications</h1>
                    <p class="mt-1 text-sm text-gray-500">Monitor system alerts and notifications</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <button id="refresh-alerts-btn" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg shadow-sm transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </header>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-card border border-gray-200 overflow-hidden">
    <!-- Status Filters -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex flex-wrap gap-2">
            <button data-status="all" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors status-btn {{ request('status', 'all') === 'all' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                All (<span id="all-count">{{ \App\Models\Alert::count() }}</span>)
            </button>
            <button data-status="active" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors status-btn {{ request('status') === 'active' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Active (<span id="active-count">{{ \App\Models\Alert::where('status', 'active')->count() }}</span>)
            </button>
            <button data-status="resolved" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors status-btn {{ request('status') === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Resolved (<span id="resolved-count">{{ \App\Models\Alert::where('status', 'resolved')->count() }}</span>)
            </button>
        </div>
    </div>

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
                    <input type="text" placeholder="Search alerts..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" id="alert-search">
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <select class="block px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" id="device-filter">
                    <option value="">All Devices</option>
                    @foreach(\App\Models\Device::all() as $device)
                        <option value="{{ $device->id }}">{{ $device->name }}</option>
                    @endforeach
                </select>
                <select class="block px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" id="sort-order">
                    <option value="desc">Latest First</option>
                    <option value="asc">Oldest First</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Alerts Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($alerts ?? [] as $alert)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                <span class="text-red-800 font-medium">
                                    {{ $alert->device ? strtoupper(substr($alert->device->name, 0, 1)) : 'N' }}
                                </span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $alert->device ? $alert->device->name : 'Unknown Device' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $alert->device ? $alert->device->ip_address : 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate" title="{{ $alert->message }}">
                        {{ $alert->message }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $alert->status === 'active' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst($alert->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $alert->created_at->diffForHumans() }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('alerts.show', $alert) }}" class="text-blue-600 hover:text-blue-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            @if($alert->status === 'active')
                            <form method="POST" action="{{ route('alerts.resolve', $alert) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-green-600 hover:text-green-900" title="Resolve Alert">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <p class="text-lg text-gray-500">No alerts found</p>
                            <p class="text-gray-400 mt-1">All systems are running smoothly</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($alerts) && method_exists($alerts, 'links'))
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        {{ $alerts->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.pusher.com/js/7.0.3/pusher.min.js"></script>
<script>
    // Alert management class
    class AlertManager {
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
            // Status filter buttons
            document.querySelectorAll('.status-btn').forEach(button => {
                button.addEventListener('click', (e) => {
                    const status = e.currentTarget.dataset.status;
                    this.filterByStatus(status);
                });
            });
            
            // Search functionality
            document.getElementById('alert-search').addEventListener('input', this.filterAlerts.bind(this));
            
            // Device filter
            document.getElementById('device-filter').addEventListener('change', this.filterAlerts.bind(this));
            
            // Sort order
            document.getElementById('sort-order').addEventListener('change', this.sortAlerts.bind(this));
            
            // Refresh button
            document.getElementById('refresh-alerts-btn').addEventListener('click', this.refreshAlerts.bind(this));
        }
        
        filterByStatus(status) {
            // Update active button
            document.querySelectorAll('.status-btn').forEach(btn => {
                btn.classList.remove('bg-blue-100', 'text-blue-800', 'bg-red-100', 'text-red-800', 'bg-green-100', 'text-green-800');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            });
            
            const activeBtn = document.querySelector(`[data-status="${status}"]`);
            if (activeBtn) {
                if (status === 'active') {
                    activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
                    activeBtn.classList.add('bg-red-100', 'text-red-800');
                } else if (status === 'resolved') {
                    activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
                    activeBtn.classList.add('bg-green-100', 'text-green-800');
                } else {
                    activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
                    activeBtn.classList.add('bg-blue-100', 'text-blue-800');
                }
            }
            
            // Filter alerts by status
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                if (status === 'all') {
                    row.style.display = '';
                } else {
                    const statusTag = row.querySelector('.bg-red-100, .bg-green-100');
                    const statusText = statusTag ? statusTag.textContent.trim().toLowerCase() : '';
                    if (statusText === status) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }
        
        filterAlerts() {
            const searchTerm = document.getElementById('alert-search').value.toLowerCase();
            const deviceFilter = document.getElementById('device-filter').value;
            
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const deviceName = row.querySelector('td:first-child .text-gray-900').textContent.toLowerCase();
                const message = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                
                const matchesSearch = deviceName.includes(searchTerm) || message.includes(searchTerm);
                const matchesDevice = !deviceFilter || deviceName.includes(deviceFilter.toLowerCase());
                
                if (matchesSearch && matchesDevice) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        sortAlerts() {
            // This would require re-rendering the table, so for now we'll just refresh
            this.refreshAlerts();
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
                
                // Listen for device status updates (to update counters)
                const statusChannel = pusher.subscribe('device-status');
                statusChannel.bind('DeviceStatusUpdated', (data) => {
                    // Refresh the alert counts
                    this.refreshAlertCounts();
                });
                
                // Listen for alerts
                const alertChannel = pusher.subscribe('device-alerts');
                alertChannel.bind('DeviceAlertCreated', (data) => {
                    this.addAlertToTable(data);
                });
                
            } catch (e) {
                console.log('Pusher not configured, using polling fallback');
            }
            
            // Fallback to polling every 15 seconds
            setInterval(() => {
                this.refreshAlerts();
            }, 15000);
        }
        
        startAutoRefresh() {
            // Refresh data every 30 seconds
            setInterval(() => {
                this.refreshAlerts();
            }, 30000);
        }
        
        async refreshAlerts() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const status = urlParams.get('status') || 'all';
                
                const response = await fetch(`/api/alerts?status=${status}`, {
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    this.updateAlertTable(result.data || result);
                }
            } catch (error) {
                console.error('Error refreshing alerts:', error);
            }
        }
        
        async refreshAlertCounts() {
            try {
                const response = await fetch('/api/alerts/unresolved', {
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    document.getElementById('active-count').textContent = result.count || 0;
                    document.getElementById('all-count').textContent = result.total || 0;
                }
            } catch (error) {
                console.error('Error refreshing alert counts:', error);
            }
        }
        
        updateAlertTable(alerts) {
            // For now, we'll just refresh the page
            // In a production system, we would dynamically update the table
            location.reload();
        }
        
        addAlertToTable(alert) {
            // Create new table row for the alert
            const newRow = document.createElement('tr');
            newRow.className = 'hover:bg-gray-50 transition-colors animate-pulse';
            newRow.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                            <span class="text-red-800 font-medium">
                                ${alert.device_name ? alert.device_name.charAt(0).toUpperCase() : 'N'}
                            </span>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                ${alert.device_name || 'Unknown Device'}
                            </div>
                            <div class="text-sm text-gray-500">
                                ${alert.device_ip || 'N/A'}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate" title="${alert.message}">
                    ${alert.message}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        Active
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    Just now
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-2">
                        <a href="/alerts/${alert.id}" class="text-blue-600 hover:text-blue-900">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                        <form method="POST" action="/alerts/${alert.id}/resolve" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-green-600 hover:text-green-900" title="Resolve Alert">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            `;
            
            // Add to the beginning of the table
            const tableBody = document.querySelector('tbody');
            if (tableBody) {
                tableBody.insertBefore(newRow, tableBody.firstChild);
                
                // Remove animation after it completes
                setTimeout(() => {
                    newRow.classList.remove('animate-pulse');
                }, 1000);
            }
        }
    }
    
    // Initialize alert manager when page loads
    document.addEventListener('DOMContentLoaded', function() {
        new AlertManager();
    });
</script>
@endpush
@endsection