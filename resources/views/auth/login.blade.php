<x-guest-layout>
    <div class="mb-10 text-center">
        <h2 class="text-2xl font-semibold text-gray-900">Sign in to your account</h2>
        <p class="mt-2 text-sm text-gray-500">
            Or 
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    create a new account
                </a>
            @endif
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" class="block text-sm font-medium text-gray-700 mb-1" />
            <x-text-input id="email" 
                         class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                         type="email" 
                         name="email" 
                         :value="old('email')" 
                         required 
                         autofocus 
                         autocomplete="username" 
                         placeholder="name@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-600" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <div class="flex items-center justify-between mb-1">
                <x-input-label for="password" :value="__('Password')" class="block text-sm font-medium text-gray-700" />
                
                @if (Route::has('password.request'))
                    <a class="text-sm text-blue-600 hover:text-blue-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <x-text-input id="password" 
                         class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                         type="password" 
                         name="password" 
                         required 
                         autocomplete="current-password"
                         placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-600" />
        </div>

        <!-- Remember Me -->
        <div class="block mb-4">
            <label for="remember_me" class="flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" name="remember">
                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>