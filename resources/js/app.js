import './bootstrap';
import { createApp } from 'vue';
import mitt from 'mitt';

import Globals from './modules/globals';

import Login from './components/Login.vue';
import Home from './components/Home.vue';
import SpotifySearch from './components/RankSetup/SpotifySearch.vue';
import RankingSetup from './components/RankSetup/RankingSetup.vue';
import SongListItem from './components/RankSetup/SongListItem.vue';
import RankMaker from './components/RankMaker.vue';
import HomeRankingsList from './components/Lists/HomeRankingsList.vue';
import AllRankingsList from './components/Lists/AllRankingsList.vue';

const app = createApp({});

app.config.globalProperties.emitter = mitt();

app.mixin(Globals);

app.component('login', Login);
app.component('home', Home);
app.component('spotifysearch', SpotifySearch);
app.component('rankingsetup', RankingSetup);
app.component('songlistitem', SongListItem);
app.component('rankmaker', RankMaker);
app.component('homerankingslist', HomeRankingsList);
app.component('allrankingslist', AllRankingsList);


app.mount('#app');
