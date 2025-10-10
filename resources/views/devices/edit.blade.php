<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Device') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('devices.update', $device) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit Informasi Perangkat</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Perbarui detail perangkat yang dipantau</p>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Perangkat *</label>
                                    <input type="text" name="name" id="name" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('name') border-red-500 @enderror" value="{{ old('name', $device->name) }}" required>
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="ip_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat IP *</label>
                                    <input type="text" name="ip_address" id="ip_address" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('ip_address') border-red-500 @enderror" value="{{ old('ip_address', $device->ip_address) }}" required>
                                    @error('ip_address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Alamat IP yang dapat diakses jaringan</p>
                                </div>

                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Perangkat *</label>
                                    <select name="type" id="type" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('type') border-red-500 @enderror" required>
                                        <option value="router" {{ old('type', $device->type) === 'router' ? 'selected' : '' }}>Router</option>
                                        <option value="switch" {{ old('type', $device->type) === 'switch' ? 'selected' : '' }}>Switch</option>
                                        <option value="access_point" {{ old('type', $device->type) === 'access_point' ? 'selected' : '' }}>Access Point</option>
                                        <option value="server" {{ old('type', $device->type) === 'server' ? 'selected' : '' }}>Server</option>
                                        <option value="other" {{ old('type', $device->type) === 'other' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="hierarchy_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Level Hierarki *</label>
                                    <select name="hierarchy_level" id="hierarchy_level" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('hierarchy_level') border-red-500 @enderror" required>
                                        <option value="utama" {{ old('hierarchy_level', $device->hierarchy_level) === 'utama' ? 'selected' : '' }}>Utama</option>
                                        <option value="sub" {{ old('hierarchy_level', $device->hierarchy_level) === 'sub' ? 'selected' : '' }}>Sub</option>
                                        <option value="device" {{ old('hierarchy_level', $device->hierarchy_level) === 'device' ? 'selected' : '' }}>Device</option>
                                    </select>
                                    @error('hierarchy_level')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Level dalam hirarki jaringan</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Perangkat Induk</label>
                                    <select name="parent_id" id="parent_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                        <option value="" {{ $device->parent_id ? '' : 'selected' }}>Tidak ada (Perangkat Utama)</option>
                                        @foreach($parentDevices as $parentDevice)
                                            <option value="{{ $parentDevice->id }}" {{ old('parent_id', $device->parent_id) == $parentDevice->id ? 'selected' : '' }}>
                                                {{ $parentDevice->name }} ({{ $parentDevice->ip_address }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pilih perangkat induk jika ini adalah sub-device</p>
                                </div>

                                <div>
                                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lokasi</label>
                                    <input type="text" name="location" id="location" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('location') border-red-500 @enderror" value="{{ old('location', $device->location) }}">
                                    @error('location')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                                    <textarea name="description" id="description" rows="3" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('description') border-red-500 @enderror">{{ old('description', $device->description) }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex items-center">
                                    <div class="flex items-center h-5">
                                        <input id="is_active" name="is_active" type="checkbox" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600" {{ old('is_active', $device->is_active) ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_active" class="font-medium text-gray-700 dark:text-gray-300">Aktif</label>
                                        <p class="text-gray-500 dark:text-gray-400">Perangkat akan dipantau secara aktif</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end space-x-3">
                            <a href="{{ route('devices.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Perbarui Perangkat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>