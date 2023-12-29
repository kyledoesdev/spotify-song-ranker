import './bootstrap';
import { createApp } from 'vue';
import mitt from 'mitt';
import { autoAnimatePlugin } from '@formkit/auto-animate/vue'

/* Modules */
import Globals from './modules/globals';
import Alerts from './modules/alerts';

/* Components */
import Welcome from './components/Welcome.vue';
import Home from './components/Home.vue';
import SpotifySearch from './components/RankSetup/SpotifySearch.vue';
import RankingSetup from './components/RankSetup/RankingSetup.vue';
import SongListItem from './components/RankSetup/SongListItem.vue';
import RankMaker from './components/RankMaker.vue';
import HomeRankingsList from './components/Lists/HomeRankingsList.vue';
import AllRankingsList from './components/Lists/AllRankingsList.vue';
import EditRanking from './components/RankSetup/EditRanking.vue';

const app = createApp({});

app.config.globalProperties.emitter = mitt();

//mixin modules
app.mixin(Globals);
app.mixin(Alerts);

app.use(autoAnimatePlugin);

app.component('welcome', Welcome);
app.component('home', Home);
app.component('spotifysearch', SpotifySearch);
app.component('rankingsetup', RankingSetup);
app.component('songlistitem', SongListItem);
app.component('rankmaker', RankMaker);
app.component('homerankingslist', HomeRankingsList);
app.component('allrankingslist', AllRankingsList);
app.component('editranking', EditRanking);


app.mount('#app');
