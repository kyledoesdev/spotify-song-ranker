<?php

namespace Database\Seeders;

use App\Models\LandingPageContent;
use Illuminate\Database\Seeder;

class LandingPageContentSeeder extends Seeder
{
    /**
     * @var array<int, array{slug: string, name: string, text: string}>
     */
    protected array $contents = [
        // Hero
        ['slug' => 'hero-title', 'name' => 'Hero Title', 'text' => 'Song - Rank'],
        ['slug' => 'hero-subtitle', 'name' => 'Hero Subtitle', 'text' => 'Rank you favorite artists\' tracks.'],
        ['slug' => 'hero-credit', 'name' => 'Hero Credit', 'text' => 'Made with ❤️ by'],
        ['slug' => 'hero-credit-name', 'name' => 'Hero Credit Name', 'text' => 'Kyle'],

        // How It Works
        ['slug' => 'how-it-works-title', 'name' => 'How It Works Title', 'text' => 'How It Works'],
        ['slug' => 'how-it-works-subtitle', 'name' => 'How It Works Subtitle', 'text' => 'Three steps to your definitive ranking'],
        ['slug' => 'how-it-works-step-1-title', 'name' => 'How It Works Step 1 Title', 'text' => '1. Choose Your Content'],
        ['slug' => 'how-it-works-step-1-text', 'name' => 'How It Works Step 1 Text', 'text' => 'Search for an artist, pick a playlist, or select an audiobook or podcast. We pull in every track so you don\'t miss one.'],
        ['slug' => 'how-it-works-step-2-title', 'name' => 'How It Works Step 2 Title', 'text' => '2. Compare Pairs'],
        ['slug' => 'how-it-works-step-2-text', 'name' => 'How It Works Step 2 Text', 'text' => 'Two tracks appear side by side. Tap the one you prefer. Our efficient algorithm minimizes the comparisons needed to build your full ranking without compromising accuracy.'],
        ['slug' => 'how-it-works-step-3-title', 'name' => 'How It Works Step 3 Title', 'text' => '3. Get Your Ranking'],
        ['slug' => 'how-it-works-step-3-text', 'name' => 'How It Works Step 3 Text', 'text' => 'See your songs ordered from favorite to least favorite. Share your ranking, explore others, and join the conversation.'],

        // Everything You Can Rank
        ['slug' => 'rank-types-title', 'name' => 'Everything You Can Rank Title', 'text' => 'Everything You Can Rank'],
        ['slug' => 'rank-types-subtitle', 'name' => 'Everything You Can Rank Subtitle', 'text' => 'More than just songs'],
        ['slug' => 'rank-types-artists-title', 'name' => 'Rank Types Artists Title', 'text' => 'Artist Discographies'],
        ['slug' => 'rank-types-artists-text', 'name' => 'Rank Types Artists Text', 'text' => 'Search for any artist and rank their entire catalog. Settle the debate once and for all — what\'s really their best track?'],
        ['slug' => 'rank-types-playlists-title', 'name' => 'Rank Types Playlists Title', 'text' => 'Playlists'],
        ['slug' => 'rank-types-playlists-text', 'name' => 'Rank Types Playlists Text', 'text' => 'Rank any playlist you follow or have created. Find out which tracks truly deserve the top spot in your go-to mixes.'],
        ['slug' => 'rank-types-audiobooks-title', 'name' => 'Rank Types Audiobooks Title', 'text' => 'Audiobooks'],
        ['slug' => 'rank-types-audiobooks-text', 'name' => 'Rank Types Audiobooks Text', 'text' => 'Rank chapters and segments from your favorite audiobooks. Perfect for bookworms who want to revisit the best parts first.'],
        ['slug' => 'rank-types-podcasts-title', 'name' => 'Rank Types Podcasts Title', 'text' => 'Podcast Episodes'],
        ['slug' => 'rank-types-podcasts-text', 'name' => 'Rank Types Podcasts Text', 'text' => 'Rank episodes of your favorite podcasts. Great for recommending the best starting points to new listeners.'],

        // Community
        ['slug' => 'community-title', 'name' => 'Community Title', 'text' => 'Share, Explore & React'],
        ['slug' => 'community-explore-title', 'name' => 'Community Explore Title', 'text' => 'Explore Rankings'],
        ['slug' => 'community-explore-text', 'name' => 'Community Explore Text', 'text' => 'Browse public rankings from users across the world. Filter by artist, type, and more.'],
        ['slug' => 'community-comment-title', 'name' => 'Community Comment Title', 'text' => 'Comment & Discuss'],
        ['slug' => 'community-comment-text', 'name' => 'Community Comment Text', 'text' => 'Leave comments on rankings you agree (or disagree) with. Start conversations about the best tracks.'],
        ['slug' => 'community-react-title', 'name' => 'Community React Title', 'text' => 'React to Rankings'],
        ['slug' => 'community-react-text', 'name' => 'Community React Text', 'text' => 'Use reactions to quickly share your take. Agree? Disagree? Let the community know.'],

        // About Developer
        ['slug' => 'about-dev-title', 'name' => 'About Developer Title', 'text' => 'Built by Kyle'],
        ['slug' => 'about-dev-text', 'name' => 'About Developer Text', 'text' => 'SongRank is designed, developed, and maintained by Kyle — a solo developer who loves music, making playlists and ranking and comparing artists\' discography. I couldn\'t find a better way to rank my favorite artists\' tracks anywhere else so I decided to build it myself.'],

        // Call to Action
        ['slug' => 'cta-title', 'name' => 'Call to Action Title', 'text' => 'Ready to Rank?'],
        ['slug' => 'cta-subtitle', 'name' => 'Call to Action Subtitle', 'text' => 'Join the community and create your first ranking in minutes.'],
    ];

    public function run(): void
    {
        foreach ($this->contents as $content) {
            LandingPageContent::query()->updateOrCreate(
                ['slug' => $content['slug']],
                $content
            );
        }
    }
}
