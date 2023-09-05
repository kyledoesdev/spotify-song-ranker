<template>
    <div>
        <div v-if="currentPair">
                <img :src="currentPair[0].cover" alt="Song 1 Cover" />
                <button @click="chooseSong(0)">{{ this.currentPair[0].title }}</button>
                <span>OR</span>
                <button @click="chooseSong(1)">{{ this.currentPair[1].title }}</button>
                <img :src="currentPair[1].cover" alt="Song 2 Cover" />
        </div>
        <div v-else>
            <h2>Ranking Results</h2>
            <ol>
                <li v-for="song in rankedSongs" :key="song.id">
                    {{ song.title }}
                </li>
            </ol>
        </div>
    </div>
  </template>
  
  <script>
  export default {
    props: ['ranksongs'],

    data() {
        return {
            songs: this.ranksongs,
            rankedSongs: [],      // To store the ranked order of songs
            comparedSongs: new Set(), // To keep track of compared songs
            rankedSet: new Set(),    // To keep track of ranked songs
            pairQueue: [],        // Queue of pairs for comparison
        };
    },

    methods: {
        chooseSong(selectedIndex) {
            const chosenSong = this.currentPair[selectedIndex];

            // Check if the chosen song is already ranked
            if (!this.rankedSet.has(chosenSong.id)) {
                // Update the rankedSongs array with the chosen song
                this.rankedSongs.push(chosenSong);

                // Mark the selected song as compared and ranked
                this.comparedSongs.add(chosenSong.id);
                this.rankedSet.add(chosenSong.id);
            }

            this.pairQueue.shift(); // Remove the current pair
            
            if (this.pairQueue.length === 0) {
                // Include unselected songs in the ranked list
                this.includeUnselectedSongs();
            }
        },

        includeUnselectedSongs() {
            // Add unselected songs to the ranked list
            this.songs.forEach((song) => {
                if (!this.rankedSet.has(song.id) && !this.comparedSongs.has(song.id)) {
                    this.rankedSongs.push(song);
                }
            });
        },
    },

    computed: {
        currentPair() {
            return this.pairQueue[0];
        },
    },

    created() {
        for (let i = 0; i < this.songs.length; i++) {
            for (let j = i + 1; j < this.songs.length; j++) {
                this.pairQueue.push([this.songs[i], this.songs[j]]);
            }
        }

        //shuffle
        this.pairQueue.sort(() => Math.random() - 0.5);
    },
};
</script>
  