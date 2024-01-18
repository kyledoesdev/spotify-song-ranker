<template>
    <div class="row">
        <div class="col-lg-9">
            <div class="card" style="min-height: 600px">
                <div class="card-body">
                    <spotifysearch />
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <homerankingslist :lists="this.lists"></homerankingslist>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Home',

        props: ['lists'],

        data() {
            return {
                colBox: ' p-1'
            }
        },
    }
</script>