<template>
    <div class="m-2 p-2">
        <div class="row">
            <div class="col-auto" style="min-width: 27vw;">
                <a :href="ranking.show_route">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-auto">
                                    <img 
                                        :src="ranking.artist.artist_img" 
                                        width="135" 
                                        height="135" 
                                        :alt="ranking.user.name"
                                    >
                                    <br>
                                    <spotify-logo :artistLink="ranking.artist.artist_id" />
                                </div>
                                <div class="col mt-1">
                                    <div class="row">
                                        <h2 :title="ranking.name">{{ truncate(ranking.name, 20) }}</h2>
                                    </div>
                                    <explore-artist-name :ranking="ranking" />
                                    <explore-top-song :ranking="ranking" />
                                    <explore-song-count :ranking="ranking" />
                                    <explore-ranking-creator :ranking="ranking" />
                                    <explore-ranking-completed-at :ranking="ranking" />
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Explore Item',

        props: ['ranking'],

        data() {
            return {
                
            }
        },
    }
</script>