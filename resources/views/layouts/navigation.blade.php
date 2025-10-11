<nav x-data="{ open: false }"
    x-init="$watch('open', value => { if (value) { document.body.style.overflow = 'hidden'; } else { document.body.style.overflow = ''; } })"
    class="bg-white border-b border-gray-950">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                        <span class="ml-2 text-xl font-bold text-gray-800 hidden md:block">Monitoring
                            Konektivitas</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex items-center">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <svg class="w-5 h-5 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-3z">
                            </path>
                        </svg>
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @can('view devices')
                        <x-nav-link :href="route('devices.index')" :active="request()->routeIs('devices.*')">
                            <svg class="h-5 w-5 inline mr-1" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path
                                    d="M128 96C92.7 96 64 124.7 64 160L64 416C64 451.3 92.7 480 128 480L272 480L256 528L184 528C170.7 528 160 538.7 160 552C160 565.3 170.7 576 184 576L456 576C469.3 576 480 565.3 480 552C480 538.7 469.3 528 456 528L384 528L368 480L512 480C547.3 480 576 451.3 576 416L576 160C576 124.7 547.3 96 512 96L128 96zM160 160L480 160C497.7 160 512 174.3 512 192L512 352C512 369.7 497.7 384 480 384L160 384C142.3 384 128 369.7 128 352L128 192C128 174.3 142.3 160 160 160z" />
                            </svg>
                            {{ __('Devices') }}
                        </x-nav-link>
                    @endcan

                    @can('view alerts')
                        <x-nav-link :href="route('alerts.index')" :active="request()->routeIs('alerts.*')">
                            <svg class="w-5 h-5 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                            {{ __('Alerts') }}
                            @if(auth()->user()->can('view alerts') && \App\Models\Alert::where('status', 'active')->count() > 0)
                                <span
                                    class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ \App\Models\Alert::where('status', 'active')->count() }}
                                </span>
                            @endif
                        </x-nav-link>
                    @endcan

                    @can('view reports')
                        <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                            <svg class="w-5 h-5 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            {{ __('Reports') }}
                        </x-nav-link>
                    @endcan

                    @can('view users')
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                            <svg class="w-5 h-5 inline mr-1" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 640 640"><!--!Font Awesome Free v7.1.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path
                                    d="M320 80C377.4 80 424 126.6 424 184C424 241.4 377.4 288 320 288C262.6 288 216 241.4 216 184C216 126.6 262.6 80 320 80zM96 152C135.8 152 168 184.2 168 224C168 263.8 135.8 296 96 296C56.2 296 24 263.8 24 224C24 184.2 56.2 152 96 152zM0 480C0 409.3 57.3 352 128 352C140.8 352 153.2 353.9 164.9 357.4C132 394.2 112 442.8 112 496L112 512C112 523.4 114.4 534.2 118.7 544L32 544C14.3 544 0 529.7 0 512L0 480zM521.3 544C525.6 534.2 528 523.4 528 512L528 496C528 442.8 508 394.2 475.1 357.4C486.8 353.9 499.2 352 512 352C582.7 352 640 409.3 640 480L640 512C640 529.7 625.7 544 608 544L521.3 544zM472 224C472 184.2 504.2 152 544 152C583.8 152 616 184.2 616 224C616 263.8 583.8 296 544 296C504.2 296 472 263.8 472 224zM160 496C160 407.6 231.6 336 320 336C408.4 336 480 407.6 480 496L480 512C480 529.7 465.7 544 448 544L192 544C174.3 544 160 529.7 160 512L160 496z" />
                            </svg>
                            {{ __('Users') }}
                        </x-nav-link>
                    @endcan
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Settings Dropdown - Desktop Only -->
                <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                    <div @click="open = ! open">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-lg
                           text-gray-700 
                           bg-white 
                           hover:bg-gray-100
                           transition-all duration-200 ease-in-out shadow-sm">

                            <div class="flex items-center">
                                <!-- Avatar -->
                                <x-avatar :user="Auth::user()" size="sm" :showName="false" class="mr-2" />

                                <!-- Nama -->
                                <div class="hidden md:block font-medium">{{ Auth::user()->name }}</div>
                            </div>

                            <div class="ml-2">
                                <svg class="fill-current h-4 w-4 opacity-70 group-hover:opacity-100 transition-all"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </div>

                    <!-- Settings Dropdown Menu -->
                    <div x-show="open"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-72 origin-top-right rounded-2xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                        style="display: none;">
                        
                        <div class="p-4">
                            <!-- User Info -->
                            <div class="text-center mb-4">
                                <x-avatar :user="Auth::user()" size="xl" class="mx-auto mb-2" />
                                <h3 class="text-lg font-semibold text-gray-800">{{ Auth::user()->name }}</h3>
                                <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                    {{ Auth::user()->getRoleNameAttribute() }}
                                </span>
                            </div>

                            <!-- Dropdown Links -->
                            <div class="space-y-2">
                                <a href="{{ route('profile.edit') }}" 
                                    class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-150">
                                    {{ __('Profile') }}
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-150">
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hamburger - Mobile/Tablet Only -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-7 w-7" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Fullscreen Overlay Menu - Mobile/Tablet Only -->
    <div x-show="open" 
         x-transition:enter="duration-300 ease-out" 
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="duration-200 ease-in"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[1000] bg-white flex items-center justify-center p-6 sm:p-8"
         style="display: none;"
         x-cloak>
        
        <!-- Close Button -->
        <button @click="open = false" 
                class="absolute top-0 right-0 p-4 text-black hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <!-- Menu Items -->
        <div class="w-full max-w-md">
            <div class="space-y-8">
                <a href="{{ route('dashboard') }}" 
                   @click="open = false"
                   class="block text-center text-2xl font-medium text-black hover:text-blue-600 transition duration-200 ease-in-out py-3">
                    {{ __('Dashboard') }}
                </a>
                
                @can('view devices')
                    <a href="{{ route('devices.index') }}" 
                       @click="open = false"
                       class="block text-center text-2xl font-medium text-black hover:text-blue-600 transition duration-200 ease-in-out py-3">
                        {{ __('Devices') }}
                    </a>
                @endcan
                
                @can('view alerts')
                    <a href="{{ route('alerts.index') }}" 
                       @click="open = false"
                       class="block text-center text-2xl font-medium text-black hover:text-blue-600 transition duration-200 ease-in-out py-3">
                        {{ __('Alerts') }}
                    </a>
                @endcan

                @can('view reports')
                    <a href="{{ route('reports.index') }}" 
                       @click="open = false"
                       class="block text-center text-2xl font-medium text-black hover:text-blue-600 transition duration-200 ease-in-out py-3">
                        {{ __('Reports') }}
                    </a>
                @endcan

                @can('view users')
                    <a href="{{ route('users.index') }}" 
                       @click="open = false"
                       class="block text-center text-2xl font-medium text-black hover:text-blue-600 transition duration-200 ease-in-out py-3">
                        {{ __('Users') }}
                    </a>
                @endcan
                
                <a href="{{ route('profile.edit') }}" 
                   @click="open = false"
                   class="block text-center text-2xl font-medium text-black hover:text-blue-600 transition duration-200 ease-in-out py-3">
                    {{ __('Profile') }}
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            @click="open = false"
                            class="block w-full text-center text-2xl font-medium text-black hover:text-blue-600 transition duration-200 ease-in-out py-3">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
