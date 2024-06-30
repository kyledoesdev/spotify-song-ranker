<!-- Fuck you spotify for making me have to abstract your annoying ahh logo into a damn component -->
<template>
    <a 
        :href="artistLink" 
        target="_blank"
        style="border-bottom: 2px solid #06D6A0; padding-bottom: 5px;"
    >
        <p style="display: inline; color: #06D6A0;">
            Listen on <img :src="asset('spotify-logo.png')" style="display: inline;">
        </p>
        <div style="display: inline-block; width: 5px;"></div>
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
    </a>
</template>
<script>
    export default {
        name: 'Spotify Logo',

        props: ['artistLink'],

        computed: {
            artistLink() {
                return "https://open.spotify.com/artist/" + this.artistLink;
            }
        }
    }
</script>