<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    {{ __('Edit User') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    Update user information: {{ $user->name }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
                <form method="POST" action="{{ route('users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="mb-6">
                        <x-input-label for="name" :value="__('Name')" class="text-gray-700 dark:text-gray-300 mb-2" />
                        <x-text-input 
                            id="name" 
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                            type="text" 
                            name="name" 
                            :value="old('name', $user->name)" 
                            required 
                            autofocus 
                            placeholder="Enter user's full name"
                        />
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Email Address -->
                    <div class="mb-6">
                        <x-input-label for="email" :value="__('Email')" class="text-gray-700 dark:text-gray-300 mb-2" />
                        <x-text-input 
                            id="email" 
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                            type="email" 
                            name="email" 
                            :value="old('email', $user->email)" 
                            required 
                            placeholder="user@example.com"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Password (Optional) -->
                    <div class="mb-6">
                        <x-input-label for="password" :value="__('New Password (Optional)')" class="text-gray-700 dark:text-gray-300 mb-2" />
                        <x-text-input 
                            id="password" 
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                            type="password" 
                            name="password" 
                            autocomplete="new-password" 
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Leave blank to keep current password</p>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-8">
                        <x-input-label for="password_confirmation" :value="__('Confirm New Password')" class="text-gray-700 dark:text-gray-300 mb-2" />
                        <x-text-input 
                            id="password_confirmation" 
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                            type="password" 
                            name="password_confirmation" 
                            autocomplete="new-password" 
                        />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('users.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 font-medium rounded-lg transition duration-200">
                            {{ __('Cancel') }}
                        </a>
                        
                        <x-primary-button class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-lg shadow-md transition duration-300 transform hover:scale-105">
                            {{ __('Update User') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>