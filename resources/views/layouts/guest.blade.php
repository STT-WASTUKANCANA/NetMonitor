<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="theme()" 
      x-bind:class="{ 'dark': dark }"
      x-init="init()">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-theme text-theme theme-transition min-h-screen flex flex-col sm:justify-center items-center">
    <!-- Logo -->
    <div class="pt-6 sm:pt-0">
        <a href="/">
            <x-application-logo class="w-20 h-20 fill-current text-[color:var(--color-primary)]" />
        </a>
    </div>

    <!-- Card -->
    <div class="w-full sm:max-w-md mt-6 px-6 py-6 surface shadow-lg overflow-hidden sm:rounded-2xl theme-transition">
        {{ $slot }}
    </div>

    <!-- Toggle Dark/Light Mode -->
    <div class="fixed bottom-4 right-4">
        <button x-on:click="toggle()" class="p-2 rounded-full border border-[color:var(--muted)] hover:scale-105 transition">
            <template x-if="!dark">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[color:var(--text)]" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 3a1 1 0 011 1v1a1 1 0 01-2 0V4a1 1 0 011-1zM4 9a1 1 0 000 2H3a1 1 0 000-2h1zm13 0a1 1 0 000 2h-1a1 1 0 000-2h1zM10 16a1 1 0 011 1v1a1 1 0 01-2 0v-1a1 1 0 011-1zM5.05 5.05a1 1 0 011.414 0L7.05 5.636a1 1 0 11-1.414 1.414L5.05 6.464a1 1 0 010-1.414zM14.95 5.05a1 1 0 010 1.414L14.364 7.05a1 1 0 11-1.414-1.414l.586-.586a1 1 0 011.414 0zM5.05 14.95a1 1 0 010-1.414L5.636 13.05a1 1 0 111.414 1.414L6.464 14.95a1 1 0 01-1.414 0zM14.95 14.95a1 1 0 01-1.414 0L13.05 14.364a1 1 0 111.414-1.414l.586.586a1 1 0 010 1.414z"/>
                </svg>
            </template>
            <template x-if="dark">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[color:var(--text)]" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                </svg>
            </template>
        </button>
    </div>

    <!-- AlpineJS -->
    <script>
        function theme() {
            return {
                dark: false,
                init() {
                    const saved = localStorage.getItem('theme');
                    if (saved) {
                        this.dark = saved === 'dark';
                        return;
                    }
                    const prefers = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    this.dark = prefers;
                    localStorage.setItem('theme', this.dark ? 'dark' : 'light');
                },
                toggle() {
                    this.dark = !this.dark;
                    localStorage.setItem('theme', this.dark ? 'dark' : 'light');
                }
            }
        }
    </script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>