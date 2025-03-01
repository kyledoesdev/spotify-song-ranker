@php $title = $ranking->is_ranked ? $ranking->name : 'Ranking - ' . $ranking->artist->artist_name; @endphp

@extends('layouts.app')

@section('content')
    @include('layouts.nav')

    <div class="pl-4 pr-4 bg-white border border-zinc-800 rounded-lg mt-4">
        @if ($ranking->is_ranked)
            <div class="flex justify-between items-center">
                <div>
                    <h5 class="text-base sm:text-lg md:text-xl font-medium">{{ $ranking->name }}</h5>
                </div>
                <div class="flex">
                    <a onclick="history.back()" class="btn-secondary p-1 sm:p-2 m-1 sm:m-2">
                        <i class="fa fa-solid fa-arrow-left text-sm sm:text-base"></i>
                    </a>
                    <share />
                </div>
            </div>
            <hr>
        @endif

        @if (!$ranking->is_ranked)
            <div class="flex justify-center bg-white p-4">
                <div class="flex items-center space-x-2 k-line">
                    <span class="text-xs sm:text-sm md:text-base whitespace-nowrap font-bold">If you leave this page, you will lose your progress!</span>
                </div>
            </div>

            <div class="flex justify-center mb-4 md:mb-8 px-4">
                <span class="text-center">Directions: click on the song title button for the song you like more.</span>
            </div>

            <div class="p-4" id="song-container"></div>
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 px-2 md:px-4 overflow-x-hidden">
            @if ($ranking->is_ranked)
                <div class="md:col-span-2 w-full m-2" style="max-height: 600px; overflow-y: auto;">
                    <ol>
                        @foreach ($songs as $song)
                            <div class="flex">
                                <div class="p-2 md:p-4 mt-4">{{ $loop->index + 1 }}.</div>
                                <div class="flex-1">
                                    <li>
                                        <songlistitem 
                                            id="{{ $song->getKey() }}"
                                            spotifyid="{{ $song->spotify_song_id }}"
                                            name="{{ $song->title }}" 
                                            cover="{{ $song->cover }}" 
                                            :candelete="false"
                                        ></songlistitem>
                                    </li>
                                </div>
                            </div>
                        @endforeach
                    </ol>
                </div>
            @else
                <div class="w-full overflow-x-hidden" id="song_1_box"></div>
                <div class="w-full overflow-x-hidden" id="song_2_box"></div>
            @endif
        </div>

        <hr class="my-4" />
        
        @if (!$ranking->is_ranked)
            <!-- Improved footer spacing and mobile layout -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center px-4 py-4 space-y-3 sm:space-y-0">
                <span class="text-sm sm:text-base flex items-center">
                    Get Cozy, this may take you a while. Enjoy the process, don't rush.
                    <i class="fa-solid fa-mug-saucer ml-2"></i>
                </span>
                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer select-none">
                        <input 
                            type="checkbox" 
                            id="showEmbeds"
                            checked
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500"
                        >
                        <span class="ml-2 text-sm sm:text-base text-gray-700">Toggle Spotify Players</span>
                    </label>
                </div>
            </div>
        @endif
    </div>

    <input type="hidden" id="is_sorted" value="{{ $ranking->is_ranked }}" />
@endsection

@push('scripts')
    <script defer>
        let logo = "{!! asset('spotify-logo.png') !!}";
        let songs = {!! $songs->shuffle() !!};
        let sortedList = null;
        let showEmbeds = true;
        let currentSong1 = null;
        let currentSong2 = null;

        function initializeToggles() {
            const embedToggle = document.getElementById('showEmbeds');
            if (embedToggle) {
                embedToggle.addEventListener('change', function(e) {
                    showEmbeds = e.target.checked;
                    
                    // Rebuild current comparison if it exists
                    const song1Box = document.getElementById('song_1_box');
                    const song2Box = document.getElementById('song_2_box');
                    
                    if (currentSong1 && song1Box) {
                        song1Box.innerHTML = buildChoice(currentSong1);
                    }

                    if (currentSong2 && song2Box) {
                        song2Box.innerHTML = buildChoice(currentSong2);
                    }
                });
            }
        }

        function compareSongs(song1, song2) {
            return new Promise((resolve) => {
                let song1Box = document.getElementById('song_1_box');
                let song2Box = document.getElementById('song_2_box');
                
                if (song1Box !== null && song2Box !== null) {
                    // Update current songs
                    currentSong1 = song1;
                    currentSong2 = song2;
                    
                    song1Box.innerHTML = buildChoice(song1);
                    song2Box.innerHTML = buildChoice(song2);
                }

                window.resolveComparison = (selectedId) => {
                    song1Box.innerHTML = '';
                    song2Box.innerHTML = '';
                    currentSong1 = null;
                    currentSong2 = null;
                    const selectedSong = selectedId === song1.id ? song1 : song2;
                    resolve(selectedSong);
                };
            });
        }

        async function merge(left, right) {
            let result = [];

            while (left.length && right.length) {
                // Await user input to compare songs
                const preferredSong = await compareSongs(left[0], right[0]);
                if (preferredSong.id === left[0].id) {
                    result.push(left.shift());
                } else {
                    result.push(right.shift());
                }
            }

            // Concatenate the remaining songs
            return [...result, ...left, ...right];
        }

        async function mergeSort(songs) {
            if (songs.length <= 1) {
                return songs;
            }

            const middle = Math.floor(songs.length / 2);
            const left = songs.slice(0, middle);
            const right = songs.slice(middle);

            // Await the sorted halves
            const sortedLeft = await mergeSort(left);
            const sortedRight = await mergeSort(right);

            // Merge the sorted halves
            return await merge(sortedLeft, sortedRight);
        }

        function displaySortedSongs(sortedSongs) {
            const songContainer = document.getElementById('song-container');

            let output = "<div class='m-2'><ol class='list-decimal'>";

            sortedSongs.forEach(song => {
                output += `
                    <li>
                        <div class="flex p-2">
                            <div><img src="${song.cover}" width="48" height="48" alt="${song.title}"/></div>
                            <div class="mt-4"><span class="p-2">${song.title}</span></div>    
                        </div>
                    </li>
                `;
            });

            output += `
                </ol>
                <button 
                    type="button" 
                    class="border border-2 border-zinc-800 rounded-lg hover:bg-zinc-100 text-zinc-800 mt-4 p-2"
                    onclick="save()"
                >
                    <span class="uppercase">Save</span>
                </button>
                </div>
            `

            songContainer.innerHTML = output;
            sortedList = sortedSongs;
        }

        function buildChoice(song) {
            const playerHtml = showEmbeds ? `
                <div class="w-full overflow-hidden">
                    <iframe
                        src="https://open.spotify.com/embed/track/${song.spotify_song_id}"
                        class="w-full max-w-full"
                        style="min-height: 232px;"
                        frameborder="0" 
                        allowtransparency="true" 
                        allow="encrypted-media"
                    ></iframe>
                </div>
            ` : `
                <div class="mb-2 flex justify-center bg-gray-100 p-4 rounded-lg">
                    <div class="text-center">
                        <img 
                            src="${song.cover}" 
                            alt="${song.title}"
                            class="w-48 h-48 sm:w-64 sm:h-64 object-cover rounded-lg shadow-lg mx-auto"
                        />
                        <div class="mt-2 font-medium text-gray-800">${song.title}</div>
                    </div>
                </div>
            `;

            return `
                <div class="w-full flex flex-col">
                    ${playerHtml}
                    <div class="mt-4 flex flex-col items-center">
                        <hr class="w-full my-3">
                        <button 
                            class="border-2 border-zinc-800 rounded-lg hover:bg-zinc-100 text-zinc-800 px-6 py-2 transition-colors"
                            onclick="resolveComparison(${song.id})"
                        >
                            ${song.title}
                        </button>
                    </div>
                </div>
            `;
        }

        function save() {
            axios.post('/song-placement/store', {
                'rankingId': {!! $ranking->getKey() !!},
                'songs' : sortedList 
            })
            .then(response => {
                const data = response.data;

                if (data && data.redirect) {
                    window.location.href = data.redirect;
                }
            });
        }

        // Start the sorting process and initialize toggles after DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeToggles();
            startSortingProcess();
        });

        // Start the sorting process
        async function startSortingProcess() {
            const sortedSongs = await mergeSort(songs);
            displaySortedSongs(sortedSongs);
        }

        let isSorted = document.getElementById('is_sorted').value;

        if (isSorted == false || isSorted == 0) {
            window.addEventListener("beforeunload", function(event) {
                // Cancel the event
                event.preventDefault();
                // Chrome requires returnValue to be set
                event.returnValue = "";

                var confirmationMessage = "Are you sure you want to leave? Your changes may not be saved.";
                (event || window.event).returnValue = confirmationMessage;
                return confirmationMessage;
            });
        }
    </script>
@endpush