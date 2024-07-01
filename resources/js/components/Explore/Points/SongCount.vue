<template>
    <div class="row">
        <div class="col-auto">
            <div class="row">
                <div class="col-auto mr-1">
                    <i class="fa fa-solid fa-hashtag"></i>
                </div>
                <div class="col p-0">
                    {{ ranking.songs_count }} songs ranked.
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Song Count',

        props: ['ranking'],
    }
</script>