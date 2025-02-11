@php $title = $ranking->is_ranked ? $ranking->name : 'Ranking - ' . $ranking->artist->artist_name; @endphp

@extends('layouts.app')

@section('content')
    @include('layouts.nav')

    <div class="pl-4 pr-4 bg-white border border-zinc-800 rounded-lg mt-4">
        @if ($ranking->is_ranked)
            <div class="flex justify-end">
                <a onclick="history.back()" class="btn-secondary p-2 m-2">
                    <i class="fa fa-solid fa-arrow-left"></i>
                </a>
                <share />
            </div>

            <hr>
        @endif

        @if (!$ranking->is_ranked)
            <div class="flex justify-center bg-white p-4">
                <div class="text-center w-full">
                    <i class="fa-solid fa-skull-crossbones"></i>&nbsp;
                    <span class="k-line">If you leave this page, you will lose your progress!</span>&nbsp;
                    <i class="fa-solid fa-skull-crossbones"></i>
                </div>
            </div>

            <div class="flex justify-center md:mb-8">
                <span>Directions: click on the song title button for the song you like more.</span>
            </div>

            <div class="p-4" id="song-container"></div>
        @endif
        
        <div class="grid grid-cols-1 md:flex justify-center">
            @if ($ranking->is_ranked)
                <div class="md:w-full m-2" style="max-height: 600px; overflow-y: auto;">
                    <ol> {{-- did not include list-decimal class here because looks better wit $loop counter --}}
                        @foreach ($songs as $song)
                            <div class="flex">
                                <div class="p-4 mt-4">{{ $loop->index + 1 }}.</div>
                                <div>
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
                <div class="m-2 p-2" id="song_1_box"></div>
                <div class="m-2 p-2" id="song_2_box"></div>
            @endif
        </div>

        <hr />
        
        @if (!$ranking->is_ranked)
            <div class="flex justify-center">
                <span class="mt-2 mb-2">Get Cozy, this may take you a while. <i class="fa-solid fa-mug-saucer"></i></span>
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

        function compareSongs(song1, song2) {
            return new Promise((resolve) => {
                let song1Box = document.getElementById('song_1_box');
                let song2Box = document.getElementById('song_2_box');
                
                if (song1Box !== null && song2Box !== null) {
                    song1Box.innerHTML = buildChoice(song1);
                    song2Box.innerHTML = buildChoice(song2);
                }

                window.resolveComparison = (selectedId) => {
                    song1Box.innerHTML = '';
                    song2Box.innerHTML = '';
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

        // Start the sorting process
        async function startSortingProcess() {
            const sortedSongs = await mergeSort(songs);
            displaySortedSongs(sortedSongs);
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

        function buildChoice(song) {
            return `
                <iframe
                    class="mb-2"
                    src="https://open.spotify.com/embed/track/${song.spotify_song_id}"
                    width="512" 
                    height="232" 
                    frameborder="0" 
                    allowtransparency="true" 
                    allow="encrypted-media"
                >
                </iframe>
                <a 
                    class="mb-2"
                    href="https://open.spotify.com/track/${song.spotify_song_id}"
                    target="_blank"
                    style="border-bottom: 2px solid #06D6A0; padding-bottom: 2px; margin-botton:2px;"
                >
                    <p style="display: inline; color: #06D6A0;">
                        <img src="${logo}" style="display: inline;">
                    </p>
                    <div style="display: inline-block; width: 5px;"></div>
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
                <br>
                <hr>
                <div class="flex justify-center mt-2">
                    <div class="col-auto">
                        <button 
                            class="border border-2 border-zinc-800 rounded-lg hover:bg-zinc-100 text-zinc-800 m-2 p-2" 
                            onclick="resolveComparison(${song.id})"
                        >
                            ${song.title}
                        </button>
                    </div>
                </div>
            `
        }

        // Call this function when you want to start the sorting process
        startSortingProcess();

        let isSorted = document.getElementById('is_sorted').value;

        if (isSorted == false || isSorted == 0) {
            window.addEventListener("beforeunload", function(event) {
                // Cancel the event
                event.preventDefault();
                // Chrome requires returnValue to be set
                event.returnValue = "";

                // Prompt the user
                var confirmationMessage = "Are you sure you want to leave? Your changes may not be saved.";
                (event || window.event).returnValue = confirmationMessage; //Gecko + IE
                return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.
            });
        }
    </script>
@endpush