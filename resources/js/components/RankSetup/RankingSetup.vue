<template>
    <div v-if="this.artistSelected" v-auto-animate>
        <div class="row">
            <div class="col-lg-4">
                <h2>Artist</h2>
                <div class="card">
                    <div class="card-body">
                        <img 
                            :src="cover" 
                            :width="this.artistImageWidth" 
                            :height="this.artistImageHeight" 
                            @click="loadSongs()" 
                            :alt="this.name"
                        />
                        <h1 class="mt-2 mx-2">{{ this.name }}</h1>
                        <a 
                            :href="artistLink" 
                            target="_blank"
                            style="border-bottom: 2px solid #06D6A0; padding-bottom: 5px; max-width: 205px; margin-left: 8px;"
                        >
                            <p style="display: inline; color: #06D6A0;">
                                Listen on <img :src="asset('spotify-logo.png')" style="display: inline;">
                            </p>
                            <div style="display: inline-block; width: 5px;"></div>
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-body">
                        <h5>Filters</h5>
                        <button type="button" class="btn btn-primary m-1" @click="filterSongs('remix')">
                            <small>Remove Remixes</small>
                        </button>
                        <button type="button" class="btn btn-secondary m-1" @click="filterSongs('live from')">
                            <small>Remove "Live From Spotify"</small>
                        </button>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-body">
                        <div class="row">
                            <h5 class="mx-1 mb-2">Enter Ranking Name</h5>
                        </div>
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
            <div class="col-lg-8">
                <h2>Tracks</h2>
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
                    <a 
                        :href="artistLink" 
                        target="_blank"
                        style="border-bottom: 2px solid #06D6A0; padding-bottom: 5px;"
                    >
                        <p style="display: inline; color: #06D6A0;">
                            Listen on <img :src="asset('spotify-logo.png')" style="display: inline;">
                        </p>
                        <div style="display: inline-block; width: 5px;"></div>
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    </a>
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
                        "Are you ready?",
                        "Are you ready to begin ranking? After starting the ranking process, you will not be able to remove or edit the songs in the ranking. You will only be able to update the title.",
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
            }
        },

        computed: {
            cover() {
                return this.cover ? this.cover : "";
            },

            artistLink() {
                return "https://open.spotify.com/artist/" + this.id;
            }
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
        max-height: 500px; 
        overflow-y: auto;
    }
</style>