@php $title = $ranking->is_ranked ? $ranking->name : title(); @endphp

@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="pl-4 pr-4">
            @if (!$ranking->is_ranked)
                <div class="row d-flex justify-content-center">
                    <div class="col-md-8 alert alert-info text-center">
                        <i class="fa-solid fa-skull-crossbones"></i>&nbsp;
                        <span>If you refresh or leave this page, you will lose your ranking progress!</span>&nbsp;
                        <i class="fa-solid fa-skull-crossbones"></i>
                    </div>
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <div class="row d-flex">
                        <div class="col justify-content-start">
                            <h1 class="mt-2">{{ $ranking->name }}</h1>
                        </div>
                        <div class="col d-flex justify-content-end m-2">
                            @if (prev_route() == 'explore')
                                <a href="{{ route('explore') }}" class="btn btn-primary border border-1 border-dark mt-1 mx-2">
                                    Go Back
                                </a>
                            @endif
                            <a href="{{ route('home') }}" class="btn btn-primary border border-1 border-dark mt-1">
                                <i class="fa fa-house"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (!$ranking->is_ranked)
                        <div class="row">
                            <div class="col d-flex justify-content-center" style="max-height: 596px; overflow-y: auto;">
                                <span>Directions: click on the song title button for the song you like more.</span>
                            </div>
                        </div>
                        <hr />
                    @endif
                    <div class="row d-flex justify-content-center" id="song-container">
                        @if ($ranking->is_ranked)
                            <div class="m-2" style="max-height: 600px; overfloy-y: auto;">
                                <ol>
                                    @foreach ($songs as $song)
                                        <li>
                                            <songlistitem 
                                                id="{{ $song->getKey() }}" 
                                                name="{{ $song->title }}" 
                                                cover="{{ $song->cover }}" 
                                                :candelete="false"
                                            ></songlistitem>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        @else
                            <div class="col-auto" id="song_1_box"></div>
                            <div class="col-auto" id="song_2_box"></div>
                        @endif
                    </div>
                </div>
                @if (!$ranking->is_ranked)
                    <div class="card-footer">
                        <div class="row">
                            <div class="col d-flex justify-content-center">
                                <span class="mt-2">Get Cozy, this may take you a while. <i class="fa-solid fa-mug-saucer"></i></span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
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

            let output = "<div class='m-2'><ol>";

            sortedSongs.forEach(song => {
                output += `
                    <li>
                        <div class="m-2">
                            <div class="row d-flex">
                                <div class="col-auto">
                                    <img src="${song.cover}" width="48" height="48" alt="${song.title}"/>
                                    <span class="mx-2">${song.title}</span>
                                </div>
                            </div>
                        </div>
                    </li>
                `;
            });

            output += `
                </ol>
                <button type="button" class="btn btn-lg btn-primary" onclick="save()">Save</button>
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
            axios.post('/rank/finish', {
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
                    src="https://open.spotify.com/embed/track/${song.spotify_song_id}"
                    width="512" 
                    height="232" 
                    frameborder="0" 
                    allowtransparency="true" 
                    allow="encrypted-media"
                >
                </iframe>
                <br>
                <a 
                    href="https://open.spotify.com/track/${song.spotify_song_id}"
                    target="_blank"
                    style="border-bottom: 2px solid #06D6A0; padding-bottom: 5px;"
                >
                    <p style="display: inline; color: #06D6A0;">
                        Listen on <img src="${logo}" style="display: inline;">
                    </p>
                    <div style="display: inline-block; width: 5px;"></div>
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
                <br>
                <hr>
                <div class="row d-flex justify-content-center pt-2">
                    <div class="col-auto">
                        <button 
                            class="btn btn-primary border border-dark border-1" 
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