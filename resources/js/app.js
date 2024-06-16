import './bootstrap';
import { createApp } from 'vue';
import mitt from 'mitt';
import { autoAnimatePlugin } from '@formkit/auto-animate/vue';

import Vapor from 'laravel-vapor';
Vapor.withBaseAssetUrl(import.meta.env.VITE_VAPOR_ASSET_URL);
window.Vapor = Vapor;

/* Modules */
import Globals from './modules/globals';
import Alerts from './modules/alerts';

/* Components */
import Welcome from './components/Welcome.vue';
import Home from './components/Home.vue';
import Settings from './components/Settings.vue';
import SpotifySearch from './components/RankSetup/SpotifySearch.vue';
import RankingSetup from './components/RankSetup/RankingSetup.vue';
import SongListItem from './components/RankSetup/SongListItem.vue';
import HomeRankingsList from './components/Lists/HomeRankingsList.vue';
import AllRankingsList from './components/Lists/AllRankingsList.vue';
import EditRanking from './components/RankSetup/EditRanking.vue';

const app = createApp({});

app.config.globalProperties.emitter = mitt();

//mixin modules
app.mixin(Globals);
app.mixin(Alerts);
app.mixin({
	methods: { asset: window.Vapor.asset }
});

app.use(autoAnimatePlugin);

app.component('welcome', Welcome);
app.component('home', Home);
app.component('settings', Settings);
app.component('spotifysearch', SpotifySearch);
app.component('rankingsetup', RankingSetup);
app.component('songlistitem', SongListItem);
app.component('homerankingslist', HomeRankingsList);
app.component('allrankingslist', AllRankingsList);
app.component('editranking', EditRanking);


app.mount('#app');
