<button 
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => '
            inline-flex items-center justify-center
            px-4 py-2 rounded-md font-semibold text-xs uppercase tracking-widest
            bg-blue-600 hover:bg-blue-700 active:bg-blue-800
            text-white border border-transparent
            focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2
            transition ease-in-out duration-150
        '
    ]) }}>
    {{ $slot }}
</button>