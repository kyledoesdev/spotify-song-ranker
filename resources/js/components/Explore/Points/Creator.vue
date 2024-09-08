<template>
    <div class="flex">
        <div class="mr-1">
            <i class="fa fa-regular fa-user"></i>
        </div>
        <div class="p-0">
            {{ ranking.user.name }}
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Ranking Creator Name',
        
        props: ['ranking'],
    }
</script>