<template>
    <div class="row">
        <div class="col-auto">
            <div class="row">
                <div class="col-auto mr-1">
                    <i class="fa fa-regular fa-user"></i>
                </div>
                <div class="col p-0">
                    {{ ranking.user.name }}
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Ranking Creator Name',
        
        props: ['ranking'],
    }
</script>