{{-- Spotify Logo Component --}}
@php
    $link = null;
    if (isset($artist)) {
        $link = "https://open.spotify.com/artist/" . $artist;
    } elseif (isset($song)) {
        $link = "https://open.spotify.com/track/" . $song;
    } elseif (isset($playlist)) {
        $link = "https://open.spotify.com/playlist/" . $playlist;
    }
@endphp

<div>
    <a 
        href="{{ $link }}"
        target="_blank"
        class="inline-flex items-center gap-2"
        style="border-bottom: 2px solid #06D6A0; padding-bottom: 2px;"
        onclick="event.stopPropagation(); window.open('{{ $link }}', '_blank'); return false;"
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