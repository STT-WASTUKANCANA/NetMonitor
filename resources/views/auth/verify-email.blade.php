<x-guest-layout>
    <div class=" flex items-center justify-center bg-gradient-to-br from-emerald-50 via-white to-cyan-50 p-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-10 animate-fade-in">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-r from-emerald-500 to-cyan-600 mb-4 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-cyan-600">
                    NetMonitor
                </h1>
                <h2 class="mt-4 text-2xl font-semibold text-gray-800">Verify your email</h2>
                <p class="mt-2 text-gray-600">
                    Check your inbox for the verification link
                </p>
            </div>

            <div class="relative bg-white/70 backdrop-blur-lg rounded-2xl shadow-xl border border-white/30 p-8 transition-all duration-300 hover:shadow-2xl animate-slide-up">
                <div class="mb-6 text-center text-gray-600 animate-fade-in-delay-100">
                    {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 p-4 bg-emerald-50 rounded-lg text-emerald-800 border border-emerald-200 animate-fade-in-delay-200">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                        </div>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-4 animate-fade-in-delay-300">
                    <form method="POST" action="{{ route('verification.send') }}" class="flex-1">
                        @csrf
                        <x-primary-button class="w-full py-3 px-4 bg-gradient-to-r from-emerald-600 to-cyan-600 hover:from-emerald-700 hover:to-cyan-700 text-white font-medium rounded-lg shadow-lg transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                            {{ __('Resend Verification Email') }}
                        </x-primary-button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-medium rounded-lg shadow transition-all duration-300 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
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
    </style>
</x-guest-layout>