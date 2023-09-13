<template>
    <div class="row">
        <div :class="colBox">
            <div class="card" style="min-height: 600px">
                <div class="card-body">
                    <spotify-search />
                </div>
            </div>
        </div>
        <home-song-list :lists="this.lists"></home-song-list>
    </div>
</template>
<script>
    import HomeSongList from './Lists/HomeSongList.vue';
    import SpotifySearch from './RankSetup/SpotifySearch.vue';

    export default {
        name: 'Home',

        props: ['lists'],

        components: {
            SpotifySearch,
            HomeSongList
        },

        data() {
            return {
                colBox: 'col-md-10 p-1'
            }
        },

        created() {
            this.emitter.on('show-song-list', (data) => {
                this.colBox = 'col-md-10 p-1';
            });

            this.emitter.on('song-list-hidden', (data) => {
                this.colBox = 'col-md-12 p-1'
            });
        }
    }
</script>