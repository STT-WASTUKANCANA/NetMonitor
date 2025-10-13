<x-app-layout>
    <x-slot name="header">
        <div class="mb-2">
            <!-- Judul Halaman -->
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Create Device') }}
            </h2>

            <!-- Breadcrumb -->
            <nav class="flex mt-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-600 transition-colors">
                            <svg class="w-4 h-4 mr-2 text-gray-400 hover:text-blue-500 transition-colors" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-black mx-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <a href="{{ route('devices.index') }}"
                                class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-600 transition-colors">
                                Manage Devices
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-black mx-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium text-black">Create Device</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-xl">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('devices.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informasi Perangkat</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Masukkan detail perangkat yang akan dipantau</p>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Perangkat *</label>
                                    <input type="text" name="name" id="name" class="block w-full rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('name') border-red-500 @enderror" value="{{ old('name') }}" required>
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="ip_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat IP *</label>
                                    <input type="text" name="ip_address" id="ip_address" class="block w-full rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('ip_address') border-red-500 @enderror" value="{{ old('ip_address') }}" required>
                                    @error('ip_address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Alamat IP yang dapat diakses jaringan</p>
                                </div>

                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Perangkat *</label>
                                    <select name="type" id="type" class="block w-full rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('type') border-red-500 @enderror" required>
                                        <option value="" disabled {{ old('type') ? '' : 'selected' }}>Pilih tipe perangkat</option>
                                        <option value="router" {{ old('type') === 'router' ? 'selected' : '' }}>Router</option>
                                        <option value="switch" {{ old('type') === 'switch' ? 'selected' : '' }}>Switch</option>
                                        <option value="access_point" {{ old('type') === 'access_point' ? 'selected' : '' }}>Access Point</option>
                                        <option value="server" {{ old('type') === 'server' ? 'selected' : '' }}>Server</option>
                                        <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="hierarchy_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Level Hirarki *</label>
                                    <select name="hierarchy_level" id="hierarchy_level" class="block w-full rounded-lg  shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('hierarchy_level') border-red-500 @enderror" required>
                                        <option value="" disabled {{ old('hierarchy_level') ? '' : 'selected' }}>Pilih level hirarki</option>
                                        <option value="utama" {{ old('hierarchy_level') === 'utama' ? 'selected' : '' }}>Utama</option>
                                        <option value="sub" {{ old('hierarchy_level') === 'sub' ? 'selected' : '' }}>Sub</option>
                                        <option value="device" {{ old('hierarchy_level') === 'device' ? 'selected' : '' }}>Device</option>
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
                                    <select name="parent_id" id="parent_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        <option value="" {{ old('parent_id') ? '' : 'selected' }}>Tidak ada (Perangkat Utama)</option>
                                        @foreach($parentDevices as $parentDevice)
                                            <option value="{{ $parentDevice->id }}" {{ old('parent_id') == $parentDevice->id ? 'selected' : '' }}>
                                                {{ $parentDevice->name }} ({{ $parentDevice->ip_address }}) - {{ ucfirst($parentDevice->hierarchy_level) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pilih perangkat induk jika ini adalah sub-device</p>
                                </div>

                                <div>
                                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lokasi</label>
                                    <input type="text" name="location" id="location" class="block w-full rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('location') border-red-500 @enderror" value="{{ old('location') }}">
                                    @error('location')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                                    <textarea name="description" id="description" rows="3" class="block w-full rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex items-center">
                                    <div class="flex items-center h-5">
                                        <input id="is_active" name="is_active" type="checkbox" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600" {{ old('is_active') !== null ? 'checked' : 'checked' }}>
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
                                Simpan Perangkat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>