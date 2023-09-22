<template>
    <div class="row">
        <div :class="colBox">
            <div class="card" style="min-height: 600px">
                <div class="card-body">
                    <spotifysearch />
                </div>
            </div>
        </div>
        <homerankingslist :lists="this.lists"></homerankingslist>
    </div>
</template>
<script>
    export default {
        name: 'Home',

        props: ['lists'],

        data() {
            return {
                colBox: 'col-md-10 p-1'
            }
        },

        created() {
            this.emitter.on('show-song-list', (data) => {
                this.colBox = 'col-md-10 p-1';
            });

            this.emitter.on('song-list-hidden', (data) => {
                this.colBox = 'col-md-12 p-1'
            });
        }
    }
</script>