@props([
    'type' => 'submit',
])

<button {{ $attributes->merge(['type' => $type, 'class' => 'flex justify-center alin-items ginline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors']) }}>
    {{ $slot }}
</button>