<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    {{ __('Edit Device') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    Update device information: {{ $device->name }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
                <form method="POST" action="{{ route('devices.update', $device) }}">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="mb-6">
                        <x-input-label for="name" :value="__('Device Name')" class="text-gray-700 dark:text-gray-300 mb-2" />
                        <x-text-input 
                            id="name" 
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                            type="text" 
                            name="name" 
                            :value="old('name', $device->name)" 
                            required 
                            autofocus 
                            placeholder="e.g., Router Main Office"
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- IP Address -->
                    <div class="mb-6">
                        <x-input-label for="ip_address" :value="__('IP Address')" class="text-gray-700 dark:text-gray-300 mb-2" />
                        <x-text-input 
                            id="ip_address" 
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                            type="text" 
                            name="ip_address" 
                            :value="old('ip_address', $device->ip_address)" 
                            required 
                            placeholder="e.g., 192.168.1.1"
                        />
                        <x-input-error :messages="$errors->get('ip_address')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <x-input-label for="description" :value="__('Description')" class="text-gray-700 dark:text-gray-300 mb-2" />
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="3" 
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            placeholder="Brief description of the device"
                        >{{ old('description', $device->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Monitoring Interval -->
                    <div class="mb-6">
                        <x-input-label for="monitoring_interval" :value="__('Monitoring Interval (seconds)')" class="text-gray-700 dark:text-gray-300 mb-2" />
                        <x-text-input 
                            id="monitoring_interval" 
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                            type="number" 
                            name="monitoring_interval" 
                            :value="old('monitoring_interval', $device->monitoring_interval)" 
                            required 
                            min="10" 
                            max="3600"
                        />
                        <x-input-error :messages="$errors->get('monitoring_interval')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Port -->
                    <div class="mb-6">
                        <x-input-label for="port" :value="__('Port')" class="text-gray-700 dark:text-gray-300 mb-2" />
                        <x-text-input 
                            id="port" 
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                            type="number" 
                            name="port" 
                            :value="old('port', $device->port)" 
                            min="1" 
                            max="65535"
                        />
                        <x-input-error :messages="$errors->get('port')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Device Type -->
                    <div class="mb-8">
                        <x-input-label for="device_type" :value="__('Device Type')" class="text-gray-700 dark:text-gray-300 mb-2" />
                        <select 
                            id="device_type" 
                            name="device_type" 
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                        >
                            <option value="router" {{ old('device_type', $device->device_type) === 'router' ? 'selected' : '' }}>Router</option>
                            <option value="switch" {{ old('device_type', $device->device_type) === 'switch' ? 'selected' : '' }}>Switch</option>
                            <option value="server" {{ old('device_type', $device->device_type) === 'server' ? 'selected' : '' }}>Server</option>
                            <option value="printer" {{ old('device_type', $device->device_type) === 'printer' ? 'selected' : '' }}>Printer</option>
                            <option value="workstation" {{ old('device_type', $device->device_type) === 'workstation' ? 'selected' : '' }}>Workstation</option>
                            <option value="other" {{ old('device_type', $device->device_type) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <x-input-error :messages="$errors->get('device_type')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('devices.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium rounded-lg transition duration-200">
                            {{ __('Cancel') }}
                        </a>
                        
                        <x-primary-button class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-lg shadow-md transition duration-300 transform hover:scale-105">
                            {{ __('Update Device') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>