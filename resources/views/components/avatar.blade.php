@props([
    'user' => null,
    'size' => 'md', // sm, md, lg, xl
    'rounded' => 'full', // full, none, sm, md, lg
    'showName' => false,
    'namePosition' => 'right', // right, bottom
    'interactive' => false, // Whether to add hover effects
])

@php
    $sizeClasses = match($size) {
        'sm' => 'w-8 h-8 text-xs',
        'lg' => 'w-12 h-12 text-sm',
        'xl' => 'w-16 h-16 text-base',
        'md' => 'w-10 h-10 text-sm',
        default => 'w-10 h-10 text-sm'
    };
    
    $roundedClasses = match($rounded) {
        'none' => 'rounded-none',
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'full' => 'rounded-full',
        default => 'rounded-full'
    };
    
    $user = $user ?? auth()->user();
    $photoUrl = $user && $user->profile_photo_path ? $user->getProfilePhotoUrlAttribute() : null;
    $userName = $user ? $user->name : 'User';
    
    // Generate initials for default avatar
    $initials = $user ? strtoupper(substr($user->name, 0, 1)) : 'U';
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center']) }}>
    @if($showName && $namePosition === 'top')
        <span class="mr-2 text-sm font-medium text-gray-700">{{ $userName }}</span>
    @endif
    
    @if($showName && $namePosition === 'left')
        <span class="mr-2 text-sm font-medium text-gray-700">{{ $userName }}</span>
    @endif
    
    @if($photoUrl)
        <div class="relative">
            <img 
                src="{{ $photoUrl }}" 
                alt="{{ $userName }}'s profile photo"
                class="{{ $sizeClasses }} {{ $roundedClasses }} object-cover border-2 {{ $interactive ? 'hover:ring-2 hover:ring-blue-500 hover:ring-offset-2 transition-all duration-200 cursor-pointer' : '' }} border-gray-200 dark:border-gray-700 shadow-sm"
                onerror="this.onerror=null; this.src='{{ asset('images/default-profile.png') }}';"
            />
            @if($interactive)
                <div class="absolute inset-0 {{ $roundedClasses }} bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-200"></div>
            @endif
        </div>
    @else
        <!-- Default avatar with initials -->
        <div class="{{ $sizeClasses }} {{ $roundedClasses }} flex items-center justify-center 
                    bg-gradient-to-br from-blue-400 to-indigo-600 text-white font-semibold 
                    border-2 border-gray-200 shadow-sm
                    {{ $interactive ? 'hover:ring-2 hover:ring-blue-500 hover:ring-offset-2 transition-all duration-200 cursor-pointer' : '' }}">
            <span class="select-none">{{ $initials }}</span>
        </div>
    @endif
    
    @if($showName && $namePosition === 'right')
        <span class="ml-2 text-sm font-medium text-gray-700 truncate max-w-[120px]">{{ $userName }}</span>
    @endif
    
    @if($showName && $namePosition === 'bottom')
        <div class="mt-2 text-center">
            <span class="text-sm font-medium text-gray-700 block">{{ $userName }}</span>
        </div>
    @endif
</div>