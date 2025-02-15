<template>
    <div class="flex">
        <div class="mr-1">
            <i class="fa fa-regular fa-user text-xs md:text-base"></i>
        </div>
        <div class="p-0">
            <a :href="getProfileURL">
                <span class="text-xs md:text-base">{{ ranking.user.name }} 
                    <i class="fa fa-arrow-up-right-from-square mx-1 text-blue-500 text-xs md:text-base"></i>
                </span>
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
                return '/profile/' + this.ranking.user.spotify_id;
            }
        }
    }
</script>