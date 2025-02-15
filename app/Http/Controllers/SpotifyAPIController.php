<?php

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpotifyAPIController extends Controller
{
    private Client $client;
    private string $errorMsg;

    public function __construct()
    {
        $this->client = new Client();
        $this->errorMsg = "Something went wrong authenticating with Spotify's servers. Please log out and log back in.";
    }

    public function search(Request $request): JsonResponse
    {
        $this->refreshToken();

        try {
            $response = $this->client->request(
                'GET',
                "https://api.spotify.com/v1/search?q={$request->artist}&type=artist&limit=10", [
                    'headers' => [
                        'Authorization' => 'Bearer '.auth()->user()->external_token,
                        'Content-Type' => 'application/json',
                    ],
                ]
            );

            $data = collect(json_decode($response->getBody())->artists->items);

            $artists = collect();

            $data->each(function ($artist) use ($artists) {
                if ($artist->images && $artist->images[0] && $artist->images[0]->url) {
                    $artists->push([
                        'id' => $artist->id,
                        'name' => $artist->name,
                        'cover' => $artist->images[0]->url,
                    ]);
                }
            });
        } catch (Exception) {
            return response()->json([
                'message' => $this->errorMsg,
            ], 500);
        }

        return response()->json([
            'artists' => $artists,
        ], 200);
    }

    public function artistSongs(Request $request): JsonResponse
    {
        $this->refreshToken();

        try {
            //first get the total number of artist albums
            $response = $this->client->request(
                'GET',
                "https://api.spotify.com/v1/artists/{$request->id}/albums?include_groups=album,single&limit=50", [
                    'headers' => [
                        'Authorization' => 'Bearer '.auth()->user()->external_token,
                        'Content-Type' => 'application/json',
                    ],
                ]
            );

            $totalAlbumCount = json_decode($response->getBody())->total;
            $songs = collect();
            $offset = 0;

            for ($i = 0; $i < round($totalAlbumCount / 50, 2, PHP_ROUND_HALF_UP); $i++) {
                $response = $this->client->request(
                    'GET',
                    "https://api.spotify.com/v1/artists/{$request->id}/albums?include_groups=album,single&limit=50&offset={$offset}", [
                        'headers' => [
                            'Authorization' => 'Bearer '.auth()->user()->external_token,
                            'Content-Type' => 'application/json',
                        ],
                    ]
                );

                $albums = collect(json_decode($response->getBody())->items)->pluck('id');
                $albumRequests = array_chunk($albums->toArray(), 20);

                foreach ($albumRequests as $albumRequest) {
                    $albumIds = implode(',', $albumRequest);

                    //get tracks from the albums from the album ids
                    $response = $this->client->request(
                        'GET',
                        "https://api.spotify.com/v1/albums?ids={$albumIds}", [
                            'headers' => [
                                'Authorization' => 'Bearer '.auth()->user()->external_token,
                                'Content-Type' => 'application/json',
                            ],
                        ]
                    );

                    $albums = collect(json_decode($response->getBody())->albums);

                    $albums->each(function ($album) use ($songs) {
                        $albumSongs = collect($album->tracks->items);

                        $albumSongs->each(function ($song) use ($album, $songs) {
                            $songs->push([
                                'id' => $song->id,
                                'name' => $song->name,
                                'cover' => $album->images[0]->url,
                            ]);
                        });
                    });
                }

                $offset += 50;
            }

            //filter duplicate songs, like if a song is on an album and single.
            $songs = $songs->groupBy('name')->map->first();

        } catch (Exception) {
            return response()->json([
                'message' => $this->errorMsg,
            ], 500);
        }

        return response()->json([
            'songs' => $songs,
        ], 200);
    }

    public function refreshToken()
    {
        try {
            $response = $this->client->request(
                'POST',
                "https://accounts.spotify.com/api/token", [
                    'form_params' => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => auth()->user()->external_refresh_token,
                        'client_id' => env('SPOTIFY_CLIENT_ID'),
                        'client_secret' => env('SPOTIFY_CLIENT_SECRET'),
                    ],
                ]
            );

            $data = json_decode((string) $response->getBody(), true);

            auth()->user()->update([
                'external_token' => $data['access_token'],
            ]);

        } catch(Exception) {
            return response()->json([
                'message' => $this->errorMsg,
            ], 500);
        }
    }
}
