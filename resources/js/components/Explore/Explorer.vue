<template>
    <div class="flex justify-center p-2 m-2 md:pb-4" v-auto-animate>
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

    <!-- Pagiantion -->
    <ul class="flex justify-center space-x-2 p-4">
        <!-- Previous Page -->
        <li v-if="ranks.current_page > 1">
            <a 
                href="#"
                @click.prevent="pageRankings(ranks.prev_page_url)"
                class="px-3 py-2 border border-zinc-800 rounded-md hover:bg-gray-100"
            >
                &larr;
            </a>
        </li>

        <!-- Page Numbers -->
        <li v-for="page in getPages()" :key="page">
            <a 
                href="#"
                @click.prevent="pageRankings(`/explore/pages?page=${page}`)"
                class="px-3 py-2 border border-zinc-800 rounded-md"
                :class="page === ranks.current_page ? 'bg-purple-400 text-white' : 'hover:bg-gray-100'"
            >
                {{ page }}
            </a>
        </li>

        <!-- Next Page -->
        <li v-if="ranks.current_page < ranks.last_page">
            <a 
                href="#"
                @click.prevent="pageRankings(ranks.next_page_url)"
                class="px-3 py-2 border border-zinc-800 rounded-md hover:bg-gray-100"
            >
                &rarr;
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

            getPages() {
                if (!this.ranks.last_page)  {
                    return [1];
                }
        
                let pages = [];
                const current = this.ranks.current_page;
                const last = this.ranks.last_page;
                
                // Calculate the range to show up to 10 pages
                let start = Math.max(1, current - 4);
                let end = Math.min(start + 9, last);
                
                // Adjust start if we're near the end
                if (end === last) {
                    start = Math.max(1, end - 9);
                }
                
                for (let i = start; i <= end; i++) {
                    pages.push(i);
                }
                
                return pages;
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