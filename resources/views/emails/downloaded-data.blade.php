@component('mail::message')

@if ($notifiable && $notifiable->name)
    <p>
        Hi {{ $notifiable->name }},
    </p>
@endif

<p>
    Your requested data download has been completed and attached to this email.
</p>


Thanks for using Song Rank!<br>
{{ config('app.name') }}
@endcomponent