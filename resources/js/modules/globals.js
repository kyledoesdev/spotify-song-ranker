export default {
    data() {
        return {
            'authid': document.getElementById('authid').value
        }
    },

    methods: {
        songEmbed(songId) {
            return "https://open.spotify.com/embed/track/" + songId
        },

        home() {
            window.location.href = '/home';
        },
    }
}