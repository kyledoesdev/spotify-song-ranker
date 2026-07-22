<?php

namespace App\Actions\Spotify;

use App\Models\User;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

final class GetArtistAppearsOnSongs
{
    /**
     * Every track the artist is featured on across other artists' records. Prolific
     * guests take dozens of requests to walk, so the album pages and album batches go
     * out concurrently, ten requests per wave to stay inside Spotify's rate limit.
     *
     * @param  Collection<int, array>  $ownedTracks
     * @return Collection<int, array>|null
     */
    public function handle(User $user, string $artistId, Collection $ownedTracks): ?Collection
    {
        $success = (new RefreshToken)->handle($user);

        if (! $success) {
            return null;
        }

        try {
            $response = $this->get($user, "https://api.spotify.com/v1/artists/{$artistId}/albums", [
                'include_groups' => 'appears_on',
                'limit' => 50,
            ]);

            $totalAlbumCount = (int) $response->json('total');
            $offsets = $totalAlbumCount > 50 ? range(50, $totalAlbumCount - 1, 50) : [];
            $albumItems = collect($response->json('items'));

            foreach (collect($offsets)->chunk(10) as $wave) {
                $albumItems = $albumItems->concat($this->fetchAlbumPages($user, $artistId, $wave));
            }

            /* compilations are licensed hit collections: they carry the artist's own songs
               (which the primary-artist filter below discards anyway) and they are the
               fattest albums to hydrate, so they never make it into the walk */
            $albumIds = $albumItems
                ->reject(fn (array $album) => data_get($album, 'album_type') === 'compilation')
                ->pluck('id')
                ->unique();

            $featured = collect();

            foreach ($albumIds->chunk(20)->chunk(10) as $wave) {
                foreach ($this->fetchAlbums($user, $wave) as $album) {
                    foreach ($this->fetchAlbumTracks($user, $album) as $track) {
                        $trackArtists = collect(data_get($track, 'artists', []));
                        $primaryArtist = $trackArtists->first();

                        /* a feature credits the artist without them being the primary — a
                           slot for their own song on somebody else's record is not one */
                        if (! $trackArtists->contains('id', $artistId) || data_get($primaryArtist, 'id') === $artistId) {
                            continue;
                        }

                        $featured->push([
                            'id' => $track['id'],
                            'name' => (string) $track['name'],
                            'uuid' => (string) Str::uuid(),
                            'cover' => data_get($album, 'images.0.url'),
                            'featured_artist' => true,
                            'primary_artist' => [
                                'id' => $primaryArtist['id'],
                                'name' => (string) $primaryArtist['name'],
                            ],
                        ]);
                    }
                }
            }

            /* the same song ships on multiple releases (album, single, deluxe) with distinct
               ids, so squash by name as well — different mixes keep their own names and can
               be swept with the remix filters instead */
            return $featured
                ->unique('id')
                ->unique('name')
                ->reject(fn (array $song) => $ownedTracks->contains('id', $song['id']))
                ->values();
        } catch (Exception $e) {
            report($e);

            return null;
        }
    }

    /**
     * How many releases the artist appears on, for one request. Assumes a fresh
     * token, so call it on the heels of another Spotify action.
     */
    public function count(User $user, string $artistId): int
    {
        try {
            return (int) $this->get($user, "https://api.spotify.com/v1/artists/{$artistId}/albums", [
                'include_groups' => 'appears_on',
                'limit' => 1,
            ])->json('total');
        } catch (Exception $e) {
            report($e);

            return 0;
        }
    }

    /**
     * One wave of album-list pages, fetched concurrently.
     */
    private function fetchAlbumPages(User $user, string $artistId, Collection $offsets): Collection
    {
        $responses = Http::pool(fn (Pool $pool) => $offsets->map(
            fn (int $offset) => $this->poolGet($pool, $user, "https://api.spotify.com/v1/artists/{$artistId}/albums", [
                'include_groups' => 'appears_on',
                'limit' => 50,
                'offset' => $offset,
            ])
        )->values()->all());

        return $this->successfulJson($responses, 'items');
    }

    /**
     * One wave of full album objects, twenty albums per request, fetched concurrently.
     */
    private function fetchAlbums(User $user, Collection $chunks): Collection
    {
        $responses = Http::pool(fn (Pool $pool) => $chunks->map(
            fn (Collection $ids) => $this->poolGet($pool, $user, 'https://api.spotify.com/v1/albums', [
                'ids' => $ids->implode(','),
            ])
        )->values()->all());

        return $this->successfulJson($responses, 'albums')->filter();
    }

    /**
     * The album's full track list — the batch album endpoint only embeds the first page.
     */
    private function fetchAlbumTracks(User $user, array $album): Collection
    {
        $tracks = collect(data_get($album, 'tracks.items', []));

        for ($offset = $tracks->count(); $offset < (int) data_get($album, 'tracks.total', 0); $offset += 50) {
            $tracks = $tracks->concat($this->get($user, "https://api.spotify.com/v1/albums/{$album['id']}/tracks", [
                'limit' => 50,
                'offset' => $offset,
            ])->json('items') ?? []);
        }

        return $tracks;
    }

    /**
     * Pull one json key out of every pooled response that came back healthy.
     */
    private function successfulJson(array $responses, string $key): Collection
    {
        return collect($responses)
            ->filter(fn ($response) => $response instanceof Response && $response->successful())
            ->flatMap(fn (Response $response) => $response->json($key) ?? []);
    }

    private function headers(User $user): array
    {
        return [
            'Authorization' => 'Bearer '.$user->external_token,
            'Content-Type' => 'application/json',
        ];
    }

    private function get(User $user, string $url, array $query = []): Response
    {
        return Http::withHeaders($this->headers($user))->get($url, $query);
    }

    private function poolGet(Pool $pool, User $user, string $url, array $query = []): PromiseInterface|Response
    {
        return $pool->withHeaders($this->headers($user))->timeout(10)->get($url, $query);
    }
}
