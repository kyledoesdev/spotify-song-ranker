<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        Edit your ranking
                    </div>
                    <div class="col d-flex justify-content-end">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary border border-1 border-dark" @click="update()">
                                Save
                            </button>
                            <button type="button" class="btn btn-secondary border border-1 border-dark" onclick="history.back()">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body" style="max-height: 600px;">
                <div class="row" v-auto-animate>
                    <div class="col-md-6">
                        <label for="ranking_name">Change ranking name:</label>
                        <input class="form-control mb-2" type="text" name="ranking_name" id="" v-model="rankingName" />
                    </div>
                    <hr/>
                    <h5>Remove Songs from the Ranking</h5>
                    <div class="row" v-for="song in songs" :key="song.id">
                        <songlistitem 
                            class="col-sm-6 m-1"
                            :id="song.id"
                            :name="song.title"
                            :cover="song.cover"
                            :candelete="true"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Edit Ranking',

        props: ['ranking'],

        data() {
            return {
                rankingName: this.ranking.name,
                songs: this.ranking.songs
            }
        },

        methods: {
            update() {
                axios.post('/rank/' + this.ranking.id + '/update', {
                    'songs': this.songs,
                    'name' : this.rankingName,
                })
                .then(response => {
                    window.location.href = response.data.redirect;
                })
                .catch(error => {
                    console.error(error);
                })
            }
        },

        created() {
            this.emitter.on('song-removed', (data) => {
                const filtered = [];

                for (const song in this.songs) {
                    if (this.songs[song].id !== data) {
                        filtered.push(this.songs[song]);
                    }
                }
                
                this.songs = filtered;
            });
        }
    }
</script>
