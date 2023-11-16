<template>
    <div v-if="this.artistSelected">
        <div class="row">
            <div class="col m-2">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col d-flex justify-content-center">
                                <img :src="cover" 
                                    :width="this.artistImageWidth" 
                                    :height="this.artistImageHeight" 
                                    @click="loadSongs()" 
                                    :alt="this.name"
                                >
                            </div>
                        </div>
                        <div class="row">
                            <div class="col d-flex justify-content-center">
                                <h1 class="mt-2 mb-0">{{ this.name }}</h1>
                            </div>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col d-flex justify-content-center">
                        <button type="button" class="btn btn-primary m-1" @click="filterSongs('remix')">
                            Remove Remixes
                        </button>
                        <button type="button" class="btn btn-secondary m-1" @click="filterSongs('live from')">
                            Remove "Live From Spotify"
                        </button>
                    </div>
                </div>
            </div>
            <div class="col m-2">
                <div class="card">
                    <div class="card-body card-scroller">
                        <div class="col-auto" v-for="song in this.artistSongs" :key="song.id" >
                            <songlistitem :id="song.id" :name="song.name" :cover="song.cover" :candelete="true"></songlistitem>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="row">
                    <div class="col d-flex justify-content-start">
                        <input type="text" class="form-control" :placeholder="this.name + ' list'" v-model="rankingName" />
                    </div>
                    <div class="col d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" @click="beginRanking" :disabled="this.canBeginRanking">
                            Begin Ranking
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else>
        <div class="m-2">
            <div class="row">
                <div class="col">
                    <img :src="cover" 
                        :width="this.artistImageWidth" 
                        :height="this.artistImageHeight" 
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
    export default {
        name: 'Song Ranking Setup',
            
        props: ['id', 'name', 'cover'],

        data() {
            return {
                rankingName: "",
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
                            'cover': this.cover
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
            },

            beginRanking() {
                axios.post('/rank/create', {
                    'artist_id': this.id,
                    'artist_name' : this.name,
                    'artist_img': this.cover,
                    'name': this.rankingName,
                    'songs': this.artistSongs
                })
                .then(response => {
                    const data = response.data;

                    if (data && data.redirect) {
                        window.location.href = response.data.redirect;
                    }
                    
                })
                .catch(error => {
                    console.error(error);
                });
            }
        },

        computed: {
            cover() {
                return this.cover ? this.cover : "";
            },
        },

        created() {
            this.emitter.on('clear-songs', (data) => {
                this.artistSongs = [];
            });

            this.emitter.on('song-removed', (data) => {
                const filtered = [];

                for (const song in this.artistSongs) {
                    if (this.artistSongs[song].id !== data) {
                        filtered.push(this.artistSongs[song]);
                    }
                }
                
                this.artistSongs = filtered;
            });
        }
    }
</script>

<style>
    img {
        cursor: pointer;
    }

    .card-scroller {
        max-height: 596px; 
        overflow-y: auto;
    }
</style>