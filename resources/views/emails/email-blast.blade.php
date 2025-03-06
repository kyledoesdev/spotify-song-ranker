@component('mail::message')

@if ($notifiable && $notifiable->name)
    <p>
        Hey {{ $notifiable->name }}!
    </p>
@endif

<p>
    This is Kyle from {{ config('app.name') }}. We've had a ton of new visitors check out {{ config('app.name') }} over the past few days, and I'd love to know how you heard
    about the app considering there is no advertising for it! Attached below is a quick 30 second google forum. Filling it out would help me out a lot!
    You can also access this survey by logging in below.
</p>

<p>
    <a href="https://forms.gle/SdETGGcuCnVyC4kR9" target="_blank">Fill out the survey here.</a>
</p>

<p>
    One final note: Song Rank is completely free and open source. Please share Song Rank with your friends to keep the site online. 
    Song Rank is completely supported by users like you. Thank you!
</p>

@component('mail::button', ['url' => env("APP_URL")])
    Login to {{ config('app.name') }}
@endcomponent

Thanks for using Song Rank!<br>
{{ config('app.name') }}
@endcomponent