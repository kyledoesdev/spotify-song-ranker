<template>
    <div class="m-2 mb-2" v-auto-animate>
        <div class="flex" v-auto-animate>
            <div v-auto-animate>
                <img 
                    :src="cover" 
                    :alt="name"
                    class="w-12 h-12 sm:w-[72px] sm:h-[72px] rounded-xl mr-4" 
                />
            </div>
            <div class="flex flex-1">
                <div class="min-w-0 pt-1 flex-1">
                    <h5 class="mx-2 mt-1 mb-0.5 text-xs sm:text-base break-words" :title="name">{{ name }}</h5>
                    <div class="flex items-center mx-2">
                        <spotify-logo :song="spotifyid" />
                        <button 
                            v-if="candelete"
                            type="button" 
                            class="text-gray-500 hover:text-red-600 transition-colors p-1 text-sm ml-2 cursor-pointer" 
                            @click="removeSong()"
                            title="Remove song"
                        >
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
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