<x-guest-layout>
    <div class="mb-10 text-center">
        <h2 class="text-2xl font-semibold text-gray-900">Reset your password</h2>
        <p class="mt-2 text-sm text-gray-500">
            Enter your new password below
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" class="block text-sm font-medium text-gray-700 mb-1" />
            <x-text-input id="email" 
                         class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                         type="email" 
                         name="email" 
                         :value="old('email', $request->email)" 
                         required 
                         autofocus 
                         autocomplete="username"
                         placeholder="name@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-600" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" class="block text-sm font-medium text-gray-700 mb-1" />
            <x-text-input id="password" 
                         class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                         type="password" 
                         name="password" 
                         required 
                         autocomplete="new-password"
                         placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-600" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-6">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="block text-sm font-medium text-gray-700 mb-1" />
            <x-text-input id="password_confirmation" 
                         class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                         type="password" 
                         name="password_confirmation" 
                         required 
                         autocomplete="new-password"
                         placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-sm text-red-600" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>