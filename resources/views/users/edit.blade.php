<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="mb-2">
                <!-- Judul Halaman -->
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Edit User') }}
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
                                <span class="text-sm font-medium text-black">Edit User: {{ $user->name }}</span>
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
                            <h2 class="text-xl font-semibold text-gray-800">Edit User: {{ $user->name }}</h2>
                            <p class="text-sm text-gray-600 mt-1">Update the user's information and permissions</p>
                        </div>
                        
                        <a href="{{ route('users.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Users
                        </a>
                    </div>

                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6 mb-6">
                            <div>
                                <x-input-label for="name" :value="__('Name')" class="block text-sm font-medium text-gray-700" />
                                <x-text-input 
                                    id="name" 
                                    type="text" 
                                    name="name" 
                                    value="{{ old('name', $user->name) }}" 
                                    required 
                                    autofocus 
                                    autocomplete="name"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200" 
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" class="block text-sm font-medium text-gray-700" />
                                <x-text-input 
                                    id="email" 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email', $user->email) }}" 
                                    required 
                                    autocomplete="username"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200" 
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('New Password (Optional)')" class="block text-sm font-medium text-gray-700" />
                                <x-text-input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    autocomplete="new-password"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200" 
                                />
                                <p class="mt-1 text-sm text-gray-500">Leave blank to keep the current password</p>
                                <x-input-error class="mt-2" :messages="$errors->get('password')" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm New Password')" class="block text-sm font-medium text-gray-700" />
                                <x-text-input 
                                    id="password_confirmation" 
                                    type="password" 
                                    name="password_confirmation" 
                                    autocomplete="new-password"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200" 
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                            </div>

                            <div>
                                <x-input-label for="role" :value="__('Role')" class="block text-sm font-medium text-gray-700" />
                                <select 
                                    id="role" 
                                    name="role" 
                                    required 
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200">
                                    <option value="">Select Role</option>
                                    <option value="Admin" {{ old('role', $user->getRoleNameAttribute()) === 'Admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="Petugas" {{ old('role', $user->getRoleNameAttribute()) === 'Petugas' ? 'selected' : '' }}>Petugas</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('role')" />
                            </div>
                        </div>

                        <!-- Profile Photo Section -->
                        <div class="grid grid-cols-1 gap-6 mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">Profile Photo</h3>
                            
                            <div class="space-y-4">
                                <div class="flex items-center space-x-4">
                                    <div class="relative group cursor-pointer">
                                        <!-- Avatar Preview -->
                                        <img id="user-avatar-preview"
                                            src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=ffffff&background=3b82f6' }}"
                                            alt="User Avatar Preview"
                                            class="w-20 h-20 rounded-full object-cover border-4 border-transparent shadow-lg transition-all duration-300 group-hover:border-blue-500"
                                            title="Current profile photo">
                                    </div>

                                    <!-- Upload Button -->
                                    <div class="flex-1">
                                        <label for="user-avatar" class="block text-sm font-medium text-gray-700 mb-2">Upload New Photo</label>
                                        <input id="user-avatar" name="user_avatar" type="file" accept="image/*"
                                            class="block w-full text-sm text-gray-500
                                                   file:mr-4 file:py-2 file:px-4
                                                   file:rounded-lg file:border-0
                                                   file:text-sm file:font-medium
                                                   file:bg-blue-50 file:text-blue-700
                                                   hover:file:bg-blue-100" 
                                            onchange="previewUserAvatar(event)">
                                        <p class="mt-1 text-sm text-gray-500">
                                            JPG, PNG, GIF (Max 2MB)
                                        </p>
                                    </div>
                                </div>

                                <!-- Remove Photo Button -->
                                @if($user->profile_photo_path)
                                    <div class="flex items-center">
                                        <button type="button" 
                                            onclick="removeUserPhoto({{ $user->id }})"
                                            class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Remove Photo
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('users.index') }}" 
                               class="px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 active:bg-gray-100 focus:outline-none focus:border-gray-300 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            
                            <x-primary-button class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Update User') }}
                            </x-primary-button>
                        </div>

                        <script>
                            function previewUserAvatar(event) {
                                const preview = document.getElementById('user-avatar-preview');
                                const file = event.target.files[0];

                                if (file) {
                                    const reader = new FileReader();

                                    reader.onload = function (e) {
                                        preview.src = e.target.result;
                                    }

                                    reader.readAsDataURL(file);
                                }
                            }
                            
                            async function removeUserPhoto(userId) {
                                if (!confirm('Are you sure you want to remove this user\'s profile photo?')) {
                                    return;
                                }
                                
                                try {
                                    const result = await window.ProfilePhotoManager.removePhoto(userId);
                                    if (result.success) {
                                        // Update the preview to the default avatar
                                        const defaultAvatar = 'https://ui-avatars.com/api/?name=' + encodeURIComponent('{{ $user->name }}') + '&color=ffffff&background=3b82f6';
                                        document.getElementById('user-avatar-preview').src = result.profile_photo_url;
                                        window.ProfilePhotoManager.showToast(result.message, 'success');
                                    } else {
                                        window.ProfilePhotoManager.showToast(result.message, 'error');
                                    }
                                } catch (error) {
                                    console.error('Error removing user photo:', error);
                                    window.ProfilePhotoManager.showToast('Error removing user profile photo', 'error');
                                }
                            }
                            
                            // Handle form submission to also update profile photo if changed
                            document.addEventListener('DOMContentLoaded', function() {
                                const userEditForm = document.querySelector('form[action="{{ route('users.update', $user) }}"]');
                                const avatarInput = document.getElementById('user-avatar');
                                
                                if (userEditForm && avatarInput) {
                                    // Store original submit handler
                                    const originalSubmit = userEditForm.onsubmit || function(e) {};
                                    
                                    // Override submit handler
                                    userEditForm.onsubmit = async function(e) {
                                        // Check if avatar field has been changed
                                        if (avatarInput.files.length > 0) {
                                            e.preventDefault(); // Prevent the main form from submitting immediately
                                            
                                            // Upload the photo using the API
                                            const result = await window.ProfilePhotoManager.uploadPhoto(avatarInput.files[0], {{ $user->id }});
                                            
                                            if (result.success) {
                                                // Update the avatar preview
                                                document.getElementById('user-avatar-preview').src = result.profile_photo_url;
                                                
                                                // Now submit the main form
                                                avatarInput.remove(); // Remove the avatar input to avoid conflicts
                                                this.submit();
                                            } else {
                                                window.ProfilePhotoManager.showToast(result.message, 'error');
                                                return false; // Don't submit if photo upload failed
                                            }
                                        }
                                    };
                                }
                            });
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>