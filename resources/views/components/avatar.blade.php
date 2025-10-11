@props([
    'user' => null,
    'size' => 'md', // sm, md, lg, xl
    'rounded' => 'full', // full, none, sm, md, lg
    'showName' => false,
    'namePosition' => 'right', // right, bottom
])

@php
    $sizeClasses = match($size) {
        'sm' => 'w-8 h-8',
        'lg' => 'w-12 h-12',
        'xl' => 'w-16 h-16',
        'md' => 'w-10 h-10',
        default => 'w-10 h-10'
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
    $photoUrl = $user ? $user->getProfilePhotoUrlAttribute() : asset('images/default-profile.png');
    $userName = $user ? $user->name : 'User';
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center']) }}>
    @if($showName && $namePosition === 'top')
        <span class="mr-2 text-sm font-medium text-gray-700">{{ $userName }}</span>
    @endif
    
    @if($showName && $namePosition === 'left')
        <span class="mr-2 text-sm font-medium text-gray-700">{{ $userName }}</span>
    @endif
    
    <img 
        src="{{ $photoUrl }}" 
        alt="{{ $userName }}'s profile photo"
        class="{{ $sizeClasses }} {{ $roundedClasses }} object-cover border border-gray-200 shadow-sm"
        onerror="this.onerror=null; this.src='{{ asset('images/default-profile.png') }}';"
    />
    
    @if($showName && $namePosition === 'right')
        <span class="ml-2 text-sm font-medium text-gray-700">{{ $userName }}</span>
    @endif
    
    @if($showName && $namePosition === 'bottom')
        <span class="mt-1 text-sm font-medium text-gray-700">{{ $userName }}</span>
    @endif
</div>