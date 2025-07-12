<template>
    <div class="space-y-6" v-auto-animate>
        <!-- Spotify Search -->
        <div class="bg-white shadow-md rounded-xl">
            <div class="p-2 mb-2 md:pb-4" v-auto-animate>
                <div>
                    <h5 class="md:text-md mt-2 mb-4">
                        Search for an artist to rank to get started.
                    </h5>
                </div>
                <div class="flex flex-col sm:flex-row items-center w-full space-y-2 sm:space-y-0 sm:space-x-2">
                    <input 
                        class="w-full sm:flex-1 p-2 border border-zinc-800 rounded-lg" 
                        type="text" 
                        :placeholder="this.artistplaceholder" 
                        v-model="searchTerm"
                    />
                    <div class="flex space-x-2">
                        <button 
                            type="button" 
                            class="btn-primary px-2 py-1 cursor-pointer" 
                            @click="search" 
                            :disabled="!this.canSearch || noSearchTerm"
                        >
                            <i class="text-lg text-zinc-800 fa fa-magnifying-glass mt-1"></i>
                        </button>
                        <button 
                            type="button" 
                            class="btn-secondary px-2 py-1 cursor-pointer" 
                            @click="reset"
                        >
                            <i class="text-lg text-zinc-800 fa-solid fa-rotate-left mt-1"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div :class="artistClass" v-auto-animate>
                <div  v-for="artist in artists" :key="artist.id">
                    <rankingsetup :id="artist.id" :name="artist.name" :cover="artist.cover"></rankingsetup>
                </div>
            </div>
        </div>

        <!-- In Progress Rankings -->
        <div class="bg-white shadow-md rounded-lg p-2" v-if="this.inprogressrankings.length && artists.length <= 0" v-auto-animate>
            <h5 class="md:text-md mt-2 mb-4 px-2" v-auto-animate>
                Pick up where you left off
            </h5>

            <div 
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" 
                v-auto-animate
            >
                <div
                    class="border rounded-xl"
                    v-for="ranking in this.inprogressrankings" 
                    :key="ranking.id"
                    v-auto-animate
                >
                    <exploreitem :ranking="ranking" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Dashboard",

        props: ['artistplaceholder', 'inprogressrankings'],

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
                            this.flash("No Artists' found.", 'Could not find artists for search term: ' + this.searchTerm, 'error');
                        }
                    }
                })
                .catch(error => {
                    this.flash("Error", error.response.data.message, 'error');
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
                return this.artists.length > 1 
                    ? 'grid grid-cols-2 md:grid-cols-6 gap-2 md:gap-0' 
                    : '';
            },

            noSearchTerm() {
                return this.searchTerm === "" || this.searchTerm == null;
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