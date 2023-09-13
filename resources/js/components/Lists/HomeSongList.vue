<template>
    <div :class="colBox" id="hideable-column">
        <div class="card">
            <div class="card-body" style="min-height: 596px; overflow-y: auto;">
                <div class="row">
                    <div class="col">
                        <h5 class="mt-2">Your Lists</h5>
                    </div>
                    <div class="col d-flex justify-content-end">
                        <button type="button" class="btn btn-sm border border-dark m-1" @click="hide()">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                    </div>
                </div>
                <hr />
                <div v-for="list in lists" :key="list.id">
                    <div class="row">
                        <h4 class="mb-0 mt-1">{{ list.name }}</h4>
                        <small class="text-muted mb-1">{{ list.artist.artist_name }} - {{ list.songs_count }} songs.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Song Lists',

        props: ['lists'],

        data() {
            return {
                colBox: 'col-md-2 p-1'
            }
        },

        methods: {
            hide() {
                this.colBox = 'd-none';
                this.emitter.emit('song-list-hidden');
            }
        },

        created() {
            this.emitter.on('show-song-list', (data) => {
                this.colBox = 'col-md-2 p-1';
            });
        }
    }
</script>