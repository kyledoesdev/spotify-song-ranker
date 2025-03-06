<template>
    <div class="border border-zinc-800 bg-white rounded-lg mt-4 p-2">
        <div class="flex justify-between" @click="toggle">
            <h5 class="mt-2">Please consider answering our discovery survey. (Open me!)</h5>
            <div class="flex justify-end">
                <button class="p-2">
                    <i :class="icon"></i>
                </button>
            </div>
        </div>
        
        <div :class="container">
            <iframe
                src="https://docs.google.com/forms/d/e/1FAIpQLSerhONXccKv0QNAr3I2Fvhw3OxOFxggNOAwUOPo86Vu9n6rDA/viewform?embedded=true"
                width="640"
                height="951"
                frameborder="0"
                marginheight="0"
                marginwidth="0"
            >
                Loading…
            </iframe>
        </div>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                open: false
            }
        },

        methods: {
            toggle() {
                this.open = ! this.open;
            }
        },

        computed: {
            icon() {
                return this.open ? 'fa fa-solid fa-xmark' : 'fa fa-solid fa-chevron-down';
            },

            container() {
                return this.open ? 'flex justify-center' : 'hidden';
            }
        }  
    }
</script>