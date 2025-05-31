<template>
    <div class="bg-white rounded-lg shadow-md mt-4" style="min-height: 300px;">
        <div class="flex justify-between mt-2">
            <div>
                <h5 class="md:text-2xl mt-2 ml-2">Edit your ranking</h5>
            </div>
            <div class="mr-4">
                <button 
                    type="button" 
                    class="btn-primary" 
                    @click="update()"
                >
                    <i class="fa fa-solid fa-check"></i>
                </button>
                <button 
                    type="button" 
                    class="btn-secondary"
                    onclick="history.back()"
                >
                    <i class="fa fa-solid fa-times"></i>
                </button>
            </div>
        </div>

        <div class="mx-2 pt-4">
            <div class="mb-4">
                <label>Name:</label>
                <input 
                    class="border bg-zinc-100 rounded-lg p-1 ml-2"
                    type="text" 
                    name="ranking_name" 
                    v-model="rankingName" 
                    maxlength="30" 
                />
            </div>

            <div class="mb-4">
                <label class="mx-1 mb-2">Show In Explore Feed?</label>
                <select class="border bg-zinc-100 rounded-lg p-1 ml-2" v-model="is_public" required>
                    <option :value="true" selected>Yes</option>
                    <option :value="false">No</option>
                </select>
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
