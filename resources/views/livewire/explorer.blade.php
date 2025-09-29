<div>
    <div class="min-h-screen">
        <!-- Toggle Buttons (visible on smaller screens) -->
        <button 
            class="lg:hidden fixed left-0 top-1/2 -translate-y-1/2 bg-white border p-2 rounded-r-md z-50 shadow-lg"
            wire:click="toggleSidebar"
        >
            <i class="fa-solid fa-angle-right"></i>
        </button>

        <button 
            class="lg:hidden fixed right-0 top-1/2 -translate-y-1/2 bg-white border p-2 rounded-l-md z-50 shadow-lg"
            wire:click="togglePlaylistSidebar"
        >
            <i class="fa-solid fa-angle-left"></i>
        </button>

        <div class="grid grid-cols-1 lg:grid-cols-[minmax(auto,320px)_1fr_minmax(auto,320px)] min-h-screen py-4 gap-4 px-4">
            <!-- Left Sidebar - Artists -->
            <div class="
                lg:block 
                fixed lg:static 
                inset-0 lg:inset-auto 
                z-40 lg:z-auto 
                bg-black/50 lg:bg-transparent
                transition-all duration-300 ease-in-out
                {{ $isSideBarOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none lg:opacity-100 lg:pointer-events-auto' }}
            ">
                <div class="
                    w-80 lg:w-full lg:max-w-80 
                    h-full lg:h-auto 
                    bg-white 
                    shadow-md 
                    rounded-lg 
                    overflow-y-auto
                    ml-0 lg:ml-0
                    transform transition-transform duration-300 ease-in-out
                    {{ $isSideBarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0' }}
                ">
                    <div class="p-4">
                        <h3 class="text-lg text-center k-line font-semibold mb-3">
                            Top Ranked Artists
                        </h3>
                        
                        <ul class="space-y-2">
                            @foreach ($artists as $index => $artist)
                                @php
                                    $color = 'w-8 h-8 rounded-full flex items-center justify-center ' . match($index + 1) {
                                        1 => 'bg-amber-400',
                                        2 => 'bg-slate-300',
                                        3 => 'bg-yellow-700',
                                        default => 'bg-purple-200'
                                    };
                                @endphp

                                <li 
                                    wire:key="artist-{{ $artist->getKey() }}"
                                    class="p-2 hover:bg-gray-100 rounded-md cursor-pointer {{ $this->artist == $artist->getKey() ? 'bg-purple-100 border-l-4 border-purple-500' : '' }}"
                                    wire:click="filterByArtist({{ $artist->getKey() }})"
                                >
                                    <div class="flex items-center space-x-2">
                                        <div class="{{ $color }}">
                                            {{ $index + 1 }}
                                        </div>
                                        <span>{{ $artist->artist_name }}
                                            <span class="text-xs">({{ $artist->artist_rankings_count }})</span>
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Overlay click area (mobile only) -->
                <div class="lg:hidden absolute inset-0 -z-10" wire:click="toggleSidebar"></div>
            </div>

            <!-- Main Content -->
            <div class="w-full max-w-4xl lg:mx-auto">
                <!-- Search Section -->
                <div class="mb-4 bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center w-full space-y-2 sm:space-y-0 sm:space-x-2">
                        <input 
                            class="w-full sm:flex-1 p-2 border rounded-lg transition-all duration-300 focus:ring-2 focus:ring-purple-400 border-zinc-800" 
                            type="text" 
                            placeholder="Search rankings by name, artist or playlist" 
                            wire:model="search"
                        />
                        <div class="flex space-x-2">
                            <button 
                                type="button" 
                                class="btn-primary px-2 py-1 cursor-pointer transform transition-all duration-300 hover:scale-110 active:scale-95" 
                                wire:click="performSearch"
                            >
                                <i class="text-lg fa fa-magnifying-glass mt-1"></i>
                            </button>
                            <button 
                                type="button" 
                                class="btn-secondary px-2 py-1 cursor-pointer transform transition-all duration-300 hover:scale-110 active:scale-95" 
                                wire:click="resetSearch"
                            >
                                <i class="text-lg fa-solid fa-rotate-left mt-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                @if ($this->rankings->count())
                    <div 
                        class="flex flex-col space-y-4"
                        x-data="{ 
                            previousRankingIds: [],
                            init() {
                                this.captureRankingIds();
                                this.animateCards();
                                Livewire.hook('morph.updated', (component) => {
                                    this.$nextTick(() => {
                                        const currentIds = this.getCurrentRankingIds();
                                        if (JSON.stringify(currentIds) !== JSON.stringify(this.previousRankingIds)) {
                                            this.animateCards();
                                            this.previousRankingIds = currentIds;
                                        }
                                    });
                                });
                            },
                            getCurrentRankingIds() {
                                return Array.from(this.$el.querySelectorAll('[data-ranking-card]'))
                                    .map(card => card.getAttribute('wire:key'));
                            },
                            captureRankingIds() {
                                this.previousRankingIds = this.getCurrentRankingIds();
                            },
                            animateCards() {
                                const cards = this.$el.querySelectorAll('[data-ranking-card]');
                                cards.forEach((card) => {
                                    card.style.opacity = '0';
                                    card.style.transform = 'translateY(20px) scale(0.98)';
                                    card.style.transition = 'none';
                                });
                                
                                setTimeout(() => {
                                    cards.forEach((card) => {
                                        card.style.transition = 'all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                                        card.style.opacity = '1';
                                        card.style.transform = 'translateY(0) scale(1)';
                                    });
                                }, 50);
                            }
                        }"
                    >
                        @foreach ($this->rankings as $index => $ranking)
                            <div 
                                class="rounded-md cursor-pointer p-1 transform transition-all duration-300 hover:scale-101" 
                                wire:key="ranking-{{ $ranking->getKey() }}"
                                data-ranking-card
                            >
                                <x-ranking-card :ranking="$ranking" />
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $this->rankings->links() }}
                    </div>
                @else
                    <div class="flex justify-center">
                        <span>Loading Rankings...</span>
                    </div>
                @endif
            </div>

            <!-- Right Sidebar - Playlists -->
            <div class="
                lg:block 
                fixed lg:static 
                inset-0 lg:inset-auto 
                z-40 lg:z-auto 
                bg-black/50 lg:bg-transparent
                transition-all duration-300 ease-in-out
                {{ $isPlaylistSideBarOpen ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none lg:opacity-100 lg:pointer-events-auto' }}
            ">
                <div class="
                    w-80 lg:w-full lg:max-w-80 
                    h-full lg:h-auto 
                    bg-white 
                    shadow-md 
                    rounded-lg 
                    overflow-y-auto
                    ml-auto lg:ml-0
                    transform transition-transform duration-300 ease-in-out
                    {{ $isPlaylistSideBarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0' }}
                ">
                    <div class="p-4">
                        <h3 class="text-lg text-center k-line font-semibold mb-3">
                            Top Ranked Playlists
                        </h3>

                        @if (count($playlists))
                            <ul class="space-y-2">
                                @foreach ($playlists as $index => $playlist)
                                    @php
                                        $color = 'w-8 h-8 rounded-full flex items-center justify-center ' . match($index + 1) {
                                            1 => 'bg-amber-400',
                                            2 => 'bg-slate-300',
                                            3 => 'bg-yellow-700',
                                            default => 'bg-green-200'
                                        };
                                    @endphp

                                    <li 
                                        wire:key="playlist-{{ $playlist->getKey() }}"
                                        class="p-2 hover:bg-gray-100 rounded-md cursor-pointer {{ $this->playlist == $playlist->getKey() ? 'bg-purple-100 border-l-4 border-purple-500' : '' }}"
                                        wire:click="filterByPlaylist({{ $playlist->getKey() }})"
                                    >
                                        <div class="flex items-center space-x-2">
                                            <div class="{{ $color }}">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-medium text-sm">{{ Str::limit($playlist->name, 25) }}</div>
                                                <div class="text-xs text-gray-500">
                                                    <i class="fa fa-user mr-1"></i>
                                                    {{ $playlist->creator_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-xs flex justify-center">No playlist rankings yet</span>
                        @endif
                    </div>
                </div>

                <!-- Overlay click area (mobile only) -->
                <div class="lg:hidden absolute inset-0 -z-10" wire:click="togglePlaylistSidebar"></div>
            </div>
        </div>
    </div>
</div>