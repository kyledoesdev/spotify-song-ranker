import './bootstrap';
import { createApp } from 'vue';
import mitt from 'mitt';

import Login from './components/Login.vue';
import Home from './components/Home.vue';
import SpotifySearch from './components/RankSetup/SpotifySearch.vue';
import RankingSetup from './components/RankSetup/RankingSetup.vue';
import SongListItem from './components/RankSetup/SongListItem.vue';
import RankMaker from './components/RankMaker.vue';
import HomeSongList from './components/Lists/HomeSongList.vue';

const app = createApp({
    components: {
        Login,
        Home,
        SpotifySearch,
        RankingSetup,
        SongListItem,
        RankMaker,
        HomeSongList
    }
});

app.config.globalProperties.emitter = mitt();

app.mount('#app');
