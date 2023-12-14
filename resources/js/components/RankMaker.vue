<template>
    <div v-if="!this.isranked"> <!-- If not ranked -->
        <div class="pl-4 pr-4" v-if="currentPair">
            <div class="row d-flex justify-content-center">
                <div class="col-md-8 alert alert-info text-center">
                    <i class="fa-solid fa-skull-crossbones"></i>&nbsp;
                    <span>If you refresh or leave this page, you will lose your ranking progress!</span>&nbsp;
                    <i class="fa-solid fa-skull-crossbones"></i>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="row d-flex">
                        <div class="col justify-content-start">
                            <h1 class="mt-2">{{ this.rankname }}</h1>
                        </div>
                        <div class="col d-flex justify-content-end m-2">
                            <button class="btn btn-primary border border-1 border-dark" @click="this.home()">
                                <i class="fa fa-house"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col d-flex justify-content-center">
                            <span>Directions: click on the song title button for the song you like more.</span>
                        </div>
                    </div>
                    <hr />
                    <div class="row d-flex justify-content-center">
                        <div class="col-auto" v-auto-animate>
                            <iframe 
                                :src="this.songEmbed(this.currentPair[0].spotify_song_id)" 
                                width="512" 
                                height="232" 
                                frameborder="0" 
                                allowtransparency="true" 
                                allow="encrypted-media"
                                loading="lazy"
                                v-if="this.spotifyWidgetsEnabled"
                            >
                            </iframe>
                            <img 
                                class="mb-2" 
                                :src="currentPair[0].cover"
                                width="512" 
                                height="512" 
                                alt="" 
                                v-else 
                            />
                            <div class="row d-flex justify-content-center">
                                <div class="col-auto">
                                    <button class="btn btn-primary" type="button" @click="chooseSong(0)">
                                        {{ this.currentPair[0].title }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <iframe 
                                :src="this.songEmbed(this.currentPair[1].spotify_song_id)" 
                                width="512" 
                                height="232" 
                                frameborder="0" 
                                allowtransparency="true" 
                                allow="encrypted-media"
                                loading="lazy"
                                v-if="this.spotifyWidgetsEnabled"
                            >
                            </iframe>
                            <img 
                                class="mb-2" 
                                :src="currentPair[1].cover" 
                                width="512" 
                                height="512" 
                                alt="" 
                                v-else 
                            />
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
                    <div class="row">
                        <div class="col d-flex justify-content-start">
                            <h5 class="text-bold mt-2">{{ pairQueueFullness }}% complete</h5>
                        </div>
                        <div class="col d-flex justify-content-center">
                            <span class="mt-2">Get Cozy, this may take you a while. <i class="fa-solid fa-mug-saucer"></i></span>
                        </div>
                        <div class="col d-flex justify-content-end">
                            <span class="m-2" title="The page loads faster with this toggled off.">
                                Enable Spotify Widgets <i class="fa fa-question-circle"></i>
                            </span>
                            <label class="switch">
                                <input type="checkbox">
                                <span class="slider round" @click="enableWidgets()"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="card">
                <div class="card-header">
                    <h5 class="mt-2">{{ this.rankname }}</h5>
                </div>
                <div class="card-body card-scroller">
                    <ol>
                        <li v-for="song in rankedSongs" :key="song.id">
                            <songlistitem :id="song.id" :name="song.title" :cover="song.cover" :candelete="false"></songlistitem>
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
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col d-flex justify-content-start">
                        <h5 class="mt-2">{{ this.rankname }} :: {{ this.creator }}</h5>
                    </div>
                    <div class="col d-flex justify-content-end">
                        <button class="btn btn-secondary" type="button" onclick="history.back()">Back</button>
                    </div>
                </div>
            </div>
            <div class="card-body card-scroller">
                <ol>
                    <li v-for="song in rankedSongs" :key="song.id">
                        <songlistitem :id="song.id" :name="song.title" :cover="song.cover" :candelete="false"></songlistitem>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Rank Maker',

        props: ['rankingid', 'rankname', 'ranksongs', 'isranked', 'creator'],

        data() {
            return {
                songs: this.ranksongs,
                rankedSongs: this.isranked ? this.ranksongs : [],      // To store the ranked order of songs
                eloRatings: {},       // Elo ratings for songs
                pairQueue: [],        // Queue of pairs for comparison
                comparison: 0,
                spotifyWidgetsEnabled: false,
            };
        },

        methods: {
            chooseSong(selectedIndex) {
                // Check if the chosen song is already ranked
                const winner = this.currentPair[selectedIndex];
                const loser = this.currentPair[1 - selectedIndex];

                // Update Elo ratings based on the outcome
                this.updateEloRatings(winner.id, loser.id);

                this.comparison++;

                this.pairQueue.shift();
                
                if (this.pairQueue.length === 0) {
                    // Include unselected songs in the ranked list
                    for (const songId in this.eloRatings) {
                        const song = this.songs.find(obj => obj.id == songId);

                        this.rankedSongs.push({
                            'id': songId,
                            'spotify_song_id': song.spotify_song_id,
                            'title': song.title,
                            'cover': song.cover,
                            'rank': this.eloRatings[songId].rank
                        });
                    }
                    
                    this.rankedSongs.sort((a, b) => b.rank - a.rank);
                }
            },

            updateEloRatings(winnerId, loserId) {
                const k = 32;
                const winnerRating = this.eloRatings[winnerId].rank;
                const loserRating = this.eloRatings[loserId].rank;

                const expectedOutcome = 1 / (1 + Math.pow(10, (loserRating - winnerRating) / 400));
                const actualOutcome = 1; // Winner won
                const ratingChange = k * (actualOutcome - expectedOutcome);

                this.eloRatings[winnerId].rank += ratingChange;
                this.eloRatings[loserId].rank -= ratingChange;
            },

            save() {
                axios.post('/rank/finish', {
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

            enableWidgets() {
                this.spotifyWidgetsEnabled = ! this.spotifyWidgetsEnabled;
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
            this.songs.forEach((song) => {
                this.eloRatings[song.id] = { rank: 1000 }; // Initial rating (adjust as needed)
            });

            for (let i = 0; i < this.songs.length; i++) {
                for (let j = i + 1; j < this.songs.length; j++) {
                    this.pairQueue.push([this.songs[i], this.songs[j]]);
                }
            }

            //shuffle
            this.pairQueue.sort(() => Math.random() - 0.5);
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

    /* The switch - the box around the slider */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
  