<template>
    <div class="flex">
        <div class="mr-1">
            <i class="fa fa-solid fa-hashtag"></i>
        </div>
        <div class="p-0">
            {{ ranking.songs_count }} songs ranked.
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Song Count',

        props: ['ranking'],
    }
</script>