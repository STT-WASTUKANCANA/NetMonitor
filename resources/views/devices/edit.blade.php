@extends('layouts.app')

@section('title', 'Edit Device')

@section('header')
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Edit Device</h1>
                    <p class="mt-1 text-sm text-gray-500">Update device information: {{ $device->name }}</p>
                </div>
            </div>
        </div>
    </header>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-card border border-gray-200 p-6">
        <form method="POST" action="{{ route('devices.update', $device) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Device Name')" class="block text-sm font-medium text-gray-700 mb-1" />
                    <x-text-input id="name" 
                                 class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                 type="text" 
                                 name="name" 
                                 :value="old('name', $device->name)" 
                                 required 
                                 autofocus 
                                 placeholder="e.g., Main Router" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- IP Address -->
                <div>
                    <x-input-label for="ip_address" :value="__('IP Address')" class="block text-sm font-medium text-gray-700 mb-1" />
                    <x-text-input id="ip_address" 
                                 class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                 type="text" 
                                 name="ip_address" 
                                 :value="old('ip_address', $device->ip_address)" 
                                 required 
                                 placeholder="e.g., 192.168.1.1" />
                    <x-input-error :messages="$errors->get('ip_address')" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- Description -->
                <div>
                    <x-input-label for="description" :value="__('Description')" class="block text-sm font-medium text-gray-700 mb-1" />
                    <textarea id="description" 
                             name="description" 
                             rows="3" 
                             class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                             placeholder="Brief description of the device">{{ old('description', $device->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- Monitoring Interval -->
                <div>
                    <x-input-label for="monitoring_interval" :value="__('Monitoring Interval (seconds)')" class="block text-sm font-medium text-gray-700 mb-1" />
                    <x-text-input id="monitoring_interval" 
                                 class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                 type="number" 
                                 name="monitoring_interval" 
                                 :value="old('monitoring_interval', $device->monitoring_interval)" 
                                 required 
                                 min="10" 
                                 max="3600" />
                    <x-input-error :messages="$errors->get('monitoring_interval')" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- Port -->
                <div>
                    <x-input-label for="port" :value="__('Port')" class="block text-sm font-medium text-gray-700 mb-1" />
                    <x-text-input id="port" 
                                 class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                 type="number" 
                                 name="port" 
                                 :value="old('port', $device->port)" 
                                 min="1" 
                                 max="65535"
                                 placeholder="e.g., 80, 443, 22" />
                    <x-input-error :messages="$errors->get('port')" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- Device Type -->
                <div>
                    <x-input-label for="device_type" :value="__('Device Type')" class="block text-sm font-medium text-gray-700 mb-1" />
                    <select id="device_type" 
                           name="device_type" 
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="router" {{ old('device_type', $device->device_type) === 'router' ? 'selected' : '' }}>Router</option>
                        <option value="switch" {{ old('device_type', $device->device_type) === 'switch' ? 'selected' : '' }}>Switch</option>
                        <option value="server" {{ old('device_type', $device->device_type) === 'server' ? 'selected' : '' }}>Server</option>
                        <option value="printer" {{ old('device_type', $device->device_type) === 'printer' ? 'selected' : '' }}>Printer</option>
                        <option value="workstation" {{ old('device_type', $device->device_type) === 'workstation' ? 'selected' : '' }}>Workstation</option>
                        <option value="other" {{ old('device_type', $device->device_type) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    <x-input-error :messages="$errors->get('device_type')" class="mt-1 text-sm text-red-600" />
                </div>
            </div>

            <div class="flex items-center justify-end mt-8 space-x-4">
                <a href="{{ route('devices.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    {{ __('Cancel') }}
                </a>
                <x-primary-button class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                    {{ __('Update Device') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection