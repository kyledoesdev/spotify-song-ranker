import './bootstrap';
import { createApp } from 'vue';
import Alpine from 'alpinejs';
import mitt from 'mitt';
import { autoAnimatePlugin } from '@formkit/auto-animate/vue';

/* Modules */
import Globals from './modules/globals';
import Alerts from './modules/alerts';

/* Components */
import Welcome from './components/Welcome.vue';
import About from './components/About.vue';
import Settings from './components/Settings.vue';
import SpotifySearch from './components/RankSetup/SpotifySearch.vue';
import RankingSetup from './components/RankSetup/RankingSetup.vue';
import SongListItem from './components/RankSetup/SongListItem.vue';
import Profile from './components/Profile.vue';
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
import Survey from './components/Survey.vue';

const app = createApp({});

app.config.globalProperties.emitter = mitt();

//mixin modules
app.mixin(Globals);
app.mixin(Alerts);

app.use(autoAnimatePlugin);

app.component('welcome', Welcome);
app.component('about', About);
app.component('settings', Settings);
app.component('spotifysearch', SpotifySearch);
app.component('rankingsetup', RankingSetup);
app.component('songlistitem', SongListItem);
app.component('profile', Profile);
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
app.component('survey', Survey);

app.mount('#app');

window.Alpine = Alpine
Alpine.start()