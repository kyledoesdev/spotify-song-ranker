<template>
    <div class="flex">
        <div class="mr-1">
            <i class="fa fa-regular fa-star text-xs md:text-base"></i>
        </div>
        <div class="p-0">
            <div v-if="ranking.is_ranked">
                <span class="text-xs md:text-base">{{ truncate(ranking.songs[0]?.title, 25) }}</span>
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