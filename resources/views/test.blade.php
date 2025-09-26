
@php
    use App\Actions\Spotify\GetPlaylistTracks;

    $playlist = 'https://open.spotify.com/playlist/5VNe6lfuONaCnWaR7ZVzWT?si=4A_y-yFcTOGgPJNDFupU9g';
    
    $results = (new GetPlaylistTracks)->search(App\Models\User::find(11), $playlist);

    dd($results);
@endphp