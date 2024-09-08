<template>
    <div class="flex">
        <div class="mr-2">
            <i class="fa fa-solid fa-music"></i>
        </div>
        <div class="p-0">
            {{ ranking.artist.artist_name }}
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Artist Name',

        props: ['ranking'],
    }
</script>