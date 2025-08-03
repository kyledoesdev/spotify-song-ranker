import './bootstrap';
import LivewireSweetAlert from './alerts';
import LivewireLoader from './loader';
import AutoAnimate from '@marcreichel/alpine-auto-animate';

document.addEventListener('livewire:init', () => {
    new LivewireLoader();
    new LivewireSweetAlert();
});

document.addEventListener('alpine:init', () => {
    Alpine.plugin(AutoAnimate);
});