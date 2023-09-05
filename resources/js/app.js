import './bootstrap';
import { createApp } from 'vue';
import mitt from 'mitt';

import Login from './components/Login.vue';
import SpotifySearch from './components/RankSetup/SpotifySearch.vue';
import RankingSetup from './components/RankSetup/RankingSetup.vue';
import SongListItem from './components/RankSetup/SongListItem.vue';
import RankMaker from './components/RankMaker.vue';

const app = createApp({
    components: {
        Login,
        SpotifySearch,
        RankingSetup,
        SongListItem,
        RankMaker
    }
});

app.config.globalProperties.emitter = mitt();

app.mount('#app');
