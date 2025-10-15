<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                    {{ __('Profile') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    Manage your account settings and preferences
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <!-- Status Messages -->
            @if(session('status') === 'profile-updated')
                <div class="mb-6 rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                    {{ __('Profile updated successfully.') }}
                </div>
            @endif

            @if(session('status') === 'password-updated')
                <div class="mb-6 rounded-lg bg-green-50 dark:bg-green-900/30 p-4 text-green-800 dark:text-green-200 border border-green-200 dark:border-green-800">
                    {{ __('Password updated successfully.') }}
                </div>
            @endif

            <!-- Profile Information Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Profile Information</h3>
                
                <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('patch')

                    <!-- Name Field -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Name')" class="text-gray-700 dark:text-gray-300 mb-2" />
                            <x-text-input 
                                id="name" 
                                name="name" 
                                type="text" 
                                class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                :value="old('name', $user->name)" 
                                required 
                                autofocus 
                                autocomplete="name" 
                            />
                            <x-input-error class="mt-2 text-red-500 text-sm" :messages="$errors->get('name')" />
                        </div>

                        <!-- Email Field -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" class="text-gray-700 dark:text-gray-300 mb-2" />
                            <x-text-input 
                                id="email" 
                                name="email" 
                                type="email" 
                                class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                :value="old('email', $user->email)" 
                                required 
                                autocomplete="email" 
                            />
                            <x-input-error class="mt-2 text-red-500 text-sm" :messages="$errors->get('email')" />
                        </div>
                    </div>

                    <!-- Email Verification -->
                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                        <div class="mt-4">
                            <p class="text-sm text-gray-800 dark:text-gray-200">
                                {{ __('Your email address is unverified.') }}

                                <form method="post" action="{{ route('verification.send') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="ml-2 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        {{ __('Click here to re-send the verification email.') }}
                                    </button>
                                </form>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            @endif
                        </div>
                    @endif

                    <div class="flex items-center justify-end">
                        <x-primary-button class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-lg shadow-md transition duration-300 transform hover:scale-105">
                            {{ __('Save') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Update Password Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Update Password</h3>
                
                <form method="post" action="{{ route('password.update') }}" class="space-y-6">
                    @csrf
                    @method('put')

                    <!-- Current Password -->
                    <div>
                        <x-input-label for="update_password_current_password" :value="__('Current Password')" class="text-gray-700 dark:text-gray-300 mb-2" />
                        <x-text-input 
                            id="update_password_current_password" 
                            name="current_password" 
                            type="password" 
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                            autocomplete="current-password" 
                        />
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- New Password -->
                    <div>
                        <x-input-label for="update_password_password" :value="__('New Password')" class="text-gray-700 dark:text-gray-300 mb-2" />
                        <x-text-input 
                            id="update_password_password" 
                            name="password" 
                            type="password" 
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                            autocomplete="new-password" 
                        />
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Confirm New Password -->
                    <div>
                        <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="text-gray-700 dark:text-gray-300 mb-2" />
                        <x-text-input 
                            id="update_password_password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                            autocomplete="new-password" 
                        />
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="flex items-center justify-end">
                        <x-primary-button class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-lg shadow-md transition duration-300 transform hover:scale-105">
                            {{ __('Update Password') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- Delete Account Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-4">Delete Account</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
                </p>
                
                <div class="flex items-center justify-end">
                    <x-danger-button
                        x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                        class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-medium rounded-lg shadow-md transition duration-300 transform hover:scale-105"
                    >
                        {{ __('Delete Account') }}
                    </x-danger-button>
                </div>

                <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                    <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                        @csrf
                        @method('delete')

                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('Are you sure you want to delete your account?') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <div class="mt-6">
                            <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                            <x-text-input
                                id="password"
                                name="password"
                                type="password"
                                class="mt-1 block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition duration-200"
                                placeholder="{{ __('Password') }}"
                            />

                            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-red-500 text-sm" />
                        </div>

                        <div class="mt-6 flex justify-end space-x-4">
                            <x-secondary-button x-on:click="$dispatch('close')">
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <x-danger-button class="ms-3">
                                {{ __('Delete Account') }}
                            </x-danger-button>
                        </div>
                    </form>
                </x-modal>
            </div>
        </div>
    </div>
</x-app-layout>