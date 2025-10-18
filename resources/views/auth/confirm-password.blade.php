<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-violet-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-10 animate-fade-in">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-r from-violet-500 to-purple-600 mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-violet-600 to-purple-600 dark:from-violet-400 dark:to-purple-400">
                    NetMonitor
                </h1>
                <h2 class="mt-4 text-2xl font-semibold text-gray-800 dark:text-gray-200">Secure access</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Confirm your password to continue
                </p>
            </div>

            <div class="relative bg-white/80 dark:bg-gray-800/80 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 dark:border-gray-700/50 p-8 transition-all duration-300 hover:shadow-2xl animate-slide-up">
                <div class="mb-6 text-center text-gray-600 dark:text-gray-300 animate-fade-in-delay-100">
                    {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
                </div>

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <!-- Password -->
                    <div class="mb-8 animate-fade-in-delay-200">
                        <x-input-label for="password" :value="__('Password')" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <x-text-input id="password" 
                                         class="block w-full pl-10 pr-3 py-3 rounded-lg border-gray-300 dark:border-gray-600 bg-white/50 dark:bg-gray-700/50 focus:border-violet-500 focus:ring-violet-500 dark:focus:border-violet-400 transition-all duration-200" 
                                         type="password" 
                                         name="password" 
                                         required 
                                         autocomplete="current-password"
                                         placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600 dark:text-red-400" />
                    </div>

                    <div class="animate-fade-in-delay-300">
                        <x-primary-button class="w-full py-3 px-4 bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-medium rounded-lg shadow-lg transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                            {{ __('Confirm Access') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <div class="mt-6 text-center animate-fade-in-delay-400">
                <p class="text-gray-600 dark:text-gray-400">
                    <a href="{{ route('logout') }}" class="font-medium text-violet-600 hover:text-violet-500 dark:text-violet-400 dark:hover:text-violet-300 transition-colors duration-200">
                        {{ __('Sign out') }}
                    </a>
                </p>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slide-up {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fade-in 0.6s ease-out forwards;
        }
        
        .animate-slide-up {
            animation: slide-up 0.6s ease-out 0.2s forwards;
        }
        
        .animate-fade-in-delay-100 {
            animation: fade-in 0.6s ease-out 0.1s forwards;
        }
        
        .animate-fade-in-delay-200 {
            animation: fade-in 0.6s ease-out 0.2s forwards;
        }
        
        .animate-fade-in-delay-300 {
            animation: fade-in 0.6s ease-out 0.3s forwards;
        }
        
        .animate-fade-in-delay-400 {
            animation: fade-in 0.6s ease-out 0.4s forwards;
        }
    </style>
</x-guest-layout>