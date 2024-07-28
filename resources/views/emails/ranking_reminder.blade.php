@component('mail::message')

@if ($notifiable && $notifiable->name)
    <p>
        Hi {{ $notifiable->name }},
    </p>
@endif

<p>Your have incomplete rankings to finish. Below are the rankings that still need to be completed.</p>

<table class="table table-striped">
    <thead>
        <tr>
            <th>List</th>
            <th>Artist</th>
            <th>Song Count</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rankings as $ranking)
            <tr>
                <td>{{ $ranking->name }}</td>
                <td>{{ $ranking->artist->artist_name }}</td>
                <td>{{ $ranking->songs_count }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p style="padding-top: 20px; padding-bottom: 20px;">Log in below to view complete your rankings.</p>

@component('mail::button', ['url' => env("APP_URL")])
    Log In
@endcomponent

Thanks for using Song Rank!<br>
{{ config('app.name') }}
@endcomponent