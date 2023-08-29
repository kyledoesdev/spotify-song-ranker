import './bootstrap';
import { createApp } from 'vue';

import Login from './components/Login.vue';
import SpotifySearch from './components/Spotify/Search.vue';
import ArtistResult from './components/Spotify/Artist.vue';

const app = createApp({
    components: {
        Login,
        SpotifySearch,
        ArtistResult
    }
});


app.mount('#app');
