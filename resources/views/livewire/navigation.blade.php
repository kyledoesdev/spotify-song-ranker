<div>
    <div class="flex justify-between shadow-md bg-white rounded-lg py-4">
        <div>
            <a href="{{ auth()->check() ? route('dashboard') : route('welcome') }}" class="h-10 flex items-center me-4 mx-2">
                <div class="flex items-center justify-center h-8 rounded-sm overflow-hidden shrink-0">
                    <img src="/logo.png" alt="Song Rank Logo" class="h-8 w-8" />
                </div>

                <div class="text-sm font-medium truncate text-zinc-800 mx-2">
                    {{ config('app.name') }}
                </div>
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
                        class="flex items-center px-2 gap-2 rounded-md cursor-pointer"
                    >
                        <img
                            class="w-8 h-8 rounded-xl border border-zinc-900" 
                            src="{{ auth()->user()->avatar }}" 
                            alt="User Actions"
                        />
                        <span class="text-sm sm:text-base md:text-lg hidden md:block">
                            {{ auth()->user()->name }} <i class="fa fa-solid fa-chevron-down"></i>
                        </span>
                        <span class="md:hidden">
                            <i class="fa fa-solid fa-chevron-down"></i>
                        </span>
                    </button>
                    <div
                        x-ref="panel"
                        x-show="open"
                        x-transition.origin.top.right
                        x-on:click.outside="close($refs.button)"
                        style="display: none;"
                        class="absolute right-0 mt-2 w-40 rounded-md bg-white shadow-md"
                    >
                        @if (get_route() != 'dashboard')
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
                                <i class="fa fa-solid fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                        @endif
                        @if (get_route() != 'profile')
                            <a href="{{ route('profile', ['id' => auth()->user()->spotify_id ]) }}" class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
                                <i class="fa fa-solid fa-user"></i>
                                Profile
                            </a>
                        @endif
                        @if (get_route() != 'explore')
                            <a href="{{ route('explore') }}" class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
                                <i class="fa fa-solid fa-compass"></i>
                                Explore
                            </a>
                        @endif
                        @if (get_route() != 'settings')
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