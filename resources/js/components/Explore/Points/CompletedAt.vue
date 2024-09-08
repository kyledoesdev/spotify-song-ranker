<template>
    <div class="flex">
        <div class="mr-2">
            <i class="fa fa-regular fa-clock"></i>
        </div>
        <div class="p-0">
            {{ ranking.completed_at }}
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Ranking Completed At',

        props: ['ranking'],
    }
</script>