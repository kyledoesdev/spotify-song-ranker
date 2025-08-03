<div>
    <div class="flex flex-col min-h-screen relative">
        <!-- Mobile Toggle Button -->
        <button 
            class="md:hidden fixed left-0 top-1/2 -translate-y-1/2 bg-white border border-zinc-800 p-2 rounded-r-md z-50"
            wire:click="toggleSidebar"
        >
            <i class="fa-solid fa-angle-right"></i>
        </button>

        <div class="flex flex-1 py-4">
            <!-- Mobile Sidebar Overlay (Invisible) -->
            @if($isSideBarOpen)
                <div class="md:hidden fixed inset-0 bg-transparent z-40" wire:click="toggleSidebar"></div>
            @endif

            <!-- Filters Sidebar -->
            <div 
                class="fixed md:static md:block z-50 md:h-auto h-screen top-0 transition-all duration-300 ease-in-out {{ $isSideBarOpen ? 'left-0' : '-left-72' }}"
            >
                <div class="w-72 md:h-auto h-full md:rounded-lg bg-white overflow-y-auto shadow-md">
                    <div class="p-4">
                        <h3 class="text-lg text-center k-line font-semibold mb-3">
                            Top Ranked Artists
                        </h3>
                        
                        <ul class="space-y-2">
                            @foreach ($artists as $index => $artist)
                                @php
                                    $color = 'w-8 h-8 rounded-full flex items-center justify-center ' . match($index) {
                                        0 => 'bg-amber-400',
                                        1 => 'bg-slate-300',
                                        2 => 'bg-yellow-700',
                                        default => 'bg-purple-200'
                                    };
                                @endphp

                                <li 
                                    wire:key="artist-{{ $artist->getKey() }}"
                                    class="p-2 hover:bg-gray-100 rounded-md cursor-pointer {{ $this->artist == $artist->getKey() ? 'bg-blue-100 border-l-4 border-blue-500' : '' }}"
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

                    <!-- Search Section -->
                    <div class="p-4">
                        <div class="flex flex-col space-y-2">
                            <div>
                                <input 
                                    class="w-full p-1 border border-zinc-800 bg-zinc-100 rounded-md transition-all duration-300 focus:ring-2 focus:ring-blue-400" 
                                    type="text" 
                                    placeholder="Search..." 
                                    wire:model="search"
                                />
                            </div>
                            <div class="flex justify-center space-x-2">
                                <button 
                                    type="button" 
                                    class="btn-primary px-2 transform transition-all duration-300 hover:scale-110 active:scale-95" 
                                    wire:click="performSearch" 
                                >
                                    <i class="text-lg text-zinc-800 fa fa-magnifying-glass"></i>
                                </button>
                                <button 
                                    type="button" 
                                    class="btn-secondary px-2 transform transition-all duration-300 hover:scale-110 active:scale-95" 
                                    wire:click="resetSearch"
                                >
                                    <i class="text-lg text-zinc-800 fa-solid fa-rotate-left"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Centered Main Content -->
            <div class="w-full md:flex md:justify-center">
                <div class="max-w-3xl w-full px-4">
                    @if ($this->rankings->count())
                        <div 
                            class="flex flex-col space-y-4"
                            x-data="{ show: false }"
                            x-init="setTimeout(() => show = true, 100)"
                            x-show="show"
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 transform translate-y-4"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-4"
                        >
                            @foreach ($this->rankings as $ranking)
                                <div 
                                    class="rounded-md cursor-pointer p-1 transform transition-all duration-300 hover:scale-101" 
                                    wire:key="ranking-{{ $ranking->getKey() }}"
                                >
                                    <x-ranking-card :ranking="$ranking" />
                                </div>
                            @endforeach
                        </div>

                        <div class="w-full overflow-x-auto mt-4">
                            {{ $this->rankings->links() }}
                        </div>
                    @else
                        <div class="flex justify-center">
                            <span>Loading Rankings...</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Spacer for Balance -->
            <div class="hidden md:block w-72">
            </div>
        </div>
    </div>
</div>