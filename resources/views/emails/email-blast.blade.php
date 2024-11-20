@component('mail::message')

@if ($notifiable && $notifiable->name)
    <p>
        Hey {{ $notifiable->name }}!
    </p>
@endif

<p>
    This email is to notify you that <a href="{{ env("APP_URL") }}">Song Rank</a> is now at version 1.4!
</p>

<p>
    song-rank.com has now moved to <a href="https://songrank.dev">songrank.dev!</a> You will still be able to access song rank at song-rank.com, but I highly recommend moving over to using the new url. <br>
    I've added a share button and back button to the rankings page to easily share a ranking with friends & to be able to navigate back to the previous page you were at.
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