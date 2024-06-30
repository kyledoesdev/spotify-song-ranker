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
import Logo from './components/Spotify/Logo.vue';

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
app.component('spotify-logo', Logo);
app.component('explorer', Explorer);
app.component('exploreitem', ExploreItem);
app.component('explore-artist-name', ArtistName);
app.component('explore-top-song', TopSong);
app.component('explore-song-count', SongCount);
app.component('xplore-ranking-creator', Creator);
app.component('explore-ranking-completed-at', CompletedAt);

app.mount('#app');
