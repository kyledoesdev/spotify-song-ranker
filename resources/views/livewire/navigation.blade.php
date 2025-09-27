<div>
    <!-- Mobile sidebar overlay -->
    <div 
        x-data="{ sidebarOpen: false }"
        x-on:keydown.escape.prevent.stop="sidebarOpen = false"
    >
        <!-- Mobile sidebar backdrop -->
        <div 
            x-show="sidebarOpen" 
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 lg:hidden"
            x-on:click="sidebarOpen = false"
            style="display: none;"
        ></div>

        <!-- Mobile sidebar -->
        <div 
            x-show="sidebarOpen"
            x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 flex flex-col w-64 bg-white shadow-lg z-50 lg:hidden"
            style="display: none;"
        >
            <div class="flex items-center justify-between h-16 px-4 border-b">
                <a href="{{ auth()->check() ? route('dashboard') : route('welcome') }}" class="flex items-center">
                    <img src="/logo.png" alt="Song Rank Logo" class="h-8 w-8" />
                    <span class="text-sm font-medium text-zinc-800 ml-2">{{ config('app.name') }}</span>
                </a>
                <button x-on:click="sidebarOpen = false" class="text-gray-500 hover:text-gray-700">
                    <i class="fa fa-times fa-lg"></i>
                </button>
            </div>

            <nav class="flex-1 px-4 py-4 space-y-2">
                @auth
                    @if (Route::currentRouteName() !== 'explore')
                        <a href="{{ route('explore') }}" class="flex items-center px-3 py-2 text-sm font-medium text-zinc-800 rounded-md hover:bg-gray-100">
                            <i class="fa fa-compass mr-3"></i>
                            Explore
                        </a>
                    @endif
                    @if (Route::currentRouteName() != 'profile')
                        <a href="{{ route('profile', ['id' => auth()->user()->spotify_id ]) }}" class="flex items-center px-3 py-2 text-sm font-medium text-zinc-800 rounded-md hover:bg-gray-100">
                            <i class="fa fa-user mr-3"></i>
                            Profile
                        </a>
                    @endif
                    @if (Route::currentRouteName() != 'settings')
                        <a href="{{ route('settings') }}" class="flex items-center px-3 py-2 text-sm font-medium text-zinc-800 rounded-md hover:bg-gray-100">
                            <i class="fa fa-cog mr-3"></i>
                            Settings
                        </a>
                    @endif
                @endauth
            </nav>

            @auth
                <div class="border-t px-4 py-4">
                    <div class="flex items-center mb-4">
                        <img src="{{ auth()->user()->avatar }}" alt="User Avatar" class="h-10 w-10 rounded-full" />
                        <div class="ml-3">
                            <p class="text-sm font-medium text-zinc-800">{{ auth()->user()->name }}</p>
                        </div>
                    </div>
                    <a href="{{ route('logout') }}" class="flex items-center px-3 py-2 text-sm font-medium text-red-600 rounded-md hover:bg-red-50">
                        <i class="fa fa-sign-out-alt mr-3"></i>
                        Logout
                    </a>
                </div>
            @endauth
        </div>

        <!-- Desktop top navigation -->
        <div class="hidden lg:block">
            <div class="flex justify-between shadow-md bg-white rounded-lg py-4">
                <div class="flex">
                    <a href="{{ auth()->check() ? route('dashboard') : route('welcome') }}" class="h-10 flex items-center me-4 ml-2">
                        <div class="flex items-center justify-center h-8 rounded-sm overflow-hidden shrink-0">
                            <img src="/logo.png" alt="Song Rank Logo" class="h-8 w-8" />
                        </div>
                        <div class="text-sm font-medium truncate text-zinc-800 mx-2 cursor-pointer">
                            {{ config('app.name') }}
                        </div>
                    </a>

                    <a href="{{ route('explore') }}" class="h-10 flex items-center me-4 mr-2 bg-gray-100 rounded-lg cursor-pointer p-2">
                        <i class="fa fa-compass mr-1 fa-lg"></i>
                        <span class="text-sm font-medium text-zinc-800 mx-1">Explore</span>
                    </a>
                </div>
                <div>
                    @auth
                        <div
                            x-data="{
                                open: false,
                                toggle() {
                                    if (this.open) {
                                        return this.close()
                                    }
                                    this.$refs.button.focus()
                                    this.open = true
                                },
                                
                                close(focusAfter) {
                                    if (! this.open) return
                                    this.open = false
                                    focusAfter && focusAfter.focus()
                                }
                            }"
                            x-on:keydown.escape.prevent.stop="close($refs.button)"
                            x-on:focusin.window="! $refs.panel.contains($event.target) && close()"
                            x-id="['dropdown-button']"
                            class="relative"
                            style="z-index: 2;"
                        >
                            <button
                                x-ref="button"
                                x-on:click="toggle()"
                                :aria-expanded="open"
                                type="button"
                                class="h-10 flex items-center me-4 mx-2"
                            >
                                <div class="flex items-center justify-center h-8 rounded-sm overflow-hidden shrink-0 cursor-pointer">
                                    <img src="{{ auth()->user()->avatar }}" alt="User Actions" class="h-8 w-8" />
                                </div>
                                <div class="text-sm font-medium truncate text-zinc-800 mx-2 hidden md:block cursor-pointer">
                                    {{ auth()->user()->name }}
                                </div>
                                <i class="fa fa-solid fa-chevron-down hidden md:block ml-2 md:ml-0 cursor-pointer"></i>
                            </button>
                            <div
                                x-ref="panel"
                                x-show="open"
                                x-transition.origin.top.right
                                x-on:click.outside="close($refs.button)"
                                style="display: none;"
                                class="absolute right-0 mt-2 w-40 rounded-md bg-white shadow-md"
                            >
                                @if (Route::currentRouteName() != 'profile')
                                    <a href="{{ route('profile', ['id' => auth()->user()->spotify_id ]) }}" class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
                                        <i class="fa fa-solid fa-user"></i>
                                        Profile
                                    </a>
                                @endif
                                @if (Route::currentRouteName() != 'settings')
                                    <a href="{{ route('settings') }}" class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
                                        <i class="fa fa-solid fa-cog"></i>
                                        Settings
                                    </a>
                                @endif
                                <a href="{{ route('logout') }}" class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
                                    <i class="fa fa-solid fa-sign-out-alt"></i>
                                    Logout
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="mt-1 mx-3">
                            <a href="{{ route('welcome') }}" class="btn-primary p-2 m-2">
                                <i class="fa fa-house"></i>
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Mobile top bar with hamburger -->
        <div class="lg:hidden flex justify-between items-center shadow-md bg-white p-4">
            <button x-on:click="sidebarOpen = true" class="text-zinc-800">
                <i class="fa fa-bars fa-lg"></i>
            </button>
            
            <a href="{{ auth()->check() ? route('dashboard') : route('welcome') }}" class="flex items-center">
                <img src="/logo.png" alt="Song Rank Logo" class="h-8 w-8" />
                <span class="text-sm font-medium text-zinc-800 ml-2">{{ config('app.name') }}</span>
            </a>

            @auth
                <a href="{{ route('profile', ['id' => auth()->user()->spotify_id ]) }}">
                    <img src="{{ auth()->user()->avatar }}" alt="User Avatar" class="h-8 w-8 rounded-full" />
                </a>
            @else
                <a href="{{ route('welcome') }}" class="btn-primary p-2">
                    <i class="fa fa-house"></i>
                </a>
            @endauth
        </div>
    </div>
</div>