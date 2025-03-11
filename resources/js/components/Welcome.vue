<template>
    <section class="mb-4">
        <div>
            <div class="flex justify-center">
                <h5 class="text-4xl md:text-6xl lg:text-7xl md:mb-2">Song - Rank</h5>
            </div>
            <div class="flex justify-center">
                <h5 class="text-xs mt-1 md:text-lg">Spotify Discography Ranking creator.</h5>
            </div>
            <div class="flex justify-center mt-1">
                <span>Made with ❤️ by 
                    <a class="text-blue-600" href="https://bsky.app/profile/kyledoes.dev">
                        Kyle
                    </a>
                </span>
            </div>
        </div>

        <div>
            <div class="flex justify-center">
                <div class="mt-4 border border-2 rounded-lg border-zinc-600">
                    <img src="/cover.png" alt="">
                </div>
            </div>
        </div>

        <div class="flex justify-center gap-4 mt-4">
            <a class="btn-secondary m-2 p-2" href="/explore">
                <span class="text-lg pb-2">Explore</span>
            </a>
            <a class="btn-primary m-2 p-2" :href="buttonhref">
                <span v-if="this.authid" class="text-lg">View Dashboard</span>
                <span v-else class="text-lg">Login & Start</span>
            </a>
        </div>        
    </section>
</template>

<script>
    export default {
        name: "Welcome Page",

        props: ['users', 'rankings'],

        data() {
            return {
                user_count: this.users,
                rankings_count: this.rankings
            }
        },

        computed: {
            buttonhref() {
                return this.authid ? '/home' : '/login/spotify';
            },
        }
    }
</script>