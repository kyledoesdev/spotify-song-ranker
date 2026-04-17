@use('App\Enums\RankingType')

<div>
    <div class="bg-white shadow-md rounded-xl">
        <div class="p-2 mb-2" x-auto-animate>
            <div class="space-y-4 md:space-y-0">
                <h5 class="md:text-md mt-2 mb-4 md:text-left">
                    Choose a ranking type to get started. Enter in an artist name, or the playlist or show URL.
                </h5>

                <div class="flex flex-col md:flex-row md:items-center md:gap-4">
                    {{-- Icon Tab Selector --}}
                    @php
                        $typeLocked = $this->isTypeLocked();
                        $tabClasses = fn (RankingType $tab) => match (true) {
                            $type === $tab => 'border-' . $tab->color() . '-500 bg-' . $tab->color() . '-100 text-' . $tab->color() . '-700 shadow-md cursor-pointer',
                            $typeLocked => 'border-zinc-200 bg-zinc-100 text-zinc-300 cursor-not-allowed opacity-50',
                            default => 'border-zinc-300 bg-white text-zinc-500 hover:border-zinc-400 cursor-pointer',
                        };
                    @endphp
                    <div class="flex flex-wrap gap-2 mb-4 md:mb-0 md:shrink-0">
                        @foreach ([RankingType::ARTIST, RankingType::PLAYLIST, RankingType::SHOW] as $tab)
                            <button
                                type="button"
                                @if (! $typeLocked || $type === $tab)
                                    wire:click="$set('type', '{{ $tab->value }}')"
                                @endif
                                @disabled($typeLocked && $type !== $tab)
                                class="flex items-center gap-2 px-4 py-2 rounded-full border-2 transition-all duration-300 {{ $tabClasses($tab) }}"
                            >
                                <i class="fa-solid {{ $tab->icon() }}"></i>
                                <span class="font-medium text-sm">{{ $tab->label() }}</span>
                            </button>
                        @endforeach
                    </div>

                    {{-- Search Input --}}
                    <div class="flex-1 flex flex-col sm:flex-row items-center w-full space-y-2 sm:space-y-0 sm:space-x-2">
                        <input
                            wire:key="search-input"
                            class="w-full sm:flex-1 p-2 border border-zinc-800 rounded-lg transition-all duration-300 focus:ring-2 focus:ring-blue-400"
                            type="text"
                            placeholder="{{ match($type) {
                                RankingType::ARTIST => $randomArtist,
                                RankingType::PLAYLIST => 'https://open.spotify.com/playlist/...',
                                RankingType::SHOW => 'https://open.spotify.com/show/...',
                            } }}"
                            wire:model="searchTerm"
                            wire:keydown.enter="search"
                        />
                        <div class="flex space-x-2">
                            <button
                                type="button"
                                class="btn-primary px-2 py-1 cursor-pointer transform transition-all duration-300 hover:scale-110 active:scale-95"
                                x-data
                                @click="window.showLoader(); $wire.search().then(() => window.hideLoader())"
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
            </div>
        </div>

        {{-- Main content area with auto-animate for smooth transitions between states --}}
        <div x-auto-animate.300ms>
            {{-- Searched Artists Grid --}}
            @if ($searchedArtists && !$selectedArtist)
                <div class="grid grid-cols-2 md:grid-cols-6 gap-2 md:gap-0" x-auto-animate>
                    @foreach ($searchedArtists as $artist)
                        <div
                            :key="'artist-'.$artist['id']"
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
                                alt="{{ $artist['name'] }}"
                                x-data
                                @click="window.showLoader(); $wire.loadArtistSongs('{{ $artist['id'] }}').then(() => window.hideLoader())"
                            >
                            <h5 class="mt-1 transition-all duration-300">{{ $artist['name'] }}</h5>
                            <div>
                                <x-spotify-logo :artist="$artist['id']" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Selected Artist/Playlist/Show View --}}
            @if ($selectedArtist || $selectedPlaylist || $selectedShow)
                @php
                    $rankingCollection = match(true) {
                        !empty($selectedArtist) => $selectedArtist,
                        !empty($selectedPlaylist) => $selectedPlaylist,
                        default => $selectedShow,
                    };
                    $tracks = $selectedTracks;
                @endphp

                <div
                    class="grid grid-cols-1 md:grid-cols-3 gap-8 m-2 p-2 pt-4"
                    x-data="{
                        show: false,
                        removedUuids: [],
                        visibleCount: 0,
                        updateCount() {
                            const total = {{ count($tracks ?? []) }};
                            this.visibleCount = total - this.removedUuids.length;
                        }
                    }"
                    x-init="
                        show = true;
                        removedUuids = @js($removedTrackUuids ?? []);
                        updateCount();
                    "
                    @tracks-batch-removed.window="
                        $event.detail.uuids.forEach(uuid => {
                            if (!removedUuids.includes(uuid)) {
                                removedUuids.push(uuid);
                            }
                        });
                        updateCount();
                    "
                    @song-removed.window="
                        if (!removedUuids.includes($event.detail.uuid)) {
                            removedUuids.push($event.detail.uuid);
                        }
                        updateCount();
                    "
                    x-init="show = true"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 transform translate-x-4"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                >
                    <div class="md:col-span-1" x-auto-animate>
                        <div class="flex flex-col justify-between h-full">
                            <div>
                                <div>
                                    <div class="my-2" x-auto-animate>
                                        <label>Name the Ranking:</label>
                                        <input
                                            type="text"
                                            class="w-full bg-zinc-100 rounded-lg p-2 focus:ring-2 focus:ring-blue-400"
                                            placeholder="{{ $rankingCollection['name'] . ' List' }}"
                                            wire:model.live.debounce.500ms="form.name"
                                            maxlength="30"
                                        />
                                    </div>

                                    <div class="my-2" x-auto-animate>
                                        <label>Show In Explore Feed?</label>
                                        <select
                                            class="w-full bg-zinc-100 rounded-lg bg-white p-2 focus:ring-2 focus:ring-blue-400"
                                            wire:model.live.debounce.500ms="form.is_public"
                                            required
                                        >
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>

                                    <div class="my-2" x-auto-animate>
                                        <label>Enable Comments</label>
                                        <select
                                            class="w-full bg-zinc-100 rounded-lg bg-white p-2 focus:ring-2 focus:ring-blue-400"
                                            wire:model.live.debounce.500ms="form.comments_enabled"
                                            required
                                        >
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>

                                    <div class="my-2" x-auto-animate>
                                        <label>Enable Comment Replies</label>
                                        <select
                                            class="w-full bg-zinc-100 rounded-lg bg-white p-2 focus:ring-2 focus:ring-blue-400 disabled:opacity-50 disabled:cursor-not-allowed"
                                            wire:model.live.debounce.500ms="form.comments_replies_enabled"
                                            @disabled(!$form->comments_enabled || $form->comments_enabled === '0')
                                            required
                                        >
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="btn-primary py-1 px-2 mt-4"
                                    wire:click="confirmBeginRanking"
                                >
                                    <h5 class="text-lg md:text-2xl uppercase cursor-pointer">Begin Ranking</h5>
                                </button>
                            </div>
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
                        </div>
                    </div>
                    <div class="md:col-span-2" x-auto-animate>
                        <h5 class="md:text-4xl mb-2 transition-all duration-300">
                            {{ ucfirst($type->itemLabel()) }} (<span x-text="visibleCount"></span>)
                        </h5>

                        {{-- Songs list with auto-animate for smooth add/remove --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 lg:gap-1 card-scroller">
                            @if($tracks)
                                @foreach ($tracks as $song)
                                    <div
                                        wire:key="song-wrapper-{{ $song['uuid'] }}"
                                        data-track-uuid="{{ $song['uuid'] }}"
                                        x-data="{ show: false }"
                                        x-init="setTimeout(() => show = true, {{ min($loop->index * 30, 300) }})"
                                        x-show="show && !$data.removedUuids?.includes('{{ $song['uuid'] }}')"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 transform translate-x-4"
                                        x-transition:enter-end="opacity-100 transform translate-x-0"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100 transform translate-x-0"
                                        x-transition:leave-end="opacity-0 transform -translate-x-4"
                                    >
                                        <livewire:song-rank.song-list-item
                                            wire:key="song-item-{{ $song['uuid'] }}"
                                            :song="$song"
                                            :type="$type"
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
