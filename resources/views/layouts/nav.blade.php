<div class="flex justify-between border border-zinc-800  bg-white rounded-lg p-4">
    <div>
        @if (isset($title))
            <h5 class="md:text-2xl mt-2">{{ $title }}</h5>
        @else
            @auth
				<span style="text-xs">Welcome back 👋</span>
				<h5 class="text-xl md:text-2xl">{{ auth()->user()->name }}</h5>
			@else
				<span style="text-xs">Welcome to Song - Rank! 👋</span>
			@endauth
        @endif
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
					class="flex items-center gap-2 px-5 py-2.5 rounded-md"
				>
					<img
						height="32"
						width="32"
						class="rounded-lg border border-2 border-zinc-700 mr-1" 
						type="button"
						aria-expanded="false" 
						src="{{ auth()->user()->avatar }}" 
						alt="User Actions"
					/>
					{{ auth()->user()->name }}
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
					<a href="{{ route('profile.show', ['id' => auth()->user()->spotify_id ]) }}" class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
						Profile
					</a>
					@if (get_route() != 'explore.index')
						<a href="{{ route('explore.index') }}" class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
							Explore
						</a>
					@endif
					@if (get_route() != 'settings.index')
						<a href="{{ route('settings.index') }}" class="flex items-center gap-2 w-full first-of-type:rounded-t-md last-of-type:rounded-b-md px-4 py-2.5 text-left text-sm hover:bg-gray-50 disabled:text-gray-500">
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