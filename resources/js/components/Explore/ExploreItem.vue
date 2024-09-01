<template>
    <div class="m-2 p-2">
        <div class="row">
            <div class="col-auto" style="min-width: 40vw;">
                <a :href="ranking.show_route">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-auto mt-3">
                                    <img 
                                        :src="ranking.artist.artist_img"
                                        class="mb-2"
                                        width="90" 
                                        height="90" 
                                        :alt="ranking.user.name"
                                    >
                                    <br>
                                    <spotify-logo :artistLink="ranking.artist.artist_id" />
                                </div>
                                <div class="col mt-3">
                                    <div class="row">
                                        <h4 :title="ranking.name">{{ truncate(ranking.name, 30) }}</h4>
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