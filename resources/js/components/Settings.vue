<template>
    <div class="row mb-2">
        <div class="col m-2">
            <div class="form-group">
                <button class="btn btn-success mx-2" @click="download">Export your data</button>
                <button class="btn btn-danger mx-2" @click="destroy">Delete your account</button>
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
            },

            async destroy() {
                let confirmed = await this.buildFlash()
                    .check(
                        "Delete your account?",
                        "Are you sure you want to delete your account? By deleting your account, we will email you one last email with your ranking records & then delete them & your account. Are you sure you want to go?",
                        "info",
                        "It's time for me to go.",
                        "I changed my mind!"
                    );

                if (confirmed) {
                    axios.post('/settings/destroy', {
                            user_id: this.authid
                        })
                        .then(response => {
                            let data = response.data;

                            if (data && data.success) {
                                window.location.href = data.redirect;
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            this.buildFlash()
                                .flash(
                                    "Something went wrong?",
                                    "Something went wrong when trying to delete your account. Please try again later!",
                                    "error"
                                );
                        });
                }
            }
        }
    }
</script>