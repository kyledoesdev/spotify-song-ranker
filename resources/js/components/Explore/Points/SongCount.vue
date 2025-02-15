<template>
    <div class="flex">
        <div class="mr-1">
            <i class="fa fa-solid fa-hashtag text-xs md:text-base"></i>
        </div>
        <div class="p-0">
            <span class="text-xs md:text-base">{{ ranking.songs_count }} songs ranked.</span>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Song Count',

        props: ['ranking'],
    }
</script>