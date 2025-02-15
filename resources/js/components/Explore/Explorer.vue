<template>
    <div class="flex flex-col min-h-screen relative">
        <!-- Mobile Toggle Button -->
        <button 
            class="md:hidden fixed left-0 top-1/2 -translate-y-1/2 bg-white border border-zinc-800 p-2 rounded-r-md z-50"
            @click="toggleSidebar"
        >
            <i class="fa-solid fa-angle-right" :class="{ 'rotate-180': isSidebarOpen }"></i>
        </button>

        <div class="flex flex-1 py-4">
            <!-- Mobile Sidebar Overlay -->
            <div 
                v-if="isSidebarOpen" 
                class="md:hidden fixed inset-0 bg-black bg-opacity-50 z-40"
                @click="toggleSidebar"
            ></div>

            <!-- Filters Sidebar -->
            <div 
                class="fixed md:static md:block z-50 md:h-auto h-screen top-0"
                :class="[
                    isSidebarOpen ? 'left-0' : '-left-72',
                    'transition-all duration-300 ease-in-out'
                ]"
            >
                <div class="w-72 md:h-auto h-full border-r md:border md:rounded-lg border-zinc-800 bg-white overflow-y-auto">
                    <div class="p-4 border-b border-zinc-800">
                        <h3 class="text-lg text-center k-line font-semibold mb-3">Top Ranked Artists</h3>
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

                    <!-- Search Section -->
                    <div class="p-4">
                        <div class="flex flex-col space-y-2">
                            <div>
                                <input 
                                    class="w-full p-2 border border-zinc-800 rounded-md" 
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
                </div>
            </div>

            <!-- Centered Main Content -->
            <div class="w-full md:flex md:justify-center">
                <div class="max-w-3xl w-full px-4">
                    <div class="flex flex-col space-y-4" v-if="ranks.total > 0" v-auto-animate>
                        <div 
                            class="border bg-white border-zinc-800 hover:bg-gray-100 rounded-md cursor-pointer" 
                            v-for="ranking in ranks.data" 
                            :key="ranking.id" 
                            v-auto-animate
                        >
                            <exploreitem :ranking="ranking" />
                        </div>
                    </div>

                    <div class="flex justify-center" v-else>
                        <span>Loading Rankings...</span>
                    </div>
                </div>
            </div>

            <!-- Right Spacer for Balance -->
            <div class="hidden md:block w-72">
            </div>
        </div>

        <!-- Pagination -->
        <div class="w-full overflow-x-auto">
            <ul class="flex justify-center items-center space-x-2 py-3 min-w-max px-4">
                <!-- Previous Button -->
                <li>
                    <a 
                        href="#"
                        @click.prevent="ranks.current_page > 1 && pageRankings(ranks.prev_page_url)"
                        class="px-3 py-2 rounded-md text-zinc-800"
                        :class="ranks.current_page <= 1 ? 'cursor-not-allowed opacity-50' : ''"
                    >
                        <i class="fa fa-solid fa-arrow-left-long"></i>
                    </a>
                </li>

                <!-- First Page -->
                <li v-if="ranks.current_page > 3">
                    <a 
                        href="#"
                        @click.prevent="pageRankings('/explore/pages?page=1')"
                        class="px-3 py-2 text-zinc-800"
                    >
                        1
                    </a>
                </li>

                <!-- Ellipsis -->
                <li v-if="ranks.current_page > 3">
                    <span class="px-2">...</span>
                </li>

                <!-- Page Numbers -->
                <template v-for="page in getPages()" :key="page">
                    <li>
                        <a 
                            href="#"
                            @click.prevent="pageRankings(`/explore/pages?page=${page}`)"
                            class="px-3 py-2 text-zinc-800"
                            :class="page === ranks.current_page ? 'k-line' : ''"
                        >
                            {{ page }}
                        </a>
                    </li>
                </template>

                <!-- Ellipsis -->
                <li v-if="ranks.current_page < ranks.last_page - 2">
                    <span class="px-2">...</span>
                </li>

                <!-- Last Page -->
                <li v-if="ranks.current_page < ranks.last_page - 2">
                    <a 
                        href="#"
                        @click.prevent="pageRankings(`/explore/pages?page=${ranks.last_page}`)"
                        class="px-3 py-2 text-zinc-800"
                    >
                        {{ ranks.last_page }}
                    </a>
                </li>

                <!-- Next Button -->
                <li>
                    <a 
                        href="#"
                        @click.prevent="ranks.current_page < ranks.last_page && pageRankings(ranks.next_page_url)"
                        class="px-3 py-2 rounded-md text-zinc-800"
                        :class="ranks.current_page >= ranks.last_page ? 'cursor-not-allowed opacity-50' : ''"
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
                searchQuery: "",
                isSidebarOpen: false
            }
        },

        methods: {
            toggleSidebar() {
                this.isSidebarOpen = !this.isSidebarOpen;
            },

            pageRankings(uri = '/explore/pages', artist = null) {
                if (artist != null) {
                    this.searchQuery = "";
                    // Close sidebar after selection on mobile
                    this.isSidebarOpen = false;
                }

                axios.get(uri, {
                    params: {
                        search: this.searchQuery,
                        artist: artist
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
                this.isSidebarOpen = false;
                this.pageRankings()
            },

            getPages() {
                if (!this.ranks.last_page) return [1];
                
                const current = this.ranks.current_page;
                const last = this.ranks.last_page;
                const delta = 2; // Show 2 pages before and after current page
                
                let pages = [];
                const left = Math.max(current - delta, 1);
                const right = Math.min(current + delta, last);
                
                // Generate range of visible page numbers
                for (let i = left; i <= right; i++) {
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