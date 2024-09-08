@component('mail::message')

@if ($notifiable && $notifiable->name)
    <p>
        Hey {{ $notifiable->name }}!
    </p>
@endif

<p>
    This email is to notify you that <a href="{{ env("APP_URL") }}">Song Rank</a> is now at version 1.2!
</p>

<p>
    In this new version of song rank, I overhauled the entire UI of the
    application. Things are going to look familiar and different. I will
    continue to make changes over the next few days to fine tune the layouts.
    For anyone interested. We switched the application over from using
    Twitter Bootstrap to TailwindCSS.
</p>

<p>
    I've also added user "profiles". You can now view any users' completed
    rankings. You can access a profile by clicking on the "creator" on
    any ranking on the explore page. Profiles do not have follows, likes or
    comments as of now. Feel free to reach out to me if this is something
    you would like added, but I don't plan on adding these for now.
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