<template>
    <div class="bg-white border border-zinc-800 rounded-lg mt-4" style="min-height: 600px;;">
        <div class="flex justify-between k-line">
            <div>
                <h5 class="md:text-4xl px-2 pt-2">Edit your ranking</h5>
            </div>
            <div>
                <button 
                    type="button" 
                    class="border border-2 border-zinc-800 rounded-lg bg-purple-400 hover:bg-purple-500 text-zinc-800 m-1 p-2" 
                    @click="update()"
                >
                    Save
                </button>
                <button 
                    type="button" 
                    class="border border-2 border-zinc-800 rounded-lg bg-green-300 hover:bg-green-400 m-1 p-2"
                    onclick="history.back()"
                >
                    Cancel
                </button>
            </div>
        </div>

        <div class="p-4">
            <div class="mb-4">
                <h5>Change ranking name:</h5>
                <input 
                    class="border border-zinc-800 p-2 bg-zinc-100 rounded-lg"
                    type="text" 
                    name="ranking_name" 
                    v-model="rankingName" 
                    maxlength="30" 
                />
            </div>

            <div>
                <h5 class="mx-1 mb-2">Show In Explore Feed?</h5>
                <select class="border border-zinc-800 bg-zinc-100 rounded-lg" v-model="is_public" required>
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
