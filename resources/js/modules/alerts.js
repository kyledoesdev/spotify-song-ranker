import Swal from 'sweetalert2';

const default_flash_styles = {
    'confirm-btn': 'btn-primary',
    'cancel-btn': 'btn-secondary',
    'redirect-btn': 'btn-primary',
};

export default {
    data() {
        return {
            flash_styles: default_flash_styles,
        };
    },

    methods: {
        flash(title, text, icon = 'success') {
            return this.swal().fire({
                title: title, 
                text: text, 
                icon: icon
            });
        },

        flashWithRedirect(title, icon = 'success', redirectLink, buttonText = 'View here!') {
            return this.swal().fire({
                title: title, 
                icon: icon,
                html: '<a class="' + this.flash_styles['redirect-btn'] + '" href="' + redirectLink + '">' + buttonText + '</a>',
                showConfirmButton: false
            });
        },

        check(title, message = null, icon = 'info', confirmButtonText = 'Confirm', cancelButtonText = 'Cancel') {
            return this.swal().fire({
                title: title,
                text: message,
                icon: icon,
                showCancelButton: true,
                confirmButtonText: confirmButtonText,
                cancelButtonText: cancelButtonText,
                reverseButtons: true
            }).then((result) => {
                return result.isConfirmed;
            });
        },

        swal() {
            return Swal.mixin({
                customClass: {
                    confirmButton: this.flash_styles['confirm-btn'],
                    cancelButton: this.flash_styles['cancel-btn'],
                },
                buttonsStyling: false
            });
        },

        overrideFlashStyles(newstyles) {
            for (const style in newstyles) {
                this.flash_styles[style] = newstyles[style];
            }

            return this;
        },

        buildFlash() {
            return this;
        }
    },
}