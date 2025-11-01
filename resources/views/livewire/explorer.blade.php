<div>
    <div class="min-h-screen">
        <!-- Toggle Buttons (visible on smaller screens) -->
        <button 
            class="lg:hidden fixed left-0 top-1/2 -translate-y-1/2 bg-white border p-2 rounded-r-md z-50 shadow-lg"
            wire:click="toggleSidebar"
        >
            <i class="fa-solid fa-angle-right"></i>
        </button>

        <div class="grid grid-cols-1 lg:grid-cols-[minmax(auto,320px)_1fr_minmax(auto,320px)] min-h-screen py-4 gap-4">
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
                                    :key="'artist-'.$artist->getKey()"
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
                    <div class="flex flex-col space-y-4">
                        @foreach ($this->rankings as $ranking)
                            <div 
                                class="rounded-md cursor-pointer p-1 transform transition-all duration-300 hover:scale-101"
                                wire:key="ranking-{{ $ranking->getKey() }}"
                            >
                                <livewire:ranking.card :ranking="$ranking" wire:key="card-{{ $ranking->getKey() }}" />
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
        </div>
    </div>
</div>