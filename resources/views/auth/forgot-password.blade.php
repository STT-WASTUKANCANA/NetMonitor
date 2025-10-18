<x-guest-layout>
    <div class=" flex items-center justify-center bg-gradient-to-br from-amber-50 via-white to-orange-50 p-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-10 animate-fade-in">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-r from-amber-500 to-orange-600 mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-amber-600 to-orange-600">
                    NetMonitor
                </h1>
                <h2 class="mt-4 text-2xl font-semibold text-gray-800">Reset your password</h2>
                <p class="mt-2 text-gray-600">
                    Enter your email to receive reset instructions
                </p>
            </div>

            <div class="relative bg-white/70 backdrop-blur-lg rounded-2xl shadow-xl border border-white/30 p-8 transition-all duration-300 hover:shadow-2xl animate-slide-up">
                <div class="mb-6 text-center text-gray-600 animate-fade-in-delay-100">
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-6 animate-fade-in-delay-200" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-8 animate-fade-in-delay-300">
                        <x-input-label for="email" :value="__('Email')" class="block text-sm font-medium text-gray-700 mb-2" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <x-text-input id="email" 
                                         class="block w-full pl-10 pr-3 py-3 rounded-lg border-gray-300 bg-white/50 focus:border-amber-500 focus:ring-amber-500 transition-all duration-200" 
                                         type="email" 
                                         name="email" 
                                         :value="old('email')" 
                                         required 
                                         autofocus 
                                         placeholder="name@company.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-500" />
                    </div>

                    <div class="animate-fade-in-delay-400">
                        <x-primary-button class="w-full py-3 px-4 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-medium rounded-lg shadow-lg transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2">
                            {{ __('Email Reset Link') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <div class="mt-6 text-center animate-fade-in-delay-500">
                <p class="text-gray-600">
                    Remember your password? 
                    <a href="{{ route('login') }}" class="font-medium text-amber-600 hover:text-amber-700 transition-colors duration-200">
                        Sign in
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
        
        .animate-fade-in-delay-500 {
            animation: fade-in 0.6s ease-out 0.5s forwards;
        }
    </style>
</x-guest-layout>