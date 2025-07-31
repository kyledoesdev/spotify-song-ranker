import Swal from 'sweetalert2';

class LivewireSweetAlert {
    constructor() {
        this.flash_styles = {
            'confirm-btn': 'btn-primary m-2 p-2 cursor-pointer shadow-md',
            'cancel-btn': 'btn-secondary m-2 p-2 cursor-pointer shadow-md',
            'redirect-btn': 'btn-primary m-2 p-2 cursor-pointer shadow-md',
        };

        this.setupGlobalMethods();
    }

    setupGlobalMethods() {
        window.confirm = this.confirm.bind(this);
        window.flash = this.flash.bind(this);
    }

    async confirm({ title, message, confirmText, entityId = null, styles = {}, action = null }) {
        const confirmStyles = {
            'confirm-btn': styles['confirm-btn'] || 'btn-primary m-2 p-2',
            'cancel-btn': styles['cancel-btn'] || 'btn-secondary m-2 p-2'
        };
    
        const result = await this.swal(confirmStyles).fire({
            title: title,
            text: message,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: 'Cancel',
            reverseButtons: true
        });
    
        if (result.isConfirmed && action) {
            const component = Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
            
            if (entityId) {
                await component.call(action, entityId);
            } else {
                await component.call(action);
            }
        }
    
        return result.isConfirmed;
    }

    flash({ title, message, icon = 'success' }) {
        return this.swal().fire({
            title: title,
            text: message,
            icon: icon
        });
    }

    flashWithRedirect({title, icon = 'success', redirectLink, buttonText = 'View here!'}) {
        return this.swal().fire({
            title: title,
            icon: icon,
            html: '<a class="' + this.flash_styles['redirect-btn'] + '" href="' + redirectLink + '">' + buttonText + '</a>',
            showConfirmButton: false
        });
    }

    swal(customStyles = null) {
        const styles = customStyles || this.flash_styles;
        return Swal.mixin({
            customClass: {
                confirmButton: styles['confirm-btn'],
                cancelButton: styles['cancel-btn'],
            },
            buttonsStyling: false
        });
    }

    overrideFlashStyles(newstyles) {
        for (const style in newstyles) {
            this.flash_styles[style] = newstyles[style];
        }
        return this;
    }
}

// Initialize when Livewire is ready
document.addEventListener('livewire:init', () => {
    new LivewireSweetAlert();
});

export default LivewireSweetAlert;