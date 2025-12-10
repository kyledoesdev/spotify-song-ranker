@props(['ranking', 'song'])

@use('App\Enums\RankingType')

<div class="m-2 mb-2">
    <div class="flex">
        <div>
            <img 
                src="{{ $song->cover }}" 
                alt="name"
                class="w-12 h-12 sm:w-[72px] sm:h-[72px] rounded-xl mr-4" 
            />
        </div>
        <div class="flex flex-1">
            <div class="min-w-0 pt-1 flex-1">
                <h5 class="mx-2 mt-1 mb-0.5 text-xs sm:text-base break-words" title="{{ $song->title }}">
                    @if ($ranking->isPlaylistType()) <span class="font-bold">{{ $song->artist->artist_name }}</span> - @endif {{ $song->title }}
                </h5>
                <div class="flex items-center mx-2">
                    <a 
                        href="https://open.spotify.com/{{ $song->artist?->is_podcast ? 'episode' : 'track' }}/{{ $song->spotify_song_id }}"
                        target="_blank"
                        class="inline-flex items-center gap-2"
                        style="border-bottom: 2px solid #06D6A0; padding-bottom: 2px;"
                    >
                        <p class="inline text-[#06D6A0]">
                            <img 
                                src="/spotify-logo.png" 
                                class="inline w-auto h-3.5 sm:h-4"
                                style="aspect-ratio: 3.15"
                                alt="Spotify"
                            >
                        </p>
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>