<template>
    <div class="mt-8" v-auto-animate>
        <h5 class="text-xl md:text-4xl px-4">{{ this.display_name }} rankings</h5>

        <div :class="rankingDisplayMode" v-if="this.rankings" v-auto-animate>
            <div 
                class="bg-white shadow-md rounded-lg cursor-pointer hover:shadow-lg transition-shadow relative p-2" 
                v-for="ranking in ranks" 
                :key="ranking.id" 
                v-auto-animate
                @click="show(ranking.id)"
            >
                <exploreitem :ranking="ranking" />

                <div class="absolute top-2 right-2 flex space-x-1" v-if="this.authid && ranking.user_id == this.authid" @click.stop>
                    <a 
                        class="text-gray-500 hover:text-green-600 transition-colors p-1 text-sm"
                        :href="getEditURI(ranking.id)"
                        title="Edit"
                    >
                        <i class="fa fa-pencil"></i>
                    </a>
                    <a 
                        class="text-gray-500 hover:text-red-600 transition-colors p-1 text-sm"
                        @click="destroy(ranking.id)"
                        title="Delete"
                    >
                        <i class="fa fa-trash"></i>
                    </a>
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
                            this.buildFlash()
                                .overrideFlashStyles({
                                    'confirm-btn': 'btn-primary m-2 p-2',
                                    'cancel-btn': 'btn-secondary m-2 p-2'
                                })
                                .flash('Ranking Deleted', data.message, 'success');
                                
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

        computed: {
            rankingDisplayMode() {
                if (this.rankings && this.rankings.length > 1) {
                    return "grid grid-cols-1 md:grid-cols-2 gap-4 overflow-x-auto m-2 p-2";
                }

                return "grid grid-cols-1 gap-4 overflow-x-auto m-2 p-2"
            }
        }
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