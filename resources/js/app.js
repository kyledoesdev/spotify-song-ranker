import './bootstrap';
import './alerts';

import AutoAnimate from '@marcreichel/alpine-auto-animate';

document.addEventListener('alpine:init', () => {
    Alpine.plugin(AutoAnimate);
});

import mitt from 'mitt';
import { createApp } from 'vue';
import { autoAnimatePlugin } from '@formkit/auto-animate/vue';

/* Modules */
import Globals from './modules/globals';
import Alerts from './modules/alerts';

/* Components */
import EditRanking from './components/RankSetup/EditRanking.vue';
import Logo from './components/Spotify/Logo.vue';
import Share from './components/Share.vue';

import Explorer from './components/Explore/Explorer.vue';
import ExploreItem from './components/Explore/ExploreItem.vue';
import ArtistName from './components/Explore/Points/ArtistName.vue';
import CompletedAt from './components/Explore/Points/CompletedAt.vue';
import Creator from './components/Explore/Points/Creator.vue';
import SongCount from './components/Explore/Points/SongCount.vue';
import TopSong from './components/Explore/Points/TopSong.vue';

const app = createApp({});

app.config.globalProperties.emitter = mitt();

//mixin modules
app.mixin(Globals);
app.mixin(Alerts);

app.use(autoAnimatePlugin);

app.component('editranking', EditRanking);
app.component('spotify-logo', Logo);
app.component('share', Share);
app.component('explorer', Explorer);
app.component('exploreitem', ExploreItem);
app.component('explore-artist-name', ArtistName);
app.component('explore-top-song', TopSong);
app.component('explore-song-count', SongCount);
app.component('explore-ranking-creator', Creator);
app.component('explore-ranking-completed-at', CompletedAt);

app.mount('#app');