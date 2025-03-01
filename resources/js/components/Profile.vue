<template>
    <div class="mt-8" v-auto-animate>
        <div class="flex justify-center">
            <h5 class="text-xl md:text-4xl p-4 k-line">{{ this.display_name }} Rankings</h5>
        </div>

        <div v-if="ranks" class="overflow-x-auto" v-auto-animate>
            <div class="flex flex-col items-center space-y-8 mt-4" v-if="ranks" v-auto-animate>
                <div class="border border-zinc-800 bg-white rounded-lg mb-2" v-for="ranking in ranks" :key="ranking.id" v-auto-animate>
                    <exploreitem class="mb-4" :ranking="ranking" />

                    <div class="text-dark m-4" v-if="this.authid && ranking.user_id == this.authid">
                        <a 
                            class="border border-zinc-800 bg-purple-400 rounded-sm px-2 py-1 mx-2"
                            @click="show(ranking.id)"
                        >
                            <i class="fa fa-eye"></i>
                        </a>
                        <a 
                            class="border border-zinc-800 bg-green-300 rounded-sm px-2 py-1 mx-2"
                            :href="getEditURI(ranking.id)"
                        >
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a class="border border-zinc-800 bg-red-400 rounded-sm px-2 py-1 mx-2"
                            @click="destroy(ranking.id)"
                        >
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-center space-y-8 space-x-8" v-else>
            <span>{{ this.no_rankings_msg }}</span>
            <span></span>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Profile',

        props: ['user', 'rankings', 'name'],

        data() {
            return {
                ranks: this.rankings,
                display_name: this.name,
                no_rankings_msg: "No rankings found."
            }      
        },

        methods: {
            show(rankingId) {
                window.location.href = '/rank/' + rankingId;
            },

            async destroy(rankingId) {
                let confirmed = await this.buildFlash()
                    .overrideFlashStyles({
                        'confirm-btn': 'btn-danger m-2 p-2',
                        'cancel-btn': 'btn-secondary m-2 p-2'
                    })
                    .check(
                        "Delete Ranking?",
                        "Are you sure you want to delete this ranking?",
                        "info",
                        "Delete"
                    );

                if (confirmed) {
                    axios.post('/rank/destroy', {
                        'rankingId': rankingId
                    })
                    .then(response => {
                        const data = response.data;

                        if (data && data.message && data.rankings) {
                            this.flash('Ranking Deleted', data.message, 'success');
                            this.ranks = data.rankings;
                        }
                    })
                    .catch(error => {
                        console.error(error);
                    });
                }
            },

            topSong(ranking) {
                return Object.values(ranking.songs).find(song => song.rank === 1)?.title;
            },

            getEditURI(rankingid) {
                return '/rank/' + rankingid + '/edit';
            },

            visibility(ranking) {
                return ranking.is_public == 0 ? 'No' : 'Yes';
            }
        },
    }
</script>
<style scoped>
    .table-container {
        overflow-x: auto;
        width: 100%;
    }

    .text-sm {
        font-size: 0.875rem;
    }
</style>