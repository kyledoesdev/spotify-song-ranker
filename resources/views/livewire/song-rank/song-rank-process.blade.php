@php 
    $title = $ranking->name;
@endphp

<div>
    <div class="pl-4 pr-4 bg-white shadow-lg rounded-lg mt-4">
        <div class="flex justify-center bg-white p-4">
            <div class="flex items-center space-x-2 k-line">
                <span class="text-xs sm:text-sm md:text-base whitespace-nowrap font-bold">
                    Progress will be saved automatically as you rank!
                </span>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="px-4 py-2">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium">Progress</span>
                <span class="text-sm text-gray-600">{{ $progressPercentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                    style="width: {{ $progressPercentage }}%"></div>
            </div>
        </div>

        <div class="flex justify-center mb-4 md:mb-8 px-4">
            <span class="text-center">Directions: click on the song title button for the song you like more.</span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 px-2 md:px-4 overflow-x-hidden">
            @if($currentSong1 && $currentSong2)
                <div class="w-full overflow-x-hidden">
                    <div class="w-full flex flex-col">
                        @if ($showEmbeds)
                            <div class="w-full overflow-hidden">
                                <iframe
                                    src="https://open.spotify.com/embed/track/{{ $currentSong1['spotify_song_id'] }}"
                                    class="w-full max-w-full"
                                    style="min-height: 232px;"
                                    frameborder="0" 
                                    allowtransparency="true" 
                                    allow="encrypted-media"
                                ></iframe>
                            </div>
                        @else
                            <div class="mb-2 flex justify-center bg-gray-100 p-4 rounded-lg">
                                <div class="text-center">
                                    <img 
                                        src="{{ $currentSong1['cover'] }}" 
                                        alt="{{ $currentSong1['title'] }}"
                                        class="w-48 h-48 sm:w-64 sm:h-64 object-cover rounded-lg shadow-lg mx-auto"
                                    />
                                    <div class="mt-2 font-medium text-gray-800">{{ $currentSong1['title'] }}</div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="mt-4 flex flex-col items-center">
                            <hr class="w-full my-3">
                            <button 
                                class="border-2 border-zinc-800 rounded-lg hover:bg-zinc-100 text-zinc-800 px-6 py-2 transition-colors cursor-pointer"
                                wire:click="chooseSong({{ $currentSong1['id'] }})"
                                wire:key="song1-{{ $currentSong1['id'] }}"
                            >
                                {{ $currentSong1['title'] }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="w-full overflow-x-hidden">
                    <div class="w-full flex flex-col">
                        @if ($showEmbeds)
                            <div class="w-full overflow-hidden">
                                <iframe
                                    src="https://open.spotify.com/embed/track/{{ $currentSong2['spotify_song_id'] }}"
                                    class="w-full max-w-full"
                                    style="min-height: 232px;"
                                    frameborder="0" 
                                    allowtransparency="true" 
                                    allow="encrypted-media"
                                ></iframe>
                            </div>
                        @else
                            <div class="mb-2 flex justify-center bg-gray-100 p-4 rounded-lg">
                                <div class="text-center">
                                    <img 
                                        src="{{ $currentSong2['cover'] }}" 
                                        alt="{{ $currentSong2['title'] }}"
                                        class="w-48 h-48 sm:w-64 sm:h-64 object-cover rounded-lg shadow-lg mx-auto"
                                    />
                                    <div class="mt-2 font-medium text-gray-800">{{ $currentSong2['title'] }}</div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="mt-4 flex flex-col items-center">
                            <hr class="w-full my-3">
                            <button 
                                class="border-2 border-zinc-800 rounded-lg hover:bg-zinc-100 text-zinc-800 px-6 py-2 transition-colors cursor-pointer"
                                wire:click="chooseSong({{ $currentSong2['id'] }})"
                                wire:key="song2-{{ $currentSong2['id'] }}"
                            >
                                {{ $currentSong2['title'] }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <hr class="my-4" />
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center px-4 py-4 space-y-3 sm:space-y-0">
            <span class="text-sm sm:text-base flex items-center">
                Get Cozy, this may take you a while. Enjoy the process, don't rush.
                <i class="fa-solid fa-mug-saucer ml-2"></i>
            </span>
            <div class="flex items-center">
                <label class="flex items-center cursor-pointer select-none">
                    <input 
                        type="checkbox" 
                        wire:model.live="showEmbeds"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500"
                    >
                    <span class="ml-2 text-sm sm:text-base text-gray-700">Toggle Spotify Players</span>
                </label>
            </div>
        </div>
    </div>
</div>

