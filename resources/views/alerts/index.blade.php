<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Alert Notifications') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Riwayat Peringatan</h3>
                        <div class="flex space-x-2">
                            <a href="{{ route('alerts.index', ['status' => 'active']) }}" 
                               class="px-3 py-1 text-sm rounded-full {{ request('status', 'all') === 'active' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                Aktif ({{ \App\Models\Alert::where('status', 'active')->count() }})
                            </a>
                            <a href="{{ route('alerts.index', ['status' => 'resolved']) }}" 
                               class="px-3 py-1 text-sm rounded-full {{ request('status') === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                Selesai ({{ \App\Models\Alert::where('status', 'resolved')->count() }})
                            </a>
                            <a href="{{ route('alerts.index', ['status' => 'all']) }}" 
                               class="px-3 py-1 text-sm rounded-full {{ request('status') === 'all' || !request('status') ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                Semua
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Perangkat</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jenis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Pesan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Waktu</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($alerts as $alert)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                <a href="{{ route('devices.show', $alert->device) }}" class="text-blue-500 hover:text-blue-700">
                                                    {{ $alert->device->name }}
                                                </a>
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $alert->device->ip_address }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $alert->type === 'device_down' ? 'bg-red-100 text-red-800' : 
                                                   ($alert->type === 'device_up' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $alert->type === 'device_down' ? 'Perangkat Down' : 
                                                   ($alert->type === 'device_up' ? 'Perangkat Up' : 'Latensi Tinggi') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $alert->status === 'active' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $alert->status === 'active' ? 'Aktif' : 'Selesai' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs">
                                            {{ Str::limit($alert->message, 50) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <div>{{ $alert->created_at->format('d M Y H:i:s') }}</div>
                                            @if($alert->resolved_at)
                                                <div class="text-xs text-gray-400">Selesai: {{ $alert->resolved_at->format('d M Y H:i:s') }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @can('resolve alerts')
                                                @if($alert->status === 'active')
                                                    <form action="{{ route('alerts.update', $alert) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="resolved">
                                                        <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Mark as resolved?')">
                                                            Tandai Selesai
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            Tidak ada peringatan ditemukan
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $alerts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>