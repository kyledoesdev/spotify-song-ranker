<template>
    <a 
        :href="link"
        target="_blank"
        class="inline-flex items-center gap-2"
        style="border-bottom: 2px solid #06D6A0; padding-bottom: 2px;"
        @click.stop
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
</template>

<script>
    export default {
        name: 'Spotify Logo',

        props: ['artist', 'song'],

        computed: {
            link() {
                if (this.artist) {
                    return "https://open.spotify.com/artist/" + this.artist;
                }
                if (this.song) {
                    return "https://open.spotify.com/track/" + this.song;
                }
            }
        }
    }
</script>