<template>
    <div class="card">
        <div class="card-header">
            <div class="row d-flex">
                <div class="col justify-content-start">
                    <h5 class="mt-3">All Rankings</h5>
                </div>
                <div class="col d-flex justify-content-end m-2">
                    <button class="btn btn-primary border border-1 border-dark" @click="this.home()">
                        <i class="fa fa-house"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body" style="min-height: 600px;" v-auto-animate>
            <div v-if="ranks.total > 0">
                <table class="table table-responsive table-striped" v-auto-animate>
                    <thead>
                        <tr>
                            <th></th>
                            <!--<th>Artist</th>-->
                            <th>Name</th>
                            <th>Songs</th>
                            <th>Top Song</th>
                            <th>In Explore Feed?</th>
                            <th>Completed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody v-auto-animate>
                        <tr v-for="ranking in ranks.data" :key="ranking.id">
                            <td class="col-auto">
                                <img 
                                    :src="ranking.artist.artist_img"
                                    :alt="ranking.artist.artist_name"
                                    :title="ranking.artist.artist_name"
                                    width="88"
                                    height="88"
                                />
                            </td>   
                            <!-- <td>{{ ranking.artist.artist_name }}</td> -->
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
                                    <span>N/A (In Progress)</span>
                                </div>
                            </td>
                            <td>{{ this.visibility(ranking) }}</td>
                            <td>
                                <span v-if="ranking.is_ranked">{{ ranking.completed_at }}</span>
                                <span v-else>In Progress</span>
                            </td>
                            <td>
                                <div class="row">
                                    <div class="col mt-2">
                                        <div class="btn-group">
                                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="#" @click="show(ranking.id)">
                                                        View
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" :href="getEditURI(ranking.id)">
                                                        Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" @click="destroy(ranking.id)">
                                                        Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row d-flex jusitfy-content-center" v-else>
                <span>You do not have any rankings. Go make one!</span>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col d-flex justify-content-end">
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
    </div>
</template>
<script>
    export default {
        name: 'All Rankings',

        data() {
            return {
                ranks: []
            }      
        },

        methods: {
            show(rankingId) {
                window.location.href = '/rank/' + rankingId;
            },

            async destroy(rankingId) {
                let confirmed = await this.buildFlash()
                    .overrideFlashStyles({
                        'confirm-btn': 'btn btn-sm btn-danger m-2 p-2'
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
                            this.buildFlash()
                                .overrideFlashStyles({
                                    'confirm-btn': 'btn btn-sm btn-success m-2 p-2'
                                })
                                .flash('Ranking Deleted', data.message);
                            
                            this.ranks = response.data.rankings;
                        }
                    })
                    .catch(error => {
                        console.error(error);
                    });
                }
            },

            topSong(ranking) {
                return Object.values(ranking.songs).find(song => song.rank === 1)?.spotify_song_id;
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
            },

            visibility(ranking) {
                return ranking.is_public == 0 ? 'No' : 'Yes';
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