<template>
    <div class="flex">
        <div class="mr-2">
            <i class="fa fa-solid fa-music text-xs md:text-base"></i>
        </div>
        <div class="p-0">
            <span class="text-xs md:text-base">{{ ranking.artist.artist_name }}</span>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Artist Name',

        props: ['ranking'],
    }
</script>