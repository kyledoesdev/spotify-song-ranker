@component('mail::message')

@if ($notifiable && $notifiable->name)
    <p>
        Hey {{ $notifiable->name }},
    </p>
@endif

<p>
    This email is to notify you that <a href="{{ env("APP_URL") }}">Song Rank</a> is now at version 1.1!
</p>

<p>
    Song Rank was approved for full Spotify API privileges about 1 month ago now, and within that month the following features were added and bugs were fixed:
</p>

<h5>Features:</h5>
<ul>
    <li>Explore Features: Explore other users' rankings. (Explore feature available to guest users as well.)</li>
    <li>Ranking Privacy: Rankings can now be created or edited to not be visible in the explore feed. If you built a ranking that you would like kept private, 
        please log in and update its visibility. Ranking visibility does not interact with sharing a ranking, it just does not appear on the explore feed.
    </li>
    <li>Export your data: You can now log in to your account's settings page and download a backup of your ranking data.</li>
    <li>New filter when creating a new ranking.</li>
    <li>Account Deletion... for if any of you want to leave :(</li>
</ul>

<br>

<h5>Bugs:</h5>
<ul>
    <li>Fixed "creating a new rank" & not being redirected bug.</li>
    <li>Fixed spotify token issue. (Logging out & back in).</li>
    <li>If you do not have any rankings, the UI is reflective of that rather than showing an empty table/list.</li>
    <li>Some other obsecure technical things.</li>
</ul>

<p>
    If you happen to run into any other bugs, be sure to report them 
    <a href="https://github.com/kylenotfound/spotify-song-ranker/blob/master/contributing.md">here.</a>
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