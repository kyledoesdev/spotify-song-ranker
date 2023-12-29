<template>
    <div class="row" v-auto-animate>
        <div class="col-auto">
            <h5 class="mt-2">Search for an artist to rank to get started.</h5>
        </div>
        <div class="col-auto">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Enter an artists' name." v-model="searchTerm"/>
                <button type="button" class="btn btn-primary" @click="search" :disabled="!this.canSearch">
                    <i class="fa fa-magnifying-glass"></i>
                </button>
                <button type="button" class="btn btn-secondary" @click="reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>
        </div>
    </div>
    <hr />
    <div class="row mt-4" v-auto-animate>
        <div :class="artistClass" v-for="artist in artists" :key="artist.id">
            <rankingsetup :id="artist.id" :name="artist.name" :cover="artist.cover"></rankingsetup>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Spotify Artist Searcher",

        data() {
            return {
                searchTerm: "",
                artists: [],
                canSearch: true,
            }
        },

        methods: {
            search() {
                axios.get('/spotify/search', {
                    params: {
                        'artist': this.searchTerm
                    }
                })
                .then(response => {
                    if (response.data && response.data.artists) {
                        this.artists = response.data.artists

                        if (this.artists.length == 0) {
                            alert('Could not find artists for search term: ' + this.searchTerm);
                        }
                    }
                })
                .catch(error => {
                   alert(error.response.data.message);
                });
            },

            reset() {
                this.canSearch = true;
                this.searchTerm = "";
                this.artists = [];
                this.emitter.emit('clear-songs')
            }
        },

        computed: {
            artistClass() {
                return this.artists.length > 1 ? 'col-auto' : 'col';
            }
        },

        created() {
            this.emitter.on('artist-selected', (data) => {
                this.artists = data;
                this.canSearch = false;
            });
        }
    }
</script>