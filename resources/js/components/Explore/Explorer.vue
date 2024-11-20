<template>
    <div class="flex justify-center p-2 mb-2 md:pb-4" v-auto-animate>
        <div class="flex flex-col sm:flex-row items-center w-full md:w-1/2 space-y-2 sm:space-y-0 sm:space-x-2">
            <input 
                class="w-full sm:flex-1 p-2 border border-zinc-800 rounded-md" 
                type="text" 
                placeholder="Search..." 
                v-model="searchQuery"
            />
            <div class="flex space-x-2">
                <button 
                    type="button" 
                    class="bg-purple-400 border border-zinc-800 px-4 py-2 text-white rounded-md" 
                    @click="pageRankings()" 
                >
                    <i class="text-xl text-zinc-800 fa fa-magnifying-glass"></i>
                </button>
                <button 
                    type="button" 
                    class="bg-green-300 border border-zinc-800 px-4 py-2 text-white rounded-md" 
                    @click="reset"
                >
                    <i class="text-xl text-zinc-800 fa-solid fa-rotate-left"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="flex flex-col items-center m-2 p-2" v-if="ranks.total > 0" v-auto-animate>
        <div class="border border-zinc-800 rounded-lg mb-2" v-for="ranking in ranks.data" :key="ranking.id" v-auto-animate>
            <exploreitem :ranking="ranking" />
        </div>
    </div>

    <div class="row d-flex jusitfy-content-center" v-else>
        <span>Loading Rankings...</span>
    </div>

    <ul class="pagination flex justify-end p-4">
        <li v-if="ranks.prev_page_url">
            <a 
                class="btn-primary m-2 p-2" 
                @click.prevent="pageRankings(ranks.prev_page_url)"
                href="#"
            >
                &larr; Previous Page
            </a>
        </li>
        <li v-if="ranks.next_page_url">
            <a 
                class="btn-primary m-2 p-2" 
                @click.prevent="pageRankings(ranks.next_page_url)"
                href="#"
            >
                Next Page &rarr;
            </a>
        </li>
    </ul>
</template>
<script>
    export default {
        name: 'Explore',

        props: ['lists'],

        data() {
            return {
                ranks: [],
                searchQuery: ""
            }
        },

        methods: {
            pageRankings(uri = '/explore/pages') {
                axios
                    .get(uri, {
                        params: {
                            search: this.searchQuery
                        }
                    })
                    .then(response => {
                        const data = response.data;

                        if (data && data.rankings && data.rankings.data.length > 0) {
                            this.ranks = response.data.rankings;
                        } else {
                            this.flash("No Rankings found", "No rankings found for search term: " + this.searchQuery, 'error');
                            this.reset();
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },

            reset() {
                this.searchQuery = "";
                this.pageRankings()
            }
        },

        mounted() {
           this.pageRankings('/explore/pages');
        }
    }
</script>
<style>
    .full-height {
        height: 60vh;
    }
</style>