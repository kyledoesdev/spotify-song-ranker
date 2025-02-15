<template>
    <div class="flex">
        <div class="mr-2">
            <i class="fa fa-regular fa-clock text-xs md:text-base"></i>
        </div>
        <div class="p-0">
            <span class="text-xs md:text-base">{{ ranking.completed_at }}</span>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Ranking Completed At',

        props: ['ranking'],
    }
</script>