<template>
    <div class="m-2">
        <div class="row d-flex">
            <div class="col-auto" v-auto-animate>
                <img :src="cover"  width="72" height="72" :alt="name" />
            </div>
            <div class="col-auto">
                <h5 class="truncate mx-2 mt-2 mb-2">{{ name }}</h5>
                <a 
                    :href="trackLink" 
                    target="_blank"
                    style="border-bottom: 2px solid #06D6A0; padding-bottom: 5px; max-width: 205px; margin-left: 8px;"
                >
                    <p style="display: inline; color: #06D6A0;">
                        Listen on <img :src="asset('spotify-logo.png')" style="display: inline;">
                    </p>
                    <div style="display: inline-block; width: 5px;"></div>
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
            </div>
            <div class="col d-flex justify-content-end m-1" v-if="candelete">
                <div class="row">
                    <div class="col d-flex justify-content-end" style="max-height: 35px;">
                        <button type="button" class="btn btn-sm btn-danger pb-0 pt-0" @click="removeSong()">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
</template>

<script>
    export default {
        name: 'Song List Item',

        props: ['id', 'name', 'cover', 'candelete'],

        methods: {
            removeSong() {
                this.emitter.emit('song-removed', this.id);
            }
        },

        computed: {
            trackLink() {
                return "https://open.spotify.com/track/" + this.id;
            }
        }
    }
</script>
<style>
    .truncate {
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 500px; /* Adjust according to your requirement */
    }
</style>