@extends('layouts.app')

@section('title', 'Edit User: ' . $user->name)

@section('header')
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Edit User: {{ $user->name }}</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Update user information
                    </p>
                </div>
            </div>
        </div>
    </header>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-2xl transition-all duration-300 hover:shadow-md">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">Edit User: {{ $user->name }}</h2>
                            <p class="text-sm text-gray-600 mt-1">Update user information</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input 
                                id="name" 
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                type="text" 
                                name="name" 
                                value="{{ old('name', $user->name) }}" 
                                required 
                                autofocus 
                                placeholder="Enter user's full name"
                            />
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input 
                                id="email" 
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                type="email" 
                                name="email" 
                                value="{{ old('email', $user->email) }}" 
                                required 
                                placeholder="user@example.com"
                            />
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="mb-6">
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                            <select 
                                id="role" 
                                name="role" 
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                required
                            >
                                <option value="">Select Role</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="petugas" {{ old('role', $user->role) == 'petugas' ? 'selected' : '' }}>Petugas</option>
                            </select>
                            @error('role')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password (Optional) -->
                        <div class="mb-6">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password (Optional)</label>
                            <input 
                                id="password" 
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                type="password" 
                                name="password" 
                                autocomplete="new-password" 
                            />
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-500 mt-1">Leave blank to keep current password</p>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-8">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <input 
                                id="password_confirmation" 
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                type="password" 
                                name="password_confirmation" 
                                autocomplete="new-password" 
                            />
                            @error('password_confirmation')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Profile Photo -->
                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>
                            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-4">
                                <x-avatar :user="$user" size="xl" :interactive="true" :showName="false" class="cursor-pointer" />
                                <div class="flex-1 w-full">
                                    <input 
                                        id="profile_photo" 
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                        type="file" 
                                        name="profile_photo" 
                                        accept="image/*"
                                    />
                                    @error('profile_photo')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="text-sm text-gray-500 mt-1">JPG, PNG, or GIF (max 2MB)</p>
                                    
                                    @if($user->profile_photo_path)
                                    <div class="mt-3">
                                        <form id="remove-photo-form" 
                                              action="{{ route('users.photo.remove', $user) }}" 
                                              method="POST" 
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                               class="text-red-600 hover:text-red-800 text-sm flex items-center"
                                               onclick="return confirm('Are you sure you want to remove the profile photo?')">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Remove Current Photo
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('users.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Cancel
                            </a>
                            
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection