<template>
    <div class="m-2 mb-2" v-auto-animate>
        <div class="flex" v-auto-animate>
            <div v-auto-animate>
                <img 
                    :src="cover" 
                    :alt="name"
                    class="w-12 h-12 sm:w-[72px] sm:h-[72px]" 
                />
            </div>
            <div class="flex flex-1">
                <div class="min-w-0 pt-1">
                    <h5 class="mx-2 mt-1 mb-0.5 text-xs sm:text-base break-words" :title="name">{{ name }}</h5>
                    <spotify-logo class="mx-2" :song="spotifyid" />
                </div>
                <div v-if="candelete">
                    <button type="button" class="btn-danger px-2 py-1 my-1" @click="removeSong()">
                        <i class="fa-solid text-zinc-800 fa-trash-can"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div v-if="spacer">
        <hr />
    </div>
</template>

<script>
    export default {
        name: 'Song List Item',

        props: ['id', 'spotifyid', 'name', 'cover', 'candelete', 'spacer'],

        methods: {
            removeSong() {
                this.emitter.emit('song-removed', this.id);
            }
        },
    }
</script>
<style>
    .truncate {
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 24vw;
    }
</style>