import './bootstrap';
import { createApp } from 'vue';
import mitt from 'mitt';

import Login from './components/Login.vue';
import SpotifySearch from './components/Spotify/Search.vue';
import ArtistResult from './components/Spotify/Artist.vue';
import SongResult from './components/Spotify/Song.vue';

const app = createApp({
    components: {
        Login,
        SpotifySearch,
        ArtistResult,
        SongResult
    }
});

app.config.globalProperties.emitter = mitt();

app.mount('#app');
