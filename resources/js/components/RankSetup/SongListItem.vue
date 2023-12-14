<template>
    <div class="m-2">
        <div class="row d-flex">
            <div class="col-auto" v-auto-animate>
                <img :src="this.cover"  width="48" height="48" :alt="this.name" />
                <span class="mx-2">{{ this.name }}</span>
            </div>
            <div class="col d-flex justify-content-end m-1" v-if="this.candelete">
                <button type="button" class="btn btn-sm btn-danger pb-0 pt-0" @click="removeSong()">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Song List Item',

        props: ['id', 'name', 'cover', 'candelete'],

        methods: {
            removeSong() {
                this.emitter.emit('song-removed', this.id);
            }
        }
    }
</script>