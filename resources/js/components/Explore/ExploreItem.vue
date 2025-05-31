<template>
    <div class="m-2" style="min-width: 35vw;" v-auto-animate>
        <a :href="ranking.show_route" v-auto-animate>
            <div class="flex space-x-6" v-auto-animate>
                <div>
                    <img 
                        :src="ranking.artist.artist_img"
                        class="rounded-lg border border-zinc-800 mb-2"
                        width="120" 
                        height="120" 
                        :alt="ranking.user.name"
                    >
                    <spotify-logo :artist="ranking.artist.artist_id" />
                </div>
                <div>
                    <h5 class="mb-1" :title="ranking.name">
                        <span class="text-base md:text-lg">{{ truncate(ranking.name, 30) }}</span>
                    </h5>
                    <explore-artist-name :ranking="ranking" />
                    <explore-top-song :ranking="ranking" />
                    <explore-song-count :ranking="ranking" />
                    <div v-if="this.routename != 'profile.show'">
                        <explore-ranking-creator :ranking="ranking" />
                    </div>
                    <explore-ranking-completed-at :ranking="ranking" />
                </div>
            </div>
        </a>
    </div>
</template>
<script>
    export default {
        name: 'Explore Item',

        props: ['ranking'],
    }
</script>