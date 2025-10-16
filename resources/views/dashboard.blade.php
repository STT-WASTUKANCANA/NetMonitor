@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Network Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-500" id="current-date">
                        {{ $currentDate ?? now()->format('l, F j, Y') }}
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-4">
                    <div class="flex items-center text-sm text-gray-500">
                        <span class="flex items-center">
                            <span class="w-2 h-2 rounded-full bg-green-400 mr-1"></span>
                            <span id="online-device-count">{{ \App\Models\Device::where('status', 'up')->count() }}</span> Online
                        </span>
                        <span class="flex items-center ml-4">
                            <span class="w-2 h-2 rounded-full bg-red-400 mr-1"></span>
                            <span id="offline-device-count">{{ \App\Models\Device::where('status', 'down')->count() }}</span> Offline
                        </span>
                    </div>
                    <button id="refresh-btn" class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors" title="Refresh data">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Devices</p>
                    <p class="text-2xl font-bold text-gray-900" id="total-device-count">{{ \App\Models\Device::count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Online</p>
                    <p class="text-2xl font-bold text-gray-900" id="online-count">{{ \App\Models\Device::where('status', 'up')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-red-100 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Offline</p>
                    <p class="text-2xl font-bold text-gray-900" id="offline-count">{{ \App\Models\Device::where('status', 'down')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Alerts</p>
                    <p class="text-2xl font-bold text-gray-900" id="alert-count">{{ \App\Models\Alert::where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Range Selector -->
    <div class="bg-white rounded-xl shadow-card p-4 border border-gray-200">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h3 class="text-lg font-semibold text-gray-900">Network Performance</h3>
            <div class="flex space-x-2">
                <button class="px-3 py-1 text-sm rounded-lg bg-blue-100 text-blue-800" data-period="24h">Last 24h</button>
                <button class="px-3 py-1 text-sm rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200" data-period="7d">Last 7 Days</button>
                <button class="px-3 py-1 text-sm rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200" data-period="30d">Last 30 Days</button>
                <button class="px-3 py-1 text-sm rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200" data-period="90d">Last 90 Days</button>
            </div>
        </div>
    </div>

    <!-- Realtime Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Network Performance Chart -->
        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Response Time Trend</h3>
            <div class="h-80">
                <canvas id="responseTimeChart"></canvas>
            </div>
        </div>

        <!-- Device Status Chart -->
        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Device Status Distribution</h3>
            <div class="h-80">
                <canvas id="deviceStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Alerts and Recent Devices -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Alerts -->
        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200 lg:col-span-1">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Alerts</h3>
                <a href="{{ route('alerts.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
            <div class="space-y-4" id="recent-alerts">
                @forelse(\App\Models\Alert::latest()->take(5)->get() as $alert)
                    <div class="p-3 bg-gray-50 rounded-lg border-l-4 border-red-500">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-0.5">
                                @if($alert->status === 'active')
                                    <span class="w-2 h-2 rounded-full bg-red-500 block"></span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-green-500 block"></span>
                                @endif
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $alert->device->name ?? 'Unknown Device' }}</p>
                                <p class="text-sm text-gray-500">{{ Str::limit($alert->message, 60) }}</p>
                                <p class="text-xs text-gray-400 mt-1" title="{{ $alert->created_at }}">{{ $alert->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No active alerts</p>
                @endforelse
            </div>
        </div>

        <!-- Active Devices -->
        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Active Devices</h3>
                <a href="{{ route('devices.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response Time</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Check</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="devices-table-body">
                        @forelse(\App\Models\Device::latest()->take(5)->get() as $device)
                        <tr class="hover:bg-gray-50" data-device-id="{{ $device->id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <x-avatar :user="null" :showName="false" :size="'sm'" class="mr-3" />
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $device->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $device->type }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $device->ip_address }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $device->status === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} status-badge"
                                    data-status="{{ $device->status }}">
                                    {{ ucfirst($device->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 response-time" data-device-id="{{ $device->id }}">
                                {{ $device->response_time ?? 'N/A' }} ms
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $device->last_checked_at ? $device->last_checked_at->diffForHumans() : 'Never' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No devices found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.pusher.com/js/7.0.3/pusher.min.js"></script>
<script>
    // CSRF token for API requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Dashboard class to manage all dashboard functionality
    class NetworkDashboard {
        constructor() {
            this.responseTimeChart = null;
            this.deviceStatusChart = null;
            this.currentPeriod = '24h';
            this.init();
        }
        
        init() {
            this.initCharts();
            this.initEventListeners();
            this.initBroadcasting();
            this.startAutoRefresh();
        }
        
        initCharts() {
            // Initialize response time chart
            const responseCtx = document.getElementById('responseTimeChart').getContext('2d');
            this.responseTimeChart = new Chart(responseCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Avg Response Time (ms)',
                        data: [],
                        borderColor: 'rgb(79, 70, 229)',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.4,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Response Time (ms)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Time'
                            }
                        }
                    }
                }
            });
            
            // Initialize device status chart
            const statusCtx = document.getElementById('deviceStatusChart').getContext('2d');
            this.deviceStatusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Online', 'Offline'],
                    datasets: [{
                        data: [0, 0],
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)', // Online green
                            'rgba(239, 68, 68, 0.8)'  // Offline red
                        ],
                        borderColor: [
                            'rgba(34, 197, 94, 1)',
                            'rgba(239, 68, 68, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        initEventListeners() {
            // Time range selector
            document.querySelectorAll('[data-period]').forEach(button => {
                button.addEventListener('click', (e) => {
                    document.querySelectorAll('[data-period]').forEach(btn => {
                        btn.classList.remove('bg-blue-100', 'text-blue-800');
                        btn.classList.add('bg-gray-100', 'text-gray-700');
                    });
                    e.target.classList.remove('bg-gray-100', 'text-gray-700');
                    e.target.classList.add('bg-blue-100', 'text-blue-800');
                    
                    this.currentPeriod = e.target.dataset.period;
                    this.refreshData();
                });
            });
            
            // Refresh button
            document.getElementById('refresh-btn').addEventListener('click', () => {
                this.refreshData();
            });
        }
        
        initBroadcasting() {
            // Set up Pusher or fallback to polling
            try {
                const pusher = new Pusher('{{ config("broadcasting.connections.pusher.key") }}', {
                    cluster: '{{ config("broadcasting.connections.pusher.options.cluster") }}',
                    authEndpoint: '/broadcasting/auth',
                    auth: {
                        headers: {
                            'X-CSRF-Token': csrfToken
                        }
                    }
                });
                
                // Listen for device status updates
                const statusChannel = pusher.subscribe('device-status');
                statusChannel.bind('DeviceStatusUpdated', (data) => {
                    this.updateDeviceStatus(data);
                });
                
                // Listen for alerts
                const alertChannel = pusher.subscribe('device-alerts');
                alertChannel.bind('DeviceAlertCreated', (data) => {
                    this.addAlertToDashboard(data);
                });
                
            } catch (e) {
                console.log('Pusher not configured, using polling fallback');
            }
            
            // Fallback to polling every 10 seconds
            setInterval(() => {
                this.refreshData();
            }, 10000);
        }
        
        startAutoRefresh() {
            // Refresh data every minute
            setInterval(() => {
                this.refreshData();
            }, 60000);
        }
        
        async refreshData() {
            try {
                // Get network metrics
                const metricsResponse = await fetch(`/api/metrics/network?period=${this.currentPeriod}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                if (metricsResponse.ok) {
                    const metrics = await metricsResponse.json();
                    this.updateDashboard(metrics);
                }
                
                // Get recent alerts
                const alertsResponse = await fetch(`/api/alerts?status=active&per_page=5`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                if (alertsResponse.ok) {
                    const alerts = await alertsResponse.json();
                    this.updateAlerts(alerts.data || alerts);
                }
                
            } catch (error) {
                console.error('Error refreshing dashboard:', error);
            }
        }
        
        updateDashboard(data) {
            // Update summary cards
            if (data.summary) {
                document.getElementById('total-device-count').textContent = data.summary.total_devices;
                document.getElementById('online-count').textContent = data.summary.online_devices;
                document.getElementById('offline-count').textContent = data.summary.offline_devices;
                document.getElementById('alert-count').textContent = data.summary.online_devices + data.summary.offline_devices > 0 
                    ? Math.round((data.summary.offline_devices / (data.summary.online_devices + data.summary.offline_devices)) * 100) + '% Down' 
                    : '0% Down';
            }
            
            // Update charts
            if (data.timeline && this.responseTimeChart) {
                const labels = data.timeline.map(item => new Date(item.timestamp).toLocaleTimeString());
                const responseTimes = data.timeline.map(item => item.avg_response_time);
                
                this.responseTimeChart.data.labels = labels;
                this.responseTimeChart.data.datasets[0].data = responseTimes;
                this.responseTimeChart.update();
            }
            
            // Update device status chart
            if (data.summary && this.deviceStatusChart) {
                this.deviceStatusChart.data.datasets[0].data = [
                    data.summary.online_devices,
                    data.summary.offline_devices
                ];
                this.deviceStatusChart.update();
            }
        }
        
        updateDeviceStatus(data) {
            // Update the device in the table
            const deviceRow = document.querySelector(`tr[data-device-id="${data.device_id}"]`);
            if (deviceRow) {
                // Update status badge
                const statusBadge = deviceRow.querySelector('.status-badge');
                if (statusBadge) {
                    statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                    statusBadge.className = `px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        ${data.status === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} status-badge`;
                    statusBadge.dataset.status = data.status;
                }
                
                // Update response time
                const responseTimeCell = deviceRow.querySelector('.response-time');
                if (responseTimeCell) {
                    responseTimeCell.textContent = data.response_time ? data.response_time + ' ms' : 'N/A';
                }
            }
            
            // Update the top counters
            this.refreshData(); // Refresh all data to keep counters accurate
        }
        
        updateAlerts(alerts) {
            const alertsContainer = document.getElementById('recent-alerts');
            if (!alertsContainer) return;
            
            if (alerts.length === 0) {
                alertsContainer.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">No active alerts</p>';
                return;
            }
            
            let alertsHtml = '';
            alerts.forEach(alert => {
                const deviceName = alert.device_name || 'Unknown Device';
                const message = alert.message.length > 60 ? alert.message.substring(0, 60) + '...' : alert.message;
                const timeAgo = this.timeAgo(new Date(alert.created_at));
                
                alertsHtml += `
                    <div class="p-3 bg-gray-50 rounded-lg border-l-4 border-red-500">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-0.5">
                                <span class="w-2 h-2 rounded-full bg-red-500 block"></span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">${deviceName}</p>
                                <p class="text-sm text-gray-500">${message}</p>
                                <p class="text-xs text-gray-400 mt-1" title="${alert.created_at}">${timeAgo}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            alertsContainer.innerHTML = alertsHtml;
        }
        
        addAlertToDashboard(alert) {
            // Add new alert to the alerts list without refreshing everything
            const alertsContainer = document.getElementById('recent-alerts');
            if (!alertsContainer) return;
            
            const deviceName = alert.device_name || 'Unknown Device';
            const message = alert.message.length > 60 ? alert.message.substring(0, 60) + '...' : alert.message;
            const timeAgo = this.timeAgo(new Date(alert.created_at));
            
            const newAlertHtml = `
                <div class="p-3 bg-gray-50 rounded-lg border-l-4 border-red-500 animate-pulse">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-0.5">
                            <span class="w-2 h-2 rounded-full bg-red-500 block"></span>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">${deviceName}</p>
                            <p class="text-sm text-gray-500">${message}</p>
                            <p class="text-xs text-gray-400 mt-1" title="${alert.created_at}">${timeAgo}</p>
                        </div>
                    </div>
                </div>
            `;
            
            // Add at the beginning of the list
            alertsContainer.insertAdjacentHTML('afterbegin', newAlertHtml);
            
            // Remove the animation class after it completes
            setTimeout(() => {
                const firstAlert = alertsContainer.firstElementChild;
                if (firstAlert) {
                    firstAlert.classList.remove('animate-pulse');
                }
            }, 1000);
            
            // Update alert count
            const alertCountElement = document.getElementById('alert-count');
            if (alertCountElement) {
                const currentCount = parseInt(alertCountElement.textContent) || 0;
                alertCountElement.textContent = currentCount + 1;
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
    }
    
    // Initialize dashboard when page loads
    document.addEventListener('DOMContentLoaded', function() {
        new NetworkDashboard();
    });
</script>
@endpush
@endsection