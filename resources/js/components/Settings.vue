<template>
    <div class="grid grid-cols-1 md:grid-cols-2 mt-4 bg-white border border-zinc-800 p-4 rounded-lg">
        <div>
            <h5 class="md:text-3xl">Email Preferences</h5>
            <label>Ranking Reminder Emails</label>
            <select class="border border-zinc-800 rounded-sm p-1 mx-1 mt-1" v-model="has_reminder_emails" @change="update('recieve_reminder_emails', this.has_reminder_emails)">
                <option :value="true" :selected="has_reminder_emails">Yes</option>
                <option :value="false" :selected="!has_reminder_emails">No</option>
            </select>
        </div>
        <div>
            <h5 class="md:text-3xl">Other Settings</h5>
            <button class="btn-primary m-2 p-2" @click="download">Export your data</button>
            <button class="btn-danger m-2 p-2" @click="destroy">Delete your account</button>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Settings',

        props: ['preferences'],

        data() {
            return {
                has_reminder_emails: this.preferences.recieve_reminder_emails
            }
        },

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
                    axios.get('/ranking-download')
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
            },

            update(name, value) {
                axios.post('/settings/update', {
                    user_id: this.authid,
                    setting_name: name,
                    setting_value: value
                })
                .then(response => {
                    const data = response.data;

                    if (data && data.success) {
                        this.flash("Preference Updated", "Succesfully updated");
                    }
                })
                .catch(error => {
                    this.flash("Something went wrong.", `We could not update this setting at this time. ${error.response.data.message}`, 'error');
                    console.error(error);
                });
            }
        }
    }
</script>