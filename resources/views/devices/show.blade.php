<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    {{ __('Device Details') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    Information for: {{ $device->name }}
                </p>
            </div>
            <div class="mt-4 md:mt-0 flex items-center space-x-3">
                <a href="{{ route('devices.edit', $device) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow transition duration-200">
                    {{ __('Edit') }}
                </a>
                <form method="POST" action="{{ route('devices.destroy', $device) }}" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow transition duration-200" onclick="return confirm('Are you sure you want to delete this device?')">
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Device Info Card -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Device Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Device Name</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $device->name }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">IP Address</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $device->ip_address }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $device->description ?: 'No description' }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Device Type</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ ucfirst($device->device_type) }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
                            <div class="mt-1">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $device->status === 'up' ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200' }}">
                                    {{ ucfirst($device->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Checked</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-white">
                                {{ $device->last_checked_at ? $device->last_checked_at->diffForHumans() : 'Never checked' }}
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Response Time</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-white">
                                {{ $device->response_time }}ms
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Monitoring Interval</p>
                            <p class="text-lg font-semibold text-gray-800 dark:text-white">
                                {{ $device->monitoring_interval }} seconds
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status History Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Status History</h3>
                
                <!-- Placeholder for chart -->
                <div class="h-64 flex items-center justify-center bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div class="text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">Status chart will appear here</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Alerts -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Recent Alerts</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Message</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($device->alerts()->latest()->take(5)->get() as $alert)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $alert->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $alert->message }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $alert->status === 'active' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200' : 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200' }}">
                                    {{ ucfirst($alert->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No alerts found for this device</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>