<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    {{ __('Dashboard') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    {{ $currentDate ?? now()->format('l, d F Y') }}
                </p>
            </div>
            
            <div class="mt-4 md:mt-0 flex items-center space-x-4">
                <div class="flex items-center space-x-2 text-sm">
                    <div class="flex items-center text-green-500">
                        <div class="w-2 h-2 rounded-full bg-green-500 mr-1 animate-pulse"></div>
                        <span>{{ \App\Models\Device::where('status', 'up')->count() }} Online</span>
                    </div>
                    <div class="flex items-center text-red-500">
                        <div class="w-2 h-2 rounded-full bg-red-500 mr-1"></div>
                        <span>{{ \App\Models\Device::where('status', 'down')->count() }} Offline</span>
                    </div>
                </div>
                
                <button id="refresh-btn" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 transition duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Devices -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 transform transition duration-300 hover:scale-[1.02]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Devices</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ \App\Models\Device::count() }}</p>
                    </div>
                    <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/50">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm">
                        <span class="text-green-500 font-medium">+0.00%</span>
                        <span class="text-gray-500 dark:text-gray-400 ml-1">from last week</span>
                    </div>
                </div>
            </div>

            <!-- Online Devices -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 transform transition duration-300 hover:scale-[1.02]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Online Devices</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ \App\Models\Device::where('status', 'up')->count() }}</p>
                    </div>
                    <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/50">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm">
                        <span class="text-green-500 font-medium">+0.00%</span>
                        <span class="text-gray-500 dark:text-gray-400 ml-1">from last week</span>
                    </div>
                </div>
            </div>

            <!-- Offline Devices -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 transform transition duration-300 hover:scale-[1.02]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Offline Devices</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ \App\Models\Device::where('status', 'down')->count() }}</p>
                    </div>
                    <div class="p-3 rounded-lg bg-red-100 dark:bg-red-900/50">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm">
                        <span class="text-red-500 font-medium">+0.00%</span>
                        <span class="text-gray-500 dark:text-gray-400 ml-1">from last week</span>
                    </div>
                </div>
            </div>

            <!-- Active Alerts -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 transform transition duration-300 hover:scale-[1.02]">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Active Alerts</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ \App\Models\Alert::where('status', 'active')->count() }}</p>
                    </div>
                    <div class="p-3 rounded-lg bg-yellow-100 dark:bg-yellow-900/50">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-sm">
                        <span class="text-red-500 font-medium">+0.00%</span>
                        <span class="text-gray-500 dark:text-gray-400 ml-1">from last week</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Device Status Chart -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Device Status Overview</h3>
                <div class="h-80">
                    <canvas id="deviceStatusChart"></canvas>
                </div>
            </div>

            <!-- Recent Alerts -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Alerts</h3>
                    <a href="{{ route('alerts.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
                </div>
                <div class="space-y-4">
                    @forelse(\App\Models\Alert::latest()->take(5)->get() as $alert)
                        <div class="p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    @if($alert->status === 'active')
                                        <div class="w-3 h-3 rounded-full bg-red-500 mt-1"></div>
                                    @else
                                        <div class="w-3 h-3 rounded-full bg-green-500 mt-1"></div>
                                    @endif
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $alert->device->name ?? 'Unknown Device' }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $alert->message }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $alert->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">No alerts found</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Device List -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Active Devices</h3>
                <a href="{{ route('devices.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All Devices</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Device</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">IP Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Last Check</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Response Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse(\App\Models\Device::latest()->take(5)->get() as $device)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r {{ $device->status === 'up' ? 'from-green-400 to-green-500' : 'from-red-400 to-red-500' }} flex items-center justify-center">
                                            <span class="text-white text-sm font-bold">{{ strtoupper(substr($device->name, 0, 1)) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $device->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $device->description }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $device->ip_address }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $device->status === 'up' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200' }}">
                                    {{ ucfirst($device->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $device->last_checked_at ? $device->last_checked_at->diffForHumans() : 'Never' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $device->response_time }}ms</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No devices found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Device Status Chart
        document.addEventListener('DOMContentLoaded', function() {
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
</x-app-layout>