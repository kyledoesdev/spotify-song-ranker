<template>
    <div class="card">
        <div class="card-header">
            <div class="row m-2">
                <div class="col-sm-3">
                    <div class="input-group">
                        <input class="form-control" type="text" v-model="searchQuery" placeholder="Search..." />
                        <button type="button" class="btn btn-primary" @click="pageRankings()">
                            <i class="fa fa-magnifying-glass"></i>
                        </button>
                        <button type="button" class="btn btn-secondary" @click="reset">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex flex-column justify-content-between full-height" v-if="ranks.total > 0">
                <div class="row mt-1" v-auto-animate>
                    <div class="col-auto" v-for="ranking in ranks.data" :key="ranking.id">
                        <exploreitem :ranking="ranking" />
                    </div>
                </div>
            </div>
            <div class="row d-flex jusitfy-content-center" v-else>
                <span>There are no lists to explore... Well that's embarassing.</span>
            </div>
        </div>
        <div class="card-footer">
            <div class="row mt-5 mx-3" style="position: relative;">
                <div class="col d-flex justify-content-end" style="position: absolute; bottom: 0;">
                    <ul class="pagination">
                        <li v-if="ranks.prev_page_url">
                            <a class="btn btn-sm btn-primary border border-1 border-dark mx-2" 
                                @click.prevent="pageRankings(ranks.prev_page_url)" href="#"
                            >
                                &larr; Previous Page
                            </a>
                        </li>
                        <li v-if="ranks.next_page_url">
                            <a class="btn btn-sm btn-primary border border-1 border-dark mx-2" 
                                @click.prevent="pageRankings(ranks.next_page_url)" href="#"
                            >
                                Next Page &rarr;
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
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
                searchQuery: ""
            }
        },

        methods: {
            pageRankings(uri = '/explore/pages') {
                axios.get(uri, {
                        params: {
                            search: this.searchQuery
                        }
                    })
                    .then(response => {
                        this.ranks = response.data.rankings;
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