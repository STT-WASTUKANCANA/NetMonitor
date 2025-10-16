@extends('layouts.app')

@section('title', 'Create Device')

@section('header')
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Create Device</h1>
                    <p class="mt-1 text-sm text-gray-500">Add a new device to monitor</p>
                </div>
            </div>
        </div>
    </header>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-card border border-gray-200 p-6">
        <form method="POST" action="{{ route('devices.store') }}">
            @csrf

            <div class="grid grid-cols-1 gap-6">
                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Device Name')" class="block text-sm font-medium text-gray-700 mb-1" />
                    <x-text-input id="name" 
                                 class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                 type="text" 
                                 name="name" 
                                 :value="old('name')" 
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
                                 :value="old('ip_address')" 
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
                             placeholder="Brief description of the device">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- Hierarchy Level -->
                <div>
                    <x-input-label for="hierarchy_level" :value="__('Hierarchy Level')" class="block text-sm font-medium text-gray-700 mb-1" />
                    <select id="hierarchy_level" 
                           name="hierarchy_level" 
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           onchange="toggleParentField()">
                        <option value="utama" {{ old('hierarchy_level') === 'utama' ? 'selected' : '' }}>Utama</option>
                        <option value="sub" {{ old('hierarchy_level') === 'sub' ? 'selected' : '' }}>Sub</option>
                        <option value="device" {{ old('hierarchy_level') === 'device' ? 'selected' : '' }}>Device</option>
                    </select>
                    <x-input-error :messages="$errors->get('hierarchy_level')" class="mt-1 text-sm text-red-600" />
                </div>

                <!-- Parent Device (for sub and device levels) -->
                <div id="parent_field" style="display: {{ in_array(old('hierarchy_level'), ['sub', 'device']) ? 'block' : 'none' }};">
                    <x-input-label for="parent_id" :value="__('Parent Device')" class="block text-sm font-medium text-gray-700 mb-1" />
                    <select id="parent_id" 
                           name="parent_id" 
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Select Parent Device --</option>
                        @foreach($parentDevices as $parentDevice)
                            <option value="{{ $parentDevice->id }}" {{ old('parent_id') == $parentDevice->id ? 'selected' : '' }}>
                                {{ $parentDevice->name }} ({{ $parentDevice->hierarchy_level }})
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('parent_id')" class="mt-1 text-sm text-red-600" />
                    <p class="mt-1 text-sm text-gray-500">Parent device must be 'utama' for 'sub' level, or 'utama'/'sub' for 'device' level</p>
                </div>

                <!-- Device Type -->
                <div>
                    <x-input-label for="type" :value="__('Device Type')" class="block text-sm font-medium text-gray-700 mb-1" />
                    <select id="type" 
                           name="type" 
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="router" {{ old('type') === 'router' ? 'selected' : '' }}>Router</option>
                        <option value="switch" {{ old('type') === 'switch' ? 'selected' : '' }}>Switch</option>
                        <option value="access_point" {{ old('type') === 'access_point' ? 'selected' : '' }}>Access Point</option>
                        <option value="server" {{ old('type') === 'server' ? 'selected' : '' }}>Server</option>
                        <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-1 text-sm text-red-600" />
                </div>
            </div>

            <!-- JavaScript to show/hide parent field -->
            <script>
                function toggleParentField() {
                    const hierarchyLevel = document.getElementById('hierarchy_level').value;
                    const parentField = document.getElementById('parent_field');
                    
                    if (hierarchyLevel === 'utama') {
                        parentField.style.display = 'none';
                    } else {
                        parentField.style.display = 'block';
                    }
                }
            </script>

            <div class="flex items-center justify-end mt-8 space-x-4">
                <a href="{{ route('devices.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    {{ __('Cancel') }}
                </a>
                <x-primary-button class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                    {{ __('Create Device') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection