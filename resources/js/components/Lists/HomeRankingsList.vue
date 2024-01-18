<template>
    <div class="card mt-1">
        <div class="card-body mb-2" style="min-height: 100%; overflow-y: auto;">
            <div class="row">
                <div class="col">
                    <h5 class="mt-2">Your recent lists.</h5>
                </div>
            </div>
            <hr />
            <div v-for="list in lists" :key="list.id">
                <div class="row">
                    <a :href="getListRoute(list.id)">
                        <h4 class="mb-0 mt-1">{{ list.name }}</h4>
                    </a>
                    <small class="text-muted mb-1">{{ list.artist.artist_name }} - {{ list.songs_count }} songs.</small>
                </div>
            </div>
            <hr class="mt-2">
            <div class="row d-flex justify-content-center">
                <div class="col-auto">
                    <h5 class="mt-1 mb-1" style="cursor: pointer;" @click="viewAll()">View All</h5>
                    <hr class="m-0" />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Home Rankings List',

        props: ['lists'],

        methods: {
            getListRoute(id) {
                return '/rank/' + id;
            },

            viewAll() {
                window.location.href = '/ranks';
            }
        },
    }
</script>
<style>
    a {
        text-decoration: none;
        cursor: pointer;
    }
</style>