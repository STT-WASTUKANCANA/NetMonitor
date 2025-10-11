<section>
    <header class="mb-6">
        {{-- <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p> --}}
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="space-y-2">
            <x-input-label for="update_password_current_password" :value="__('Current Password')" class="block text-sm font-medium text-gray-700" />
            <x-text-input 
                id="update_password_current_password" 
                name="current_password" 
                type="password" 
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 transition duration-200" 
                autocomplete="current-password" 
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <x-input-label for="update_password_password" :value="__('New Password')" class="block text-sm font-medium text-gray-700" />
            <x-text-input 
                id="update_password_password" 
                name="password" 
                type="password" 
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 transition duration-200" 
                autocomplete="new-password" 
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div class="space-y-2">
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="block text-sm font-medium text-gray-700" />
            <x-text-input 
                id="update_password_password_confirmation" 
                name="password_confirmation" 
                type="password" 
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 transition duration-200" 
                autocomplete="new-password" 
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-4">
            <x-primary-button class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                {{ __('Save') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p 
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition:enter="transition ease-in duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-out duration-200"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>