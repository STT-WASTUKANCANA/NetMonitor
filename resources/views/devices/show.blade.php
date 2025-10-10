<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Device Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
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
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="font-medium mb-2">Informasi Perangkat</h4>
                            <ul class="space-y-1">
                                <li class="flex">
                                    <span class="w-32 text-gray-600 dark:text-gray-400">IP Address</span>
                                    <span class="font-medium">{{ $device->ip_address }}</span>
                                </li>
                                <li class="flex">
                                    <span class="w-32 text-gray-600 dark:text-gray-400">Tipe</span>
                                    <span class="font-medium">{{ $device->type }}</span>
                                </li>
                                <li class="flex">
                                    <span class="w-32 text-gray-600 dark:text-gray-400">Lokasi</span>
                                    <span class="font-medium">{{ $device->location ?: '-' }}</span>
                                </li>
                                <li class="flex">
                                    <span class="w-32 text-gray-600 dark:text-gray-400">Status</span>
                                    <span class="font-medium">
                                        <span class="{{ $device->status === 'up' ? 'text-green-600' : 
                                           ($device->status === 'down' ? 'text-red-600' : 'text-gray-600') }}">
                                            {{ $device->status }}
                                        </span>
                                    </span>
                                </li>
                                <li class="flex">
                                    <span class="w-32 text-gray-600 dark:text-gray-400">Aktif</span>
                                    <span class="font-medium">{{ $device->is_active ? 'Ya' : 'Tidak' }}</span>
                                </li>
                                <li class="flex">
                                    <span class="w-32 text-gray-600 dark:text-gray-400">Terakhir Dicek</span>
                                    <span class="font-medium">{{ $device->last_checked_at ? $device->last_checked_at->diffForHumans() : 'Belum pernah dicek' }}</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <h4 class="font-medium mb-2">Deskripsi</h4>
                            <p class="text-gray-700 dark:text-gray-300">{{ $device->description ?: '-' }}</p>
                            
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
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $child->ip_address }}</p>
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
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Waktu Respons</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Waktu</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pesan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($device->logs->sortByDesc('logged_at')->take(10) as $log)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $log->status === 'up' ? 'bg-green-100 text-green-800' : 
                                                           ($log->status === 'down' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                                        {{ $log->status }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $log->response_time ? $log->response_time . ' ms' : '-' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $log->logged_at->format('d M Y H:i:s') }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs">
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
</x-app-layout>