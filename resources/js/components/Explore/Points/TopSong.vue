<template>
    <div class="flex">
        <div class="mr-1">
            <i class="fa fa-regular fa-star"></i>
        </div>
        <div class="p-0">
            <div v-if="ranking.is_ranked">
                {{ truncate(ranking.songs[0]?.title, 25) }}
            </div>
            <div v-else>
                N/A
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Top Song',
        
        props: ['ranking'],
    }
</script>