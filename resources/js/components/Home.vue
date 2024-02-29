<template>
    <div class="row">
        <div :class="spotifyRankingBox">
            <div class="card" style="min-height: 600px">
                <div class="card-body">
                    <spotifysearch />
                </div>
            </div>
        </div>
        <div :class="listVisiblity">
            <homerankingslist :lists="this.lists"></homerankingslist>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Home',

        props: ['lists'],

        data() {
            return {
                spotifyRankingBox: 'col-lg-9',
                listVisiblity: 'col-lg-3'
            }
        },

        created() {
            this.emitter.on('artist-selected', (data) => {
                this.spotifyRankingBox = 'col-12';
                this.listVisiblity = 'd-none';
            });

            this.emitter.on('clear-songs', (data) => {
                this.spotifyRankingBox = 'col-lg-9',
                this.listVisiblity = 'col-lg-3'
            });
        }
    }
</script>