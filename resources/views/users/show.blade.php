<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="mb-2">
                <!-- Judul Halaman -->
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('View User') }}
                    </h2>
                </div>

                <!-- Breadcrumb -->
                <nav class="flex mt-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="{{ route('dashboard') }}"
                                class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-600 transition-colors">
                                <svg class="w-4 h-4 mr-2 text-gray-400 hover:text-blue-500 transition-colors"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                                    </path>
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-black mx-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('users.index') }}"
                                    class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-600 transition-colors">
                                    User Management
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-black mx-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-medium text-black">View User: {{ $user->name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl transition-all duration-300 hover:shadow-md">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">User Details: {{ $user->name }}</h2>
                            <p class="text-sm text-gray-600 mt-1">View and manage user account information</p>
                        </div>
                        
                        <div class="flex space-x-2">
                            @can('edit users')
                            <a href="{{ route('users.edit', $user) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </a>
                            @endcan
                            
                            <a href="{{ route('users.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Back to Users
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200">
                            <div class="flex items-center mb-4">
                                <x-avatar :user="$user" size="xl" :showName="false" class="mr-4" />
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-800">{{ $user->name }}</h3>
                                    <p class="text-gray-600">{{ $user->email }}</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Role</h4>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                             @if($user->hasRole('Admin')) bg-red-100 text-red-800 @else bg-blue-100 text-blue-800 @endif">
                                            {{ $user->getRoleNameAttribute() }}
                                        </span>
                                    </p>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Account Created</h4>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('F d, Y H:i') }}</p>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Last Updated</h4>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('F d, Y H:i') }}</p>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Status</h4>
                                    <p class="mt-1 text-sm text-gray-900">
                                        @if($user->email_verified_at)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Verified
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Not Verified
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>