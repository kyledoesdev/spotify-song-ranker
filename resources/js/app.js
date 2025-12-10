import './bootstrap';
import LivewireSweetAlert from './alerts';
import LivewireLoader from './loader';
import AutoAnimate from '@marcreichel/alpine-auto-animate';
import '../../vendor/spatie/laravel-comments-livewire/resources/dist/comments.js';

document.addEventListener('livewire:init', () => {
    new LivewireLoader();
    new LivewireSweetAlert();
});

document.addEventListener('alpine:init', () => {
    Alpine.plugin(AutoAnimate);
});