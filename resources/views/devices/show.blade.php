@extends('layouts.app')

@section('title', 'Device Details')

@section('header')
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Device Details</h1>
                    <p class="mt-1 text-sm text-gray-500">Information for: {{ $device->name }}</p>
                </div>
                <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                    <a href="{{ route('devices.edit', $device) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                        {{ __('Edit') }}
                    </a>
                    <form method="POST" action="{{ route('devices.destroy', $device) }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow-sm transition-colors" onclick="return confirm('Are you sure you want to delete this device?')">
                            {{ __('Delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Device Info Card -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-card border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Device Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Device Name</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">{{ $device->name }}</p>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">IP Address</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">{{ $device->ip_address }}</p>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Description</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">{{ $device->description ?: 'No description' }}</p>
                </div>
            </div>
            
            <div>
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Status</h3>
                    <div class="mt-1">
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                            {{ $device->status === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($device->status) }}
                        </span>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Last Checked</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">
                        {{ $device->last_checked_at ? $device->last_checked_at->diffForHumans() : 'Never checked' }}
                    </p>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Response Time</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">
                        {{ $device->response_time }}ms
                    </p>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Device Type</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900">
                        {{ ucfirst($device->device_type) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Card -->
    <div class="bg-white rounded-xl shadow-card border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h2>
        
        <div class="space-y-4">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Monitoring Interval</span>
                <span class="text-sm font-medium text-gray-900">{{ $device->monitoring_interval }}s</span>
            </div>
            
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Port</span>
                <span class="text-sm font-medium text-gray-900">{{ $device->port }}</span>
            </div>
            
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Created</span>
                <span class="text-sm font-medium text-gray-900">{{ $device->created_at->format('M j, Y') }}</span>
            </div>
            
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Total Alerts</span>
                <span class="text-sm font-medium text-gray-900">{{ $device->alerts()->count() }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Recent Alerts -->
<div class="mt-6 bg-white rounded-xl shadow-card border border-gray-200 p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Alerts</h2>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($device->alerts()->latest()->take(5)->get() as $alert)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $alert->created_at->format('M j, Y H:i') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ Str::limit($alert->message, 50) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $alert->status === 'active' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst($alert->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No alerts found for this device</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection