
@php
    use App\Actions\Spotify\GetPlaylistTracks;

    $playlist = 'https://open.spotify.com/playlist/4uyeD5M9GvvMB1cBmLMlxU?si=9e2b41ba2a5e494d';
    
    $results = (new GetPlaylistTracks)->search(App\Models\User::find(1), $playlist);

    dd($results);
@endphp