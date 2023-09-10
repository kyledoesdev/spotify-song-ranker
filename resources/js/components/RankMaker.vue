<template>
    <div v-if="!this.isranked"> <!-- If not ranked -->
        <div class="pl-4 pr-4" v-if="currentPair">
            <div class="row d-flex justify-content-center">
                <div class="col-md-8 alert alert-info text-center">
                    <i class="fa-solid fa-skull-crossbones"></i>
                    <span>If you refresh or leave this page, you will lose your ranking progress!</span>
                    <i class="fa-solid fa-skull-crossbones"></i>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    Ranking Name
                </div>
                <div class="card-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col-auto">
                            <!-- <iframe 
                                :src="songEmbed(this.currentPair[0].spotify_song_id)" 
                                width="512" 
                                height="232" 
                                frameborder="0" 
                                allowtransparency="true" 
                                allow="encrypted-media"
                                loading="lazy"
                            >
                            </iframe> -->
                            <img class="mb-2" :src="currentPair[0].cover" width="512" height="512" alt="" />
                            <div class="row d-flex justify-content-center">
                                <div class="col-auto">
                                    <button class="btn btn-primary" type="button" @click="chooseSong(0)">
                                        {{ this.currentPair[0].title }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <!-- <iframe 
                                :src="songEmbed(this.currentPair[1].spotify_song_id)" 
                                width="512" 
                                height="232" 
                                frameborder="0" 
                                allowtransparency="true" 
                                allow="encrypted-media"
                                loading="lazy"
                            >
                            </iframe> -->
                            <img class="mb-2" :src="currentPair[1].cover" width="512" height="512" alt="" />
                            <div class="row d-flex justify-content-center">
                                <div class="col-auto">
                                    <button class="btn btn-secondary" type="button" @click="chooseSong(1)">
                                        {{ this.currentPair[1].title }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <h5 class="text-bold mt-2">{{ pairQueueFullness }}% complete</h5>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="card">
                <div class="card-header">
                    Ranking Name
                </div>
                <div class="card-body card-scroller">
                    <ol>
                        <li v-for="song in rankedSongs" :key="song.id">
                            <song-list-item :id="song.id" :name="song.title" :cover="song.cover" :candelete="false"></song-list-item>
                        </li>
                    </ol>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col">
                            <h5 class="text-bold mt-2">{{ pairQueueFullness }}% complete</h5>
                        </div>
                        <div class="col d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" @click="save()">Save</button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <div v-else>
        <h2>Ranking Results</h2>
        <ol>
            <li v-for="song in rankedSongs" :key="song.id">
                {{ song.title }}
            </li>
        </ol>
    </div>
</template>
<script>
    import SongListItem from './RankSetup/SongListItem.vue';

    export default {
        name: 'Rank Maker',

        props: ['rankingid', 'ranksongs', 'isranked'],

        components: {
            SongListItem
        },

        data() {
            return {
                songs: this.ranksongs,
                rankedSongs: this.isranked ? this.ranksongs : [],      // To store the ranked order of songs
                comparedSongs: new Set(), // To keep track of compared songs
                rankedSet: new Set(),    // To keep track of ranked songs
                pairQueue: [],        // Queue of pairs for comparison
            };
        },

        methods: {
            chooseSong(selectedIndex) {
                const chosenSong = this.currentPair[selectedIndex];

                // Check if the chosen song is already ranked
                if (!this.rankedSet.has(chosenSong.id)) {
                    // Update the rankedSongs array with the chosen song
                    this.rankedSongs.push(chosenSong);

                    // Mark the selected song as compared and ranked
                    this.comparedSongs.add(chosenSong.id);
                    this.rankedSet.add(chosenSong.id);
                }

                this.pairQueue.shift();
                
                if (this.pairQueue.length === 0) {
                    // Include unselected songs in the ranked list
                    this.songs.forEach((song) => {
                        if (!this.rankedSet.has(song.id) && !this.comparedSongs.has(song.id)) {
                            this.rankedSongs.push(song);
                        }
                    });
                }
            },

            save() {
                axios.post('/rank/update', {
                    'rankingId': this.rankingid,
                    'songs' : this.rankedSongs  
                })
                .then(response => {
                    const data = response.data;

                    if (data && data.redirect) {
                        window.location.href = data.redirect;
                    }
                });
            },

            songEmbed(songId) {
                return "https://open.spotify.com/embed/track/" + songId
            }
        },

        computed: {
            currentPair() {
                return this.pairQueue[0];
            },
            totalPossiblePairs() {
                return (this.songs.length * (this.songs.length - 1)) / 2;
            },
            pairQueueFullness() {
                return Math.round((((this.totalPossiblePairs - this.pairQueue.length) / this.totalPossiblePairs) * 100), 2);
            },
        },

        created() {
            for (let i = 0; i < this.songs.length; i++) {
                for (let j = i + 1; j < this.songs.length; j++) {
                    this.pairQueue.push([this.songs[i], this.songs[j]]);
                }
            }

            //shuffle
            //this.pairQueue.sort(() => Math.random() - 0.5);
        },

        mounted() {
            window.addEventListener('beforeunload', function(e) {
                e.returnValue = true;
            });
        }
    };
</script>
<style>
    .card-scroller {
        max-height: 596px; 
        overflow-y: auto;
    }
</style>
  