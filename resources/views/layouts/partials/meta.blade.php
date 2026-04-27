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

{{-- Open Graph image --}}
<meta property="og:image" content="{{ asset('images/branding/og.png') }}">
<meta property="og:image:secure_url" content="{{ secure_asset('images/branding/og.png') }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ config('app.name') }}">

{{-- Twitter card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ config('app.name') }}">
<meta name="twitter:description" content="Rank your favorite artists' tracks">
<meta name="twitter:image" content="{{ asset('images/branding/og.png') }}">
<meta name="twitter:image:alt" content="{{ config('app.name') }}">
<meta name="twitter:creator" content="@kyledoesdev">

