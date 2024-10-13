@component('mail::message')

@if ($notifiable && $notifiable->name)
    <p>
        Hey {{ $notifiable->name }}!
    </p>
@endif

<p>
    This email is to notify you that <a href="{{ env("APP_URL") }}">Song Rank</a> is now at version 1.3!
</p>

<p>
    Hey everyone, we fixed a major bug on song rank that was not allowing you to save your rankings. it should be fixed now. Sorry for the issue!
</p>

<p>
    If you happen to run into any other bugs, be sure to report them 
    <a href="https://github.com/kyledoesdev/spotify-song-ranker/blob/master/contributing.md">here.</a>
</p>

<p>
    One final note: Song Rank is completely free and open source. Please share Song Rank with your friends to keep the site online. 
    Song Rank is completely supported by users like you. Thank you!
</p>

@component('mail::button', ['url' => env("APP_URL")])
    Login to Song Rank
@endcomponent

Thanks for using Song Rank!<br>
{{ config('app.name') }}
@endcomponent