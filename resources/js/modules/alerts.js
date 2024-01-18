import Swal from 'sweetalert2';

export default {
    data() {
        return {
            styles: {
                'confirm-btn': 'btn btn-sm btn-success m-2 p-2',
                'cancel-btn': 'btn btn-sm btn-danger m-2 p-2',
                'redirect-btn': 'btn btn-sm btn-success',
            }
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
                html: '<a class="' + this.styles['redirect-btn'] + '" href="' + redirectLink + '">' + buttonText + '</a>',
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

        flashDefaultSuccess() {
            this.flash("Success", "Saved Successfully!");
        },

        flashDefaultError() {
            this.flash("Error", "Sorry, we messed something up. Try again later!", "error");
        },

        swal() {
            return Swal.mixin({
                customClass: {
                    confirmButton: this.styles['confirm-btn'],
                    cancelButton: this.styles['cancel-btn'],
                },
                buttonsStyling: false
            });
        },

        overrideFlashStyles(newstyles) {
            for (const style in newstyles) {
                this.styles[style] = newstyles[style];
            }

            return this;
        },
    },
}