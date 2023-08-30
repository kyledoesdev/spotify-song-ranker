<template>
    <div v-if="this.artistSelected">
        <div class="row">
            <div class="col">
                <div class="m-2">
                    <div class="row">
                        <div class="col">
                            <img 
                                :src="cover" 
                                :width="this.artistImageWidth" 
                                :height="this.artistImageHeight" 
                                style="cursor: pointer;" 
                                @click="loadSongs()"
                                :alt="this.name"
                            >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col d-flex">
                            <h1 class="mt-1">{{ this.name }}</h1>
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col">
                            <button
                                class="btn btn-primary m-1"
                                type="button"
                                @click="filterSongs('remix')"
                            >
                                Remove Remixes
                            </button>
                            <button
                                class="btn btn-secondary m-1"
                                type="button"
                                @click="filterSongs('live from')"
                            >
                                Remove "Live From Spotify"
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col m-2 border border-2 border-dark" style="max-height: 512px; overflow-y: auto;">
                <div class="col-auto" v-for="song in this.artistSongs" :key="song.id" >
                    <song-result :id="song.id" :name="song.name" :cover="song.images[0].url"></song-result>
                </div>
            </div>
        </div>
    </div>
    <div v-else>
        <div class="m-2">
            <div class="row">
                <div class="col">
                    <img 
                        :src="cover" 
                        :width="this.artistImageWidth" 
                        :height="this.artistImageHeight" 
                        style="cursor: pointer;" 
                        @click="loadSongs"
                        :alt="this.name"
                    >
                </div>
            </div>
            <div class="row">
                <div class="col d-flex">
                    <h5 class="mt-1">{{ this.name }}</h5>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import SongResult from './Song.vue';

    export default {
        name: 'Artist Result',

        components: {
            SongResult
        },
            
        props: ['id', 'name', 'images'],

        data() {
            return {
                artistSelected: false,
                artistImageHeight: 128,
                artistImageWidth: 128,
                artistSongs: [],
            }
        },

        methods: {
            loadSongs() {
                axios.get('/spotify/artist_songs', {
                    params: {
                        'id': this.id
                    }
                })
                .then(response => {
                    const data = response.data;

                    if (data && data.songs) {
                        let songs = response.data.songs;

                        this.emitter.emit('artist-selected', [{
                            'id': this.id,
                            'name': this.name,
                            'images': this.images
                        }]);

                        this.artistImageWidth = 512;
                        this.artistImageHeight = 512;
                        this.artistSelected = true;
                        this.artistSongs = songs;
                    }
                })
                .catch(error => {
                    console.error(error);
                })
            },

            filterSongs(phrase) {
                const filtered = [];

                for (const song in this.artistSongs) {
                    if (!this.artistSongs[song].name.toLowerCase().includes(phrase)) {
                        filtered.push(this.artistSongs[song]);
                    }
                }
                
                this.artistSongs = filtered;
            }
        },

        computed: {
            cover() {
                return this.images[0] && this.images[0]['url'] 
                    ? this.images[0]['url'] 
                    : "";
            },
        },

        created() {
            this.emitter.on('clear-songs', (data) => {
                this.artistSongs = [];
            });
        }
    }
</script>