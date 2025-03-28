<!-- Stats.vue -->
<template>
    <div class="w-full mt-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 w-full">
            <div class="bg-white rounded-lg text-center shadow-md p-4 m-4">
                <span class="flex justify-center">
                    Join over {{ users }} + users across the world!
                </span>
            </div>

            <div class="bg-white rounded-lg shadow-md p-4 m-4">
                <span class="flex justify-center text-center">
                    Explore over {{ rankings }} + rankings of {{ artistcount }} + different artists!
                </span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Welcome Page Stats',
            
        props: ['users', 'rankings', 'artistcount'],
    }
</script>