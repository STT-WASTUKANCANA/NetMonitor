@extends('layouts.app')

@section('title', 'Dashboard')

@section('header')
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $currentDate ?? now()->format('l, F j, Y') }}
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-4">
                    <div class="flex items-center text-sm text-gray-500">
                        <span class="flex items-center">
                            <span class="w-2 h-2 rounded-full bg-green-400 mr-1"></span>
                            {{ \App\Models\Device::where('status', 'up')->count() }} Online
                        </span>
                        <span class="flex items-center ml-4">
                            <span class="w-2 h-2 rounded-full bg-red-400 mr-1"></span>
                            {{ \App\Models\Device::where('status', 'down')->count() }} Offline
                        </span>
                    </div>
                    <button id="refresh-btn" class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 transition-colors">
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
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Device::count() }}</p>
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
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Device::where('status', 'up')->count() }}</p>
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
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Device::where('status', 'down')->count() }}</p>
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
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Alert::where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Device Status Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-card p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Device Status Overview</h3>
            <div class="h-80">
                <canvas id="deviceStatusChart"></canvas>
            </div>
        </div>

        <!-- Recent Alerts -->
        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Alerts</h3>
                <a href="{{ route('alerts.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
            <div class="space-y-4">
                @forelse(\App\Models\Alert::latest()->take(5)->get() as $alert)
                    <div class="p-3 bg-gray-50 rounded-lg">
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
                                <p class="text-xs text-gray-400 mt-1">{{ $alert->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No alerts found</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Active Devices -->
    <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200">
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
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse(\App\Models\Device::latest()->take(5)->get() as $device)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-blue-800 font-medium">{{ strtoupper(substr($device->name, 0, 1)) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $device->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $device->description }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $device->ip_address }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $device->status === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($device->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $device->response_time }}ms</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Device Status Chart
        const ctx = document.getElementById('deviceStatusChart').getContext('2d');
        const onlineCount = {{ \App\Models\Device::where('status', 'up')->count() }};
        const offlineCount = {{ \App\Models\Device::where('status', 'down')->count() }};
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Online', 'Offline'],
                datasets: [{
                    data: [onlineCount, offlineCount],
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
                                const total = onlineCount + offlineCount;
                                const percentage = total ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection