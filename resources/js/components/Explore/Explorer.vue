<template>
    <div class="d-flex flex-column justify-content-between full-height">
        <div class="row mt-1" v-auto-animate>
            <div class="col-auto" v-for="ranking in ranks.data" :key="ranking.id">
                <exploreitem :ranking="ranking" />
            </div>
        </div>
        <div class="row mt-5 mx-3" style="position: relative;">
            <div class="col d-flex justify-content-end" style="position: absolute; bottom: 0;">
                <ul class="pagination">
                    <li v-if="ranks.prev_page_url">
                        <a class="btn btn-sm btn-primary border border-1 border-dark" 
                            @click.prevent="pageRankings(ranks.prev_page_url)" href="#"
                        >
                            &larr; Previous Page
                        </a>
                    </li>
                    <li v-if="ranks.next_page_url">
                        <a class="btn btn-sm btn-primary border border-1 border-dark" 
                            @click.prevent="pageRankings(ranks.next_page_url)" href="#"
                        >
                            Next Page &rarr;
                        </a>
                    </li>
                </ul>
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
                ranks: []
            }
        },

        methods: {
            pageRankings(uri) {
                axios.get(uri)
                    .then(response => {
                        this.ranks = response.data.rankings;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
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