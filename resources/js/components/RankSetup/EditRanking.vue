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
            <div class="card-body" style="min-height: 600px;">
                <div class="row" v-auto-animate>
                    <div class="col-md-6">
                        <h5 for="ranking_name">Change ranking name:</h5>
                        <input class="form-control mb-2" type="text" name="ranking_name" v-model="rankingName" />
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
                is_public: this.ranking.is_public != 0
            }
        },

        methods: {
            update() {
                axios.post('/rank/' + this.ranking.id + '/update', {
                    'name' : this.rankingName,
                    'is_public': this.is_public
                })
                .then(response => {
                    window.location.href = response.data.redirect;
                })
                .catch(error => {
                    this.flash("Something went wrong.", "We could not update your ranking at this time. Please try again later.", 'error');
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
