<template>
    <div class="m-2">
        <img
            width="48"
            height="48"
            :src="this.cover" 
            :alt="this.name"
        >
        <span class="mx-2">{{ this.name }}</span>
    </div>
</template>

<script>
    export default {
        name: 'Song Result',

        props: ['id', 'name', 'cover'],
    }
</script>