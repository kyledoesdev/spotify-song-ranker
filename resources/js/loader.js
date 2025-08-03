export default class LivewireLoader {
    constructor() {
        this.loader = null;
        this.isVisible = false;
        
        window.showLoader = this.show.bind(this);
        window.hideLoader = this.hide.bind(this);
        
        this.createLoader();
    }

    createLoader() {
        this.loader = document.createElement('div');
        this.loader.id = 'livewire-loader';
        this.loader.className = 'livewire-loader';

        const notesContainer = document.createElement('div');
        notesContainer.className = 'livewire-loader__notes';

        for (let i = 0; i < 3; i++) {
            const note = document.createElement('div');
            note.className = `livewire-loader__note livewire-loader__note--${i + 1}`;
            note.innerHTML = '♪';
            notesContainer.appendChild(note);
        }

        this.loader.appendChild(notesContainer);
        document.body.appendChild(this.loader);
    }

    show() {
        if (!this.isVisible) {
            this.loader.classList.add('show');
            this.isVisible = true;
        }
    }

    hide() {
        if (this.isVisible) {
            this.loader.classList.remove('show');
            this.isVisible = false;
        }
    }
}