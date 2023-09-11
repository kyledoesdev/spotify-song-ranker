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
                    <h5 class="mt-2">{{ this.rankname }}</h5>
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
                    <h5 class="mt-2">{{ this.rankname }}</h5>
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
        <div class="card">
            <div class="card-header">
                <h5 class="mt-2">{{ this.rankname }}</h5>
            </div>
            <div class="card-body card-scroller">
                <ol>
                    <li v-for="song in rankedSongs" :key="song.id">
                        <song-list-item :id="song.id" :name="song.title" :cover="song.cover" :candelete="false"></song-list-item>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</template>
<script>
    import SongListItem from './RankSetup/SongListItem.vue';

    export default {
        name: 'Rank Maker',

        props: ['rankingid', 'rankname', 'ranksongs', 'isranked'],

        components: {
            SongListItem
        },

        data() {
            return {
                songs: this.ranksongs,
                rankedSongs: this.isranked ? this.ranksongs : [],      // To store the ranked order of songs
                eloRatings: {},       // Elo ratings for songs
                pairQueue: [],        // Queue of pairs for comparison
            };
        },

        methods: {
            chooseSong(selectedIndex) {
                // Check if the chosen song is already ranked
                const winner = this.currentPair[selectedIndex];
                const loser = this.currentPair[1 - selectedIndex];

                // Update Elo ratings based on the outcome
                this.updateEloRatings(winner.id, loser.id);

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
</style>
  