<template>
    <div :class="colBox" id="hideable-column">
        <div class="card">
            <div class="card-body mb-2" style="min-height: 100%; overflow-y: auto;">
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
                        <a :href="getListRoute(list.id)">
                            <h4 class="mb-0 mt-1">{{ list.name }}</h4>
                        </a>
                        <small class="text-muted mb-1">{{ list.artist.artist_name }} - {{ list.songs_count }} songs.</small>
                    </div>
                </div>
                <hr class="mt-2">
                <div class="row d-flex justify-content-center">
                    <div class="col-auto">
                        <h5 class="mt-1 mb-1" style="cursor: pointer;" @click="viewAll()">View All</h5>
                        <hr class="m-0" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Home Rankings List',

        props: ['lists'],

        data() {
            return {
                colBox: 'col-md-2 p-1',
            }
        },

        methods: {
            hide() {
                this.colBox = 'd-none';
                this.emitter.emit('song-list-hidden');
            },

            getListRoute(id) {
                return '/rank/' + id;
            },

            viewAll() {
                window.location.href = '/ranks';
            }
        },

        created() {
            this.emitter.on('show-song-list', (data) => {
                this.colBox = 'col-md-2 p-1';
            });
        }
    }
</script>
<style>
    a {
        text-decoration: none;
        cursor: pointer;
    }
</style>