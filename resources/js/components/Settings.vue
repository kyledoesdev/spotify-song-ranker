<template>
    <div class="row mb-2">
        <div class="col m-2">
            <div class="form-group">
                <button class="btn btn-success" @click="download">Export your data</button>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: 'Settings',

        methods: {
            async download() {
                let confirmed = await this.buildFlash()
                    .check(
                        "Begin downloading your data?",
                        "Would you like to download all of your rankings? The export will be queued and emailed to you when completed.",
                        "info",
                        "Go",
                        "Cancel"
                    );

                if (confirmed) {
                    axios.get('/ranks/export')
                        .then(response => {
                            let data = response.data;

                            if (data && data.success) {
                                this.buildFlash()
                                    .flash(
                                        "Download Started!",
                                        data.message,
                                    )
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            this.buildFlash()
                                .flash(
                                    "Something went wrong?",
                                    "Something went wrong when trying to download your data. Please try again later!",
                                    "error"
                                );
                        })
                }
            }
        }
    }
</script>