<template>
    <div class="card">
        <div class="card-header">
            <div class="row d-flex">
                <div class="col justify-content-start">
                    <h1 class="mt-2">Rankings</h1>
                </div>
                <div class="col d-flex justify-content-end m-2">
                    <button class="btn btn-primary border border-1 border-dark" @click="home()">
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
                    <tr v-for="ranking in rankings" :key="ranking.id">
                        <td class="col-auto">
                            <img :src="ranking.artist.artist_img" :alt="ranking.artist.artist_name" style="max-width: 75px; max-height: 75px;">
                        </td>
                        <td>{{ ranking.artist.artist_name }}</td>
                        <td>{{ ranking.name }}</td>
                        <td>{{ ranking.songs_count }}</td>
                        <td>
                            <div v-if="ranking.is_ranked">
                                <iframe 
                                    :src="songEmbed(topSong(ranking))" 
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
                            {{ ranking.updated_at }}
                        </td>
                        <td>
                            <div class="row">
                                <div class="col mt-2">
                                    <div class="btn-group border border-dark border-1">
                                        <button class="btn btn-sm btn-primary" v-if="ranking.is_ranked || ranking.user_id === this.authid" @click="show(ranking.id)">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-secondary" v-if="ranking.user_id === this.authid">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" v-if="ranking.user_id === this.authid">
                                            <i class="fa fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'All Rankings',

        props: ['rankings', 'authid'],

        methods: {
            home() {
                window.location.href = '/home';
            },

            show(rankingId) {
                window.location.href = '/rank/' + rankingId;
            },

            topSong(ranking) {
                for (const song in ranking.songs) {
                    if (ranking.songs[song].rank == 1) {
                        return ranking.songs[song].spotify_song_id;
                    }
                }
            },

            songEmbed(songId) {
                return "https://open.spotify.com/embed/track/" + songId
            }
        },
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