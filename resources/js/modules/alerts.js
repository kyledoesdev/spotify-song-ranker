import Swal from 'sweetalert2';

export default {
    methods: {
        flash(title, text, icon = 'success') {
            return this.swal().fire({
                title: title, 
                text: text, 
                icon: icon
            });
        },

        flashWithRedirect(title, icon = 'success', redirectLink, buttonText = 'View here!', buttonClass = 'btn btn-success') {
            return this.swal().fire({
                title: title, 
                icon: icon,
                html: '<a class="'+buttonClass+'" href="'+redirectLink+'">'+buttonText+'</a>',
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
                    confirmButton: 'btn btn-success p-2 m-2',
                    cancelButton: 'btn btn-danger p-2 m-2',
                },
                buttonsStyling: false
            });
        }
    },
}