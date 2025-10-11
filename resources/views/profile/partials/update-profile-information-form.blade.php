<section>
    <header class="mb-6">
        {{-- <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p> --}}
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Name Field -->
        <div class="space-y-2">
            <x-input-label for="name" :value="__('Name')" class="block text-sm font-medium text-gray-700" />
            <x-text-input 
                id="name" 
                name="name" 
                type="text" 
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 transition duration-200" 
                :value="old('name', $user->name)" 
                required 
                autofocus 
                autocomplete="name" 
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email Field -->
        <div class="space-y-2">
            <x-input-label for="email" :value="__('Email')" class="block text-sm font-medium text-gray-700" />
            <x-text-input 
                id="email" 
                name="email" 
                type="email" 
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 transition duration-200" 
                :value="old('email', $user->email)" 
                required 
                autocomplete="email" 
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4">
                    <p class="text-sm text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 ml-2">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Avatar Upload Field -->
        <div class="space-y-2">
            <x-input-label for="avatar" :value="__('Profile Picture')" class="block text-sm font-medium text-gray-700" />
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <img 
                        id="avatar-preview" 
                        src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=7F9CF5&background=EBF4FF' }}" 
                        alt="Avatar Preview" 
                        class="w-16 h-16 rounded-full object-cover border-2 border-gray-200 shadow-sm"
                    >
                </div>
                <div class="flex-1">
                    <x-text-input 
                        id="avatar" 
                        name="avatar" 
                        type="file" 
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 transition duration-200" 
                        accept="image/*"
                        onchange="previewAvatar(event)"
                    />
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div class="flex items-center gap-4 pt-4">
            <x-primary-button class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                {{ __('Save') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
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

    <script>
        function previewAvatar(event) {
            const preview = document.getElementById('avatar-preview');
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                
                reader.readAsDataURL(file);
            }
        }
    </script>
</section>