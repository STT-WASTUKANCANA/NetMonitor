<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Devices') }}
        </h2>
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
                                        <div class="flex space-x-2 ml-2">
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
</x-app-layout>