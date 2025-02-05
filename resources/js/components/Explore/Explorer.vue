<template>
    <div class="flex flex-col min-h-screen">
        <div class="flex flex-1">
            <!-- Left Sidebar -->
            <div class="hidden md:block w-64 p-4 border-zinc-800">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold mb-3">Top Ranked Artists</h3>
                    <ul class="space-y-2">
                        <li 
                            v-for="(artist, index) in artists"
                            :key="artist.id"
                            @click="pageRankings('/explore/pages', artist.id)"
                            class="p-2 hover:bg-gray-100 rounded-md cursor-pointer"
                        >
                            <div class="flex items-center space-x-2">
                                <div :class="artistRankingIcon(index + 1)">
                                    {{ index + 1 }}
                                </div>
                                <span>{{ artist.artist_name }} <span class="text-xs">({{ artist.artist_rankings_count }})</span></span>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="flex flex-col space-y-2">
                    <div>
                        <input 
                            class="w-full sm:flex-1 p-2 border border-zinc-800 rounded-md" 
                            type="text" 
                            placeholder="Search..." 
                            v-model="searchQuery"
                        />
                    </div>
                    <div class="flex justify-center space-x-2">
                        <button 
                            type="button" 
                            class="btn-primary px-2 py-1 text-white rounded-md" 
                            @click="pageRankings()" 
                        >
                            <i class="text-xl text-zinc-800 fa fa-magnifying-glass"></i>
                        </button>
                        <button 
                            type="button" 
                            class="btn-secondary px-2 py-1 text-white rounded-md" 
                            @click="reset"
                        >
                            <i class="text-xl text-zinc-800 fa-solid fa-rotate-left"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 my-4">
                <div class="flex flex-col items-center space-y-4 px-4 md:px-0" v-if="ranks.total > 0" v-auto-animate>
                    <div class="border border-zinc-800 w-full md:w-1/2 hover:bg-gray-100 rounded-md cursor-pointer" v-for="ranking in ranks.data" :key="ranking.id" v-auto-animate>
                        <exploreitem :ranking="ranking" />
                    </div>
                </div>

                <div class="flex justify-center" v-else>
                    <span>Loading Rankings...</span>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="border-t border-zinc-800 w-full">
            <ul class="flex justify-center items-center space-x-2 py-4">
                <!-- Previous Button -->
                <li>
                    <a 
                        href="#"
                        @click.prevent="ranks.current_page > 1 && pageRankings(ranks.prev_page_url)"
                        class="px-3 py-2 border border-zinc-800 rounded-md"
                        :class="ranks.current_page <= 1 ? 'bg-gray-100 cursor-not-allowed' : 'hover:bg-gray-100'"
                    >
                        <i class="fa fa-solid fa-arrow-left-long"></i>
                    </a>
                </li>

                <!-- Page Numbers -->
                <template v-for="page in getPages()" :key="page">
                    <li>
                        <a 
                            href="#"
                            @click.prevent="pageRankings(`/explore/pages?page=${page}`)"
                            class="px-3 py-2 border border-zinc-800 rounded-md"
                            :class="page === ranks.current_page ? 'bg-purple-400 text-white' : 'hover:bg-gray-100'"
                        >
                            {{ page }}
                        </a>
                    </li>
                </template>

                <!-- Next Button -->
                <li>
                    <a 
                        href="#"
                        @click.prevent="ranks.current_page < ranks.last_page && pageRankings(ranks.next_page_url)"
                        class="px-3 py-2 border border-zinc-800 rounded-md"
                        :class="ranks.current_page >= ranks.last_page ? 'bg-gray-100 cursor-not-allowed' : 'hover:bg-gray-100'"
                    >
                        <i class="fa fa-solid fa-arrow-right-long"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Explore',

        props: ['lists'],

        data() {
            return {
                ranks: [],
                artists: [],
                searchQuery: ""
            }
        },

        methods: {
            pageRankings(uri = '/explore/pages', value = null) {
                let search = value != null ? value : this.searchQuery;

                axios.get(uri, {
                    params: {
                        search: search
                    }
                })
                .then(response => {
                    const data = response.data;

                    if (data && data.rankings && data.rankings.data.length > 0) {
                        this.ranks = response.data.rankings;
                        this.artists = data.top_artists;
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
            },

            getPages() {
                if (!this.ranks.last_page) return [1];
                
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
                
                // Add the pages
                for (let i = start; i <= end; i++) {
                    pages.push(i);
                }
                
                return pages;
            },

            artistRankingIcon(index) {
                let color = 'bg-purple-200';

                if (index == 1) {
                    color = 'bg-amber-400'
                } else if (index == 2) {
                    color = 'bg-slate-300';
                } else if (index == 3) {
                    color = 'bg-yellow-700';
                }

                return `w-8 h-8 rounded-full flex items-center justify-center ${color}`;
            }
        },

        mounted() {
            this.pageRankings();
        }
    }
</script>