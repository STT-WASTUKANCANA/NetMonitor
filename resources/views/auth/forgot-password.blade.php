<x-guest-layout>
    <div class="p-6">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">Reset Password</h1>
            <p class="text-gray-600 dark:text-gray-400">Enter your email to receive reset instructions</p>
        </div>

        <div class="mb-6 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-6" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-8">
                <x-input-label for="email" :value="__('Email')" class="text-gray-700 dark:text-gray-300 mb-2" />
                <x-text-input 
                    id="email" 
                    class="block w-full px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                />
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
            </div>

            <x-primary-button class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-lg shadow-md transition duration-300 transform hover:scale-105">
                {{ __('Email Password Reset Link') }}
            </x-primary-button>
        </form>

        <div class="mt-6 text-center">
            <a class="text-sm text-blue-600 dark:text-blue-400 hover:underline transition duration-200" href="{{ route('login') }}">
                {{ __('Back to login') }}
            </a>
        </div>
    </div>
</x-guest-layout>