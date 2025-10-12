# 🎵 songrank.dev

[![Laravel Forge Site Deployment Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2F450a9eee-4bdb-4407-b76c-ae56c34a8155&style=plastic)](https://forge.laravel.com/servers/857959/sites/2522533)
[![Tests](https://github.com/kyledoesdev/spotify-song-ranker/actions/workflows/tests.yml/badge.svg)](https://github.com/kyledoesdev/spotify-song-ranker/actions/workflows/tests.yml)

## Welcome to Song Rank

songrank.dev is a Laravel application that lets you rank your favorite artists' songs through head-to-head song comparisons. This isn't your typical bracket-style ranking system. Instead, we use a merge-sort algorithm to efficiently determine your preferences by presenting strategic pairwise comparisons. You'll answer "which song do you prefer?" repeatedly until the algorithm has enough data to generate your complete ranking.

### Key Features

- **Spotify OAuth Integration** - Secure login with your Spotify account
- **Flexible Ranking Options** - Rank an entire discography, specific albums, just singles, or any custom selection of artist tracks or a playlist of tracks, podcast episodes or audiobooks
- **Smart Algorithm** - Merge-sort based comparison system minimizes the number of comparisons needed
- **Shareable Results** - Export and share your finalized rankings with friends
- **Granular Filtering** - Filter down to exactly the tracks you want to rank

## How It Works

1. 🔐 Log in with your Spotify account
2. 🔍 Search for an artist or playlist
3. 🎛️ Choose which tracks to include (full discography, specific albums, singles, etc.)
4. ⚔️ Begin comparing - pick your preferred song from each pair presented
5. 🧠 The algorithm strategically selects comparisons to minimize total questions
6. 🏆 Once complete, view your personalized ranking
7. 📤 Download or share your results
8. 🔍 Explore other rankings from the community

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you'd like to change.

## License

This project is open source and available under the MIT License.

## Credits

Created by [kyledoesdev](https://github.com/kyledoesdev)

## Links

- Live App: [songrank.dev](https://songrank.dev)
- Repository: [github.com/kyledoesdev/spotify-song-ranker](https://github.com/kyledoesdev/spotify-song-ranker)
