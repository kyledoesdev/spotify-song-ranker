<template>
    <div class="card">
        <div class="card-header">
            <div class="row d-flex">
                <div class="col justify-content-start">
                    <h1 class="mt-2">All Rankings</h1>
                </div>
                <div class="col d-flex justify-content-end m-2">
                    <button class="btn btn-primary border border-1 border-dark" @click="this.home()">
                        <i class="fa fa-house"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-responsive table-striped" style="overflow-x: auto;">
                <thead>
                    <tr>
                        <th></th>
                        <th>Artist</th>
                        <th>Ranking Name</th>
                        <th>Song Count</th>
                        <th>Top Song</th>
                        <th>Completed</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="ranking in ranks.data" :key="ranking.id">
                        <td class="col-auto">
                            <img :src="ranking.artist.artist_img" :alt="ranking.artist.artist_name" style="max-width: 75px; max-height: 75px;">
                        </td>
                        <td>{{ ranking.artist.artist_name }}</td>
                        <td>{{ ranking.name }}</td>
                        <td>{{ ranking.songs_count }}</td>
                        <td>
                            <div v-if="ranking.is_ranked">
                                <iframe 
                                    :src="this.songEmbed(topSong(ranking))" 
                                    width="300" 
                                    height="128" 
                                    frameborder="0" 
                                    allowtransparency="true" 
                                    allow="encrypted-media"
                                    loading="lazy"
                                    style="max-height: 80px;"
                                >
                                </iframe>
                            </div>
                            <div v-else>
                                <span>N/A</span>
                            </div>
                        </td>
                        <td>
                            <span v-if="ranking.is_ranked">{{ ranking.updated_at }}</span>
                            <span v-else>In Progress</span>
                        </td>
                        <td>
                            <div class="row">
                                <div class="col mt-2">
                                    <button class="btn btn-sm btn-dark border border-2 border-dark m-1 text-white" @click="show(ranking.id)">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    <a 
                                        class="btn btn-sm btn-secondary border border-2 border-dark m-1 text-white" 
                                        v-if="!ranking.is_ranked" 
                                        :href="getEditURI(ranking.id)"
                                    >
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>

                                    <button class="btn btn-sm btn-danger border border-2 border-dark m-1"  @click="destroy(ranking.id)">
                                        <i class="fa fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col d-flex justify-content-end">
                    <ul class="pagination">
                        <li v-if="ranks.prev_page_url">
                            <a class="btn btn-sm btn-primary border border-1 border-dark" @click.prevent="pageRankings(ranks.prev_page_url)" href="#">&larr; Previous Page</a>
                        </li>
                        <li v-if="ranks.next_page_url">
                            <a class="btn btn-sm btn-primary border border-1 border-dark" @click.prevent="pageRankings(ranks.next_page_url)" href="#">Next Page &rarr;</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import EditRanking from '../RankSetup/EditRanking.vue';

    export default {
        name: 'All Rankings',

        components: {
            EditRanking,
        },

        data() {
            return {
                ranks: []
            }      
        },

        methods: {
            show(rankingId) {
                window.location.href = '/rank/' + rankingId;
            },

            destroy(rankingId) {
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
            },

            topSong(ranking) {
                for (const song in ranking.songs) {
                    if (ranking.songs[song].rank == 1) {
                        return ranking.songs[song].spotify_song_id;
                    }
                }
            },

            pageRankings(uri) {
                axios.get(uri)
                    .then(response => {
                        this.ranks = response.data.rankings;
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },

            getEditURI(rankingid) {
                return '/rank/' + rankingid + '/edit';
            }
        },

        mounted() {
           this.pageRankings('/ranks/pages');
        }
    }
</script>
<style>
    td {
        text-overflow: ellipsis;
    }

    th, td {
        text-align: center;
    }

    .card-body {
        overflow: auto;
    }
</style>