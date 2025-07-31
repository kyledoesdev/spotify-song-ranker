<div class="flex justify-between shadow-md bg-white rounded-lg py-4">
    <div>
        <a href="{{ auth()->check() ? route('home') : route('welcome') }}">
            <h5 class="text-xs md:text-base lg:text-xl p-1 mx-3">songrank 🎵</h5>
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
					style="z-index: 2;" {{-- go above any other elements --}}
				>
				<button
                    x-ref="button"
                    x-on:click="toggle()"
                    :aria-expanded="open"
                    type="button"
                    class="flex items-center px-2 gap-2 rounded-md cursor-pointer"
                >
                    <img
                        class="hidden md:block md:w-8 md:h-8 md:rounded-xl md:border md:border-zinc-900" 
                        src="{{ auth()->user()->avatar }}" 
                        alt="User Actions"
                    />
                    <span class="text-sm sm:text-base md:text-lg">
                        {{ auth()->user()->name }} <i class="fa fa-solid fa-chevron-down"></i>
                    </span>
                </button>
				<div
					x-ref="panel"
					x-show="open"
					x-transition.origin.top.left
					x-on:click.outside="close($refs.button)"
					style="display: none;"
					class="absolute left-0 mt-2 w-40 rounded-md bg-white shadow-md"
				>
					@if (get_route() != 'home')
						<a href="{{ route('home') }}" class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
							Home
						</a>
					@endif
					<a href="{{ route('profile', ['id' => auth()->user()->spotify_id ]) }}" class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
						Profile
					</a>
					@if (get_route() != 'explore.index')
						<a href="{{ route('explore.index') }}" class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
							Explore
						</a>
					@endif
					@if (get_route() != 'settings')
						<a href="{{ route('settings') }}" class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
							Settings
						</a>
					@endif
					<a  href="{{ route('logout') }}" class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
						Logout
					</a>
				</div>
			</div>
        @else
			<div class="mt-1">
				<a href="{{ route('welcome') }}" class="btn-primary p-2 m-2">
					<i class="fa fa-house"></i>
				</a>
			</div>
        @endauth
    </div>
</div>

{{-- cdn-ing alpine in here for the nav drop down - filament doesn't like when I include it in app.js .... --}}
@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush