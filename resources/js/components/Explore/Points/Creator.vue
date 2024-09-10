<template>
    <div class="flex">
        <div class="mr-1">
            <i class="fa fa-regular fa-user"></i>
        </div>
        <div class="p-0">
            <a :href="getProfileURL">
                {{ ranking.user.name }} <i class="fa fa-arrow-up-right-from-square mx-2 text-blue-500"></i>
            </a>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Ranking Creator Name',
        
        props: ['ranking'],

        computed: {
            getProfileURL() {
                return '/profile?user=' + this.ranking.user.spotify_id;
            }
        }
    }
</script>