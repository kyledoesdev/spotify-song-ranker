<template>
    <div class="row">
        <div class="col-auto">
            <div class="row">
                <div class="col-auto mr-1">
                    <i class="fa fa-solid fa-music"></i>
                </div>
                <div class="col p-0">
                    {{ ranking.artist.artist_name }}
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Artist Name',

        props: ['ranking'],
    }
</script>