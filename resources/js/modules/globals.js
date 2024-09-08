export default {
    data() {
        return {
            'authid': document.getElementById('authid').value,
            'authname': document.getElementById('authname').value,
            'routename': document.getElementById('routename').value,
        }
    },

    methods: {
        songEmbed(songId) {
            return "https://open.spotify.com/embed/track/" + songId
        },

        home() {
            window.location.href = '/home';
        },

        truncate(str, n){
            return (str.length > n) ? str.slice(0, n-1) + '...' : str;
        }
    }
}