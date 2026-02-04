import Swal from 'sweetalert2';

export default class LivewireSweetAlert {
    constructor() {
        this.flash_styles = {
            'confirm-btn': 'btn-primary m-2 p-2 cursor-pointer shadow-md',
            'cancel-btn': 'btn-secondary m-2 p-2 cursor-pointer shadow-md',
            'redirect-btn': 'btn-primary m-2 p-2 cursor-pointer shadow-md',
        };

        window.confirm = this.confirm.bind(this);
        window.flash = this.flash.bind(this);
    }

    async confirm({ title, message, confirmText, componentId = null, entityId = null, action = null, styles = {}}) {
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
            const component = componentId 
                ? Livewire.find(componentId)
                : Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
            
            if (entityId != null) {
                await component.call(action, entityId);
            } else {
                await component.call(action);
            }
        }
    
        return result.isConfirmed;
    }

    flash({ title, message, submessage = null, icon = 'success' }) {
        const html = [
            message ? `<p>${message}</p>` : '',
            submessage ? `<p class="text-sm text-gray-500 mt-2 whitespace-pre-line">${submessage}</p>` : '',
        ].filter(Boolean).join('');

        return this.swal().fire({
            title,
            html,
            icon
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
                htmlContainer: 'max-h-[500px] overflow-y-auto',
            },
            buttonsStyling: false
        });
    }
}