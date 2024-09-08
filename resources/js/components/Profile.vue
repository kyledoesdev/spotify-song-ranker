<template>
    <div class="bg-zinc-100 rounded-lg mt-8" v-auto-animate>
        <h5 class="text-xl md:text-4xl mt-4 md:mt-8 p-4">{{ this.display_name }} Rankings</h5>
        <div v-if="ranks.total > 0" class="bg-white overflow-x-auto" v-auto-animate>
            <div class="flex flex-col items-center m-2 p-2" v-if="ranks.total > 0" v-auto-animate>
                <div class="border border-zinc-800 rounded-lg mb-2" v-for="ranking in ranks.data" :key="ranking.id" v-auto-animate>
                    <exploreitem class="mb-4" :ranking="ranking" />

                    <div class="text-dark m-4" v-if="ranking.user_id == this.authid">
                        <a 
                            class="border border-zinc-800 bg-purple-400 rounded px-2 py-1 mx-2"
                            @click="show(ranking.id)"
                        >
                            <i class="fa fa-eye"></i>
                        </a>
                        <a 
                            class="border border-zinc-800 bg-green-300 rounded px-2 py-1 mx-2"
                            :href="getEditURI(ranking.id)"
                        >
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a class="border border-zinc-800 bg-red-400 rounded px-2 py-1 mx-2"
                            @click="destroy(ranking.id)"
                        >
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4" v-else>
            <span>{{ this.no_rankings_msg }}</span>
        </div>

        <div class="mt-4 mx-2" v-auto-animate>
            <ul class="pagination flex justify-end pb-4" v-auto-animate>
                <li v-if="ranks.prev_page_url" class="m-1">
                    <a 
                        class="border border-zinc-800 p-2 rounded bg-purple-400 hover:bg-purple-500 text-white" 
                        @click.prevent="pageRankings(ranks.prev_page_url)"
                        href="#"
                    >
                        &larr; Previous Page
                    </a>
                </li>
                <li v-if="ranks.next_page_url" class="m-1">
                    <a 
                        class="border border-zinc-800 p-2 rounded bg-purple-400 hover:bg-purple-500 text-white" 
                        @click.prevent="pageRankings(ranks.next_page_url)"
                        href="#"
                    >
                        Next Page &rarr;
                    </a>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Profile',

        data() {
            return {
                ranks: [],
                display_name: "",
                profile_name: "",
                no_rankings_msg: "You do not have any rankings. Go make one!"
            }      
        },

        methods: {
            show(rankingId) {
                window.location.href = '/rank/' + rankingId;
            },

            async destroy(rankingId) {
                let confirmed = await this.buildFlash()
                    .overrideFlashStyles({
                        'confirm-btn': 'border border-2 border-zinc-800 rounded-lg bg-red-400 hover:bg-red-500 text-zinc-800 m-2 p-2'
                    })
                    .check(
                        "Delete Ranking?",
                        "Are you sure you want to delete this ranking?",
                        "info",
                        "Delete"
                    );

                if (confirmed) {
                    axios.post('/rank/delete', {
                        'rankingId': rankingId
                    })
                    .then(response => {
                        const data = response.data;

                        if (data && data.message && data.rankings) {
                            this.flash('Ranking Deleted', data.message);
                            this.ranks = response.data.rankings;
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

            pageRankings(uri) {
                axios.get(uri, {
                        params: {
                            user: this.profile_name
                        }
                    })
                    .then(response => {
                        const data = response.data;

                        this.display_name = data.name;

                        if (data.success == false) {
                            this.no_rankings_msg = data.message;
                            return;
                        }

                        if (data.success == true && data.rankings) {
                            this.ranks = data.rankings;
                        }                    
                    })
                    .catch(error => {
                        this.flash('Error Fetching Rankings', `Could not get rankings for ${this.profile_name} at this time. Please try again later.`, 'error')
                        console.log(error);
                    });
            },

            getEditURI(rankingid) {
                return '/rank/' + rankingid + '/edit';
            },

            visibility(ranking) {
                return ranking.is_public == 0 ? 'No' : 'Yes';
            }
        },

        mounted() {
            this.profile_name = new URLSearchParams(window.location.search).get('user');

            this.pageRankings('/ranks/pages');
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