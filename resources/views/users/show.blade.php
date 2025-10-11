@extends('layouts.app')

@section('title', 'View User')

@section('content')
<div class="max-w-3xl mx-auto sm:px-6 lg:px-8 py-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">User Details: {{ $user->name }}</h2>
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
                <div class="bg-gray-50 p-6 rounded-lg">
                    <div class="flex items-center mb-4">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold text-xl mr-4">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">{{ $user->name }}</h3>
                            <p class="text-gray-600">{{ $user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mt-6">
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
@endsection