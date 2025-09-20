@component('mail::message')

@if ($notifiable && $notifiable->name)
    <p>
        Hey {{ $notifiable->name }},
    </p>
@endif

<p>
    This email is to remind you that you have incomplete rankings to finish. Below are the rankings that are not completed.
    If you don't plan on completing the ranking, log in to <a href="{{ config('app.url') }}">{{ config('app.name') }}</a> to delete it.
</p>

<table class="table table-striped">
    <thead>
        <tr>
            <th>List</th>
            <th>Artist</th>
            <th>Song</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rankings as $ranking)
            <tr>
                <td>{{ $ranking->name }}</td>
                <td>{{ $ranking->artist->artist_name }}</td>
                <td>{{ $ranking->songs_count }}</td>
                <td>{{ $ranking->created_at->diffForHumans() }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p style="padding-top: 20px; padding-bottom: 20px;">
If you do not want to get these reminder emails, log in to <a href="{{ config('app.url') }}">{{ config('app.name') }}</a> to disable them in your settings.
</p>

<p style="padding-bottom: 20px;">Log in below to view complete your rankings.</p>

@component('mail::button', ['url' => config('app.url')])
Log In to {{ config('app.name') }}
@endcomponent

Thanks for using Song Rank!<br>
{{ config('app.name') }}
@endcomponent