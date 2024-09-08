<template>
    <div v-if="this.artistSelected" v-auto-animate>
        <div class="grid grid-cols-1 md:grid-cols-2 m-2 p-2">
            <div>
                <div class="grid">
                    <h5 class="md:text-4xl mb-2">
                        Artist:
                    </h5>
                    <div class="mb-4">
                        <img 
                            class="border rounded-lg border-zinc-800"
                            :src="albumArt" 
                            @click="loadSongs()" 
                            :alt="this.name"
                            style="max-height: 20rem; max-height: 20rem;"
                        />
                        <div class="flex">
                            <div>
                                <h5 class="md:text-2xl mt-2 mb-2">{{ this.name }}</h5>
                            </div>
                            <div class="mt-2 mx-2">
                                <spotify-logo :artistLink="this.id" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2">
                    <div class="grid grid-cols-1 mt-3 mb-2">
                        <h5 class="md:text-2xl">Filters</h5>
                        <button 
                            type="button" 
                            class="btn-primary" 
                            @click="filterSongs('remix')"
                        >
                            Remove Remixes
                        </button>
                        <button 
                            type="button" 
                            class="btn-secondary" 
                            @click="filterSongs('live from')"
                        >
                            Remove "Live From" Tracks
                        </button>
                        <button 
                            type="button" 
                            class="btn-helper" 
                            @click="filterSongs('instrumental')"
                        >
                            Remove "Instrumental" Tracks
                        </button>
                    </div>

                    <div class="grid grid-cols-1">
                        <h5 class="md:tex-2xl mx-1 mb-2">Custom Ranking Name?</h5>
                        <input 
                            type="text" 
                            class="mx-1 border border-zinc-800 rounded p-2 mb-4"
                            :placeholder="this.name + ' List'"
                            v-model="rankingName"
                            maxlength="30"
                        />
                    </div>

                    <div class="grid grid-cols-1 gap-0 mb-2">
                        <h5 class="mb-2 mx-1">Show In Explore Feed?</h5>
                        <select class="border border-zinc-800 rounded mx-1 bg-white p-1" v-model="is_public" required>
                            <option :value="true" selected>Yes</option>
                            <option :value="false">No</option>
                        </select>
                    </div>

                    <button 
                        type="button" 
                        class="btn-animated"
                        @click="beginRanking"
                    >
                        <h5 class="text-lg md:text-2xl uppercase">Begin Ranking</h5>
                    </button>
                </div>
            </div>
            <div class="w-full">
                <h5 class="md:text-4xl mb-2">Tracks</h5>
                <div class="card-scroller" v-auto-animate>
                    <div v-for="song in this.artistSongs" :key="song.id" v-auto-animate>
                        <songlistitem :id="song.id" :name="song.name" :cover="song.cover" :candelete="true"></songlistitem>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else>
        <div class="m-2 p-2">
            <img 
                class="border border-zinc-800 m-2"
                :src="cover" 
                @click="loadSongs"
                :alt="this.name"
            >
            <h5 class="mt-1">{{ this.name }}</h5>
            <spotify-logo :artistLink="this.id" />
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
        max-height: 80vh; 
        overflow-y: auto;
    }
</style>