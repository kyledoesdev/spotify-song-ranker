<template>
    <div class="m-2">
        <div class="row">
            <div class="col">
                <img 
                    :src="cover" 
                    width="128" 
                    height="128" 
                    style="cursor: pointer;" 
                    @click="loadSongs"
                    :alt="this.name"
                >
            </div>
        </div>
        <div class="row">
            <div class="col d-flex" style="text-align: pre;">
                {{ this.name }}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Artist Result',

        props: ['id', 'name', 'images'],

        methods: {
            loadSongs() {
                axios.get('/spotify/artist_songs', {
                    params: {
                        'id': this.id
                    }
                })
                .then(response => {
                    for (const key in response.data.songs) {
                        console.log(key);
                    }

                    console.log(response.data.songs.length);
                })
                .catch(error => {

                })
            }
        },

        computed: {
            cover() {
                return this.images[0] && this.images[0]['url'] 
                    ? this.images[0]['url'] 
                    : "";
            }
        },
    }
</script>