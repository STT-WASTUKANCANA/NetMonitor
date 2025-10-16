@extends('layouts.app')

@section('title', 'Profile')

@section('header')
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Profile</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage your account settings and preferences</p>
                </div>
            </div>
        </div>
    </header>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Status Messages -->
    @if(session('status') === 'profile-updated')
        <div class="rounded-xl bg-green-50 border border-green-200 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">Profile updated successfully.</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('status') === 'password-updated')
        <div class="rounded-xl bg-green-50 border border-green-200 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">Password updated successfully.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Profile Information Card -->
    <div class="bg-white rounded-xl shadow-card border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h2>
        
        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('patch')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="name" :value="__('Name')" class="block text-sm font-medium text-gray-700 mb-1" />
                    <x-text-input id="name" 
                                 name="name" 
                                 type="text" 
                                 class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                 :value="old('name', $user->name)" 
                                 required 
                                 autofocus 
                                 autocomplete="name" 
                                 placeholder="John Doe" />
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="email" :value="__('Email')" class="block text-sm font-medium text-gray-700 mb-1" />
                    <x-text-input id="email" 
                                 name="email" 
                                 type="email" 
                                 class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                 :value="old('email', $user->email)" 
                                 required 
                                 autocomplete="email"
                                 placeholder="name@company.com" />
                    <x-input-error class="mt-1 text-sm text-red-600" :messages="$errors->get('email')" />
                </div>
            </div>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <form method="post" action="{{ route('verification.send') }}" class="inline">
                            @csrf
                            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </form>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif

            <div class="flex items-center gap-4">
                <x-primary-button class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                    {{ __('Save') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <!-- Update Password Card -->
    <div class="bg-white rounded-xl shadow-card border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Update Password</h2>
        
        <form method="post" action="{{ route('password.update') }}" class="space-y-6">
            @csrf
            @method('put')

            <div>
                <x-input-label for="update_password_current_password" :value="__('Current Password')" class="block text-sm font-medium text-gray-700 mb-1" />
                <x-text-input id="update_password_current_password" 
                             name="current_password" 
                             type="password" 
                             class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                             autocomplete="current-password"
                             placeholder="••••••••" />
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1 text-sm text-red-600" />
            </div>

            <div>
                <x-input-label for="update_password_password" :value="__('New Password')" class="block text-sm font-medium text-gray-700 mb-1" />
                <x-text-input id="update_password_password" 
                             name="password" 
                             type="password" 
                             class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                             autocomplete="new-password"
                             placeholder="••••••••" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1 text-sm text-red-600" />
            </div>

            <div>
                <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="block text-sm font-medium text-gray-700 mb-1" />
                <x-text-input id="update_password_password_confirmation" 
                             name="password_confirmation" 
                             type="password" 
                             class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                             autocomplete="new-password"
                             placeholder="••••••••" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1 text-sm text-red-600" />
            </div>

            <div class="flex items-center gap-4">
                <x-primary-button class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                    {{ __('Update Password') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <!-- Delete Account Card -->
    <div class="bg-white rounded-xl shadow-card border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Delete Account</h2>
        <p class="text-sm text-gray-600 mb-4">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
        
        <div class="flex items-center">
            <x-danger-button
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow-sm transition-colors"
            >
                {{ __('Delete Account') }}
            </x-danger-button>
        </div>

        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                @csrf
                @method('delete')

                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Are you sure you want to delete your account?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>

                <div class="mt-6">
                    <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                    <x-text-input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                        placeholder="{{ __('Password') }}"
                    />

                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-sm text-red-600" />
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
@endsection