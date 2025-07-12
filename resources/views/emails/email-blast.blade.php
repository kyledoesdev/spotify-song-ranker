@component('mail::message')

@if ($notifiable && $notifiable->name)
    <p>
        Hey {{ $notifiable->name }}!
    </p>
@endif

{!! Str::of($emailTemplate->content)->markdown() !!}

@component('mail::button', ['url' => env("APP_URL")])
    Login to {{ config('app.name') }}
@endcomponent

Thanks for using Song Rank!<br>
{{ config('app.name') }}
@endcomponent