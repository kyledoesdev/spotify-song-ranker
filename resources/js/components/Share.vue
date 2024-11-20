<template>
    <a class="btn-primary p-2 m-2" @click="share" title="Share this list with your friends!">
        <i class="fa fa-solid fa-share-from-square"></i>
    </a>
</template>
<script>
    export default {
        methods: {
            share() {
                navigator.clipboard.writeText(window.location.href)
                    .then(() => {
                        this.flash('Success', 'Copied to clipboard!');
                    })
                    .catch(error => {
                        this.flash("Something went wrong.", 'Could not copy to your clipbaord, try copy & pasting the URL manually.', 'error');
                    });
            }
        }
    }
</script>
<style>
    a {
        cursor: pointer;
    }
</style>