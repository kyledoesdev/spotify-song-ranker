<template>
    <div class="row">
        <div class="col-auto">
            <h5 class="mt-2">Search Spotify for an artist to rank.</h5>
        </div>
        <div class="col-auto">
            <div class="input-group">
                <input 
                    class="form-control"
                    type="text"
                    placeholder="Enter an artists' name."
                    v-model="searchTerm"
                />
                <button 
                    type="button"
                    class="btn btn-primary"
                    @click="search"
                >
                    Search
                </button>
                <button 
                    type="button"
                    class="btn btn-secondary"
                    @click="reset"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                </button>
            </div>
        </div>
    </div>

    <hr />

    <div class="row mt-4">
        <div :class="artistClass" v-for="artist in artists" :key="artist.id">
            <artist-result :id="artist.id" :name="artist.name" :images="artist.images"></artist-result>
        </div>
    </div>
</template>

<script>
    import ArtistResult from './Artist.vue';

    export default {
        name: "Spotify Artist Searcher",

        components: {
            ArtistResult
        },

        data() {
            return {
                searchTerm: "",
                artists: []
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
                    }
                })
                .catch(error => {
                   alert(error.response.data.message);
                });
            },

            reset() {
                this.searchTerm = "";
                this.artists = [];

                this.emitter.emit('clear-songs')
            }
        },

        computed: {
            artistClass() {
                console.log(this.artists.length)
                return this.artists.length > 1 ? 'col-auto' : 'col';
            }
        },

        created() {
            this.emitter.on('artist-selected', (data) => {
                this.artists = data;
            });
        }
    }
</script>