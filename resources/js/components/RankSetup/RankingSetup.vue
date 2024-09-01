<template>
    <div v-if="this.artistSelected" v-auto-animate>
        <div class="row">
            <div class="col-lg-4">
                <h5>Artist</h5>
                <div class="card">
                    <div class="card-body">
                        <img 
                            :src="albumArt" 
                            :width="this.artistImageWidth" 
                            :height="this.artistImageHeight" 
                            @click="loadSongs()" 
                            :alt="this.name"
                        />
                        <h5 class="mt-2 mx-2">{{ this.name }}</h5>
                        <spotify-logo :artistLink="this.id" />
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <h5>Filters</h5>
                        <button type="button" class="btn btn-primary m-1" @click="filterSongs('remix')">
                            <small>Remove Remixes</small>
                        </button>
                        <button type="button" class="btn btn-secondary m-1" @click="filterSongs('live from')">
                            <small>Remove "Live From" Tracks</small>
                        </button>
                        <button type="button" class="btn btn-warning m-1" @click="filterSongs('instrumental')">
                            <small>Remove "Instrumental" Tracks</small>
                        </button>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-auto">
                                <h5 class="mx-1 mb-2">Custom Ranking Name?</h5>
                                <input type="text" class="form-control" :placeholder="this.name + ' List'" v-model="rankingName" maxlength="30" />
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-auto">
                                <h5 class="mx-1 mb-2">Show In Explore Feed?</h5>
                                <select class="form-select" v-model="is_public" required>
                                    <option :value="true" selected>Yes</option>
                                    <option :value="false">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-auto">
                                <button type="button" class="btn btn-lg border border-1 border-dark gradient-background p-3" @click="beginRanking">
                                    <h5 class="text-uppercase k-line mt-1">Begin Ranking</h5>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <h5>Tracks</h5>
                <div class="card" v-auto-animate>
                    <div class="card-body card-scroller" v-auto-animate>
                        <div class="col-auto" v-for="song in this.artistSongs" :key="song.id" v-auto-animate>
                            <songlistitem :id="song.id" :name="song.name" :cover="song.cover" :candelete="true"></songlistitem>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else>
        <div class="m-2 p-2">
            <div class="row">
                <div class="col-auto">
                    <img :src="cover" 
                        :width="this.artistImageWidth" 
                        :height="this.artistImageHeight" 
                        @click="loadSongs"
                        :alt="this.name"
                    >
                    <h5 class="mt-1">{{ this.name }}</h5>
                    <spotify-logo :artistLink="this.id" />
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
                is_public: true,
                artistSelected: false,
                artistImageHeight: 200,
                artistImageWidth: 200,
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

            async beginRanking() {
                let confirmed = await this.buildFlash()
                    .check(
                        "Being Ranking?",
                        "Are you ready to begin ranking your selected songs? After starting the ranking process, you WILL NOT be able to remove or edit the songs in the ranking. You will only be able to update the title & visiblity of your ranking.",
                        "info",
                        "Let's Go!",
                        "Cancel"
                    );

                if (confirmed) {
                    axios.post('/rank/create', {
                        'artist_id': this.id,
                        'artist_name' : this.name,
                        'artist_img': this.cover,
                        'name': this.rankingName,
                        'songs': this.artistSongs,
                        'is_public': this.is_public
                    })
                    .then(response => {
                        const data = response.data;

                        if (data && data.redirect) {
                            window.location.href = response.data.redirect;
                        }
                        
                    })
                    .catch(error => {
                        if (error.response && error.response.data && error.response.data.message) {
                            this.flash("Something went wrong.", `There was an error making your ranking record. Error message: ${error.response.data.message}`, 'error');
                        } else {
                            this.flash("Something went wrong.", `Please try again later. Something went wrong creating your ranking record.`, 'error');
                        }
                        
                        console.error(error);
                    });
                }
            }
        },

        computed: {
            albumArt() {
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
        max-height: 70vh; 
        overflow-y: auto;
    }
</style>