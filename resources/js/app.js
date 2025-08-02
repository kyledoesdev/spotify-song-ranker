import './bootstrap';
import './alerts';

import AutoAnimate from '@marcreichel/alpine-auto-animate';

document.addEventListener('alpine:init', () => {
    Alpine.plugin(AutoAnimate);
});