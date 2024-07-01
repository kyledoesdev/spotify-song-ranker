<template>
    <div class="row">
        <div class="col-auto">
            <div class="row">
                <div class="col-auto mr-1">
                    <i class="fa fa-regular fa-clock"></i>
                </div>
                <div class="col p-0">
                    {{ ranking.completed_at }}
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Ranking Completed At',

        props: ['ranking'],
    }
</script>