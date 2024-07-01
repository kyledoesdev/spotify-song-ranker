<template>
    <div class="row">
        <div class="col-auto">
            <div class="row">
                <div class="col-auto mr-1">
                    <i class="fa fa-regular fa-star"></i>
                </div>
                <div class="col p-0">
                    {{ truncate(ranking.songs[0].title, 25) }}
                </div>
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