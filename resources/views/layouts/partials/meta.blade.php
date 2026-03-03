@php
    use App\Models\ApplicationDashboard;

    $seo = cache()->remember('seo-terms', now()->addWeeks(1), function() {
        return ApplicationDashboard::first()->seo_terms;
    });
@endphp

<meta charset="utf-8">
<meta name="author" content="kyledoesdev">
<meta name="description" content="{{ title() }}">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:title" content="{{ title() }}">
<meta property="og:description" content="{{ title() }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta name="keywords" content="{{ $seo }}">


