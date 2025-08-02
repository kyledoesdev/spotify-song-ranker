<div>
    <div class="bg-white shadow-md rounded-xl">
        <div class="p-2 mb-2 md:pb-4" x-auto-animate>
            <div x-auto-animate>
                <h5 class="md:text-md mt-2 mb-4">
                    Search for an artist to rank to get started.
                </h5>
            </div>
            <div class="flex flex-col sm:flex-row items-center w-full space-y-2 sm:space-y-0 sm:space-x-2">
                <input 
                    wire:key="search-input"
                    class="w-full sm:flex-1 p-2 border border-zinc-800 rounded-lg transition-all duration-300 focus:ring-2 focus:ring-blue-400" 
                    type="text" 
                    placeholder="{{ $randomArtist }}" 
                    wire:model="searchTerm"
                    wire:keydown.enter="searchArtist"
                />
                <div class="flex space-x-2">
                    <button 
                        type="button" 
                        class="btn-primary px-2 py-1 cursor-pointer transform transition-all duration-300 hover:scale-110 active:scale-95" 
                        wire:click="searchArtist"
                        x-data 
                        @click="window.showLoader(); $wire.searchArtist().then(() => window.hideLoader())"
                    >
                        <i class="text-lg text-zinc-800 fa fa-magnifying-glass mt-1"></i>
                    </button>
                    <button 
                        type="button" 
                        class="btn-secondary px-2 py-1 cursor-pointer transform transition-all duration-300 hover:scale-110 active:scale-95" 
                        wire:click="resetSetup"
                    >
                        <i class="text-lg text-zinc-800 fa-solid fa-rotate-left mt-1"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Main content area with auto-animate for smooth transitions between states -->
        <div x-auto-animate.300ms>
            <!-- Searched Artists Grid -->
            @if ($searchedArtists && !$selectedArtist)
                <div class="grid grid-cols-2 md:grid-cols-6 gap-2 md:gap-0" x-auto-animate>
                    @foreach ($searchedArtists as $artist)
                        <div 
                            wire:key="artist-{{ $artist['id'] }}"
                            class="m-2 p-2"
                            x-data="{ show: false }"
                            x-init="setTimeout(() => show = true, {{ $loop->index * 50 }})"
                            x-show="show"
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 transform scale-75 translate-y-4"
                            x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-300"
                            x-transition:leave-start="opacity-100 transform scale-100"
                            x-transition:leave-end="opacity-0 transform scale-75"
                        >
                            <img
                                class="shadow-md rounded-4xl m-2 cursor-pointer transform transition-all duration-300 hover:scale-105 hover:shadow-lg"
                                src="{{ $artist['cover'] }}"
                                wire:click="loadArtistSongs('{{ $artist['id'] }}')"
                                alt="{{ $artist['name'] }}"
                                x-data 
                                @click="window.showLoader(); $wire.searchArtist().then(() => window.hideLoader())"
                            >
                            <h5 class="mt-1 transition-all duration-300">{{ $artist['name'] }}</h5>
                            <div x-auto-animate>
                                <x-spotify-logo :artist="$artist['id']" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Selected Artist View -->
            @if ($selectedArtist)
                <div 
                    class="grid grid-cols-1 md:grid-cols-2 m-2 p-2"
                    x-data="{ show: false }"
                    x-init="show = true"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 transform translate-x-4"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                >
                    <div x-auto-animate>
                        <div class="grid">
                            <h5 class="md:text-2xl mb-2 transition-all duration-300">
                                Artist: {{ $selectedArtist['name'] }}
                            </h5>
                            <div class="mb-4">
                                <img
                                    class="rounded-4xl w-48 h-48 transform transition-all duration-300 hover:scale-105 hover:shadow-lg"
                                    src="{{ $selectedArtist['cover'] }}"
                                    alt="{{ $selectedArtist['name'] }}"
                                    wire:click="loadArtistSongs"
                                />
                                <div class="flex flex-col" x-auto-animate>
                                    <div class="mt-1">
                                        <x-spotify-logo :artist="$selectedArtist['id']" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="md:w-1/2" x-auto-animate>
                            <div class="mt-3 mb-4">
                                <h5 class="md:text-2xl mb-2 transition-all duration-300">Filters</h5>
                                <div class="flex flex-wrap gap-2" x-auto-animate>
                                    <button
                                        type="button"
                                        class="btn-primary px-2 py-1 text-sm transform transition-all duration-300 hover:scale-105 active:scale-95 hover:shadow-md"
                                        wire:click="filterSongs('remix')"
                                    >
                                        Remove Remixes
                                    </button>
                                    <button
                                        type="button"
                                        class="btn-secondary px-2 py-1 text-sm transform transition-all duration-300 hover:scale-105 active:scale-95 hover:shadow-md"
                                        wire:click="filterSongs('live from')"
                                    >
                                        Remove "Live From" Tracks
                                    </button>
                                    <button
                                        type="button"
                                        class="btn-helper px-2 py-1 text-sm transform transition-all duration-300 hover:scale-105 active:scale-95 hover:shadow-md"
                                        wire:click="filterSongs('instrumental')"
                                    >
                                        Remove "Instrumental" Tracks
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4" x-auto-animate>
                                <label class="block md:text-xl mb-2 transition-all duration-300">Custom Ranking Name?</label>
                                <input
                                    type="text"
                                    class="w-full bg-zinc-100 rounded-lg p-2 transition-all duration-300 focus:ring-2 focus:ring-blue-400 focus:scale-105"
                                    placeholder="{{ $selectedArtist['name'] . ' List' }}"
                                    wire:model.live.debounce.500ms="form.name"
                                    maxlength="30"
                                />
                            </div>

                            <div class="mb-4" x-auto-animate>
                                <label class="block mb-2 transition-all duration-300">Show In Explore Feed?</label>
                                <select
                                    class="w-full rounded-lg bg-white p-2 transition-all duration-300 focus:ring-2 focus:ring-blue-400"
                                    wire:model.live.debounce.500ms="form.is_public"
                                    required
                                >
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>

                            <button
                                type="button"
                                class="btn-animated p-2 w-full transform transition-all duration-300 hover:scale-105 active:scale-95 hover:shadow-lg"
                                wire:click="confirmBeginRanking"
                            >
                                <h5 class="text-lg md:text-2xl uppercase cursor-pointer">Begin Ranking</h5>
                            </button>
                        </div>
                    </div>
                    <div class="w-full" x-auto-animate>
                        <h5 class="md:text-4xl mb-2 transition-all duration-300">
                            Tracks ({{ count($selectedArtistTracks) }})
                        </h5>
                        
                        <!-- Songs list with auto-animate for smooth add/remove -->
                        <div class="card-scroller" x-auto-animate.200ms>
                            @if($selectedArtistTracks)
                                @foreach ($selectedArtistTracks as $song)
                                    <div
                                        wire:key="song-wrapper-{{ $song['id'] }}"
                                        x-data="{ show: false }"
                                        x-init="setTimeout(() => show = true, {{ min($loop->index * 30, 300) }})"
                                        x-show="show"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 transform translate-x-4"
                                        x-transition:enter-end="opacity-100 transform translate-x-0"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100 transform translate-x-0"
                                        x-transition:leave-end="opacity-0 transform -translate-x-4"
                                    >
                                        <livewire:song-rank.song-list-item
                                            :key="'song-item-'.$song['id']"
                                            :song="$song"
                                            :canDelete="true"
                                        />
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>