<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

function get_route(): ?string
{
    return Route::currentRouteName();
}

function prev_route(): string
{
    return Route::getRoutes()->match(app('request')->create(url()->previous()))->getName();
}

function get_timezone(): string
{
    $ip = 'http://ip-api.com/json/'.request()->ip();

    if (env('APP_ENV') !== 'production') {
        return 'America/New_York';
    }

    return json_decode(file_get_contents($ip), true)['timezone'];
}

function tz(): string
{
    return auth()->check() && auth()->user()->timezone ? auth()->user()->timezone : get_timezone();
}

function title(bool $app_name = true): string
{
    $page_name = Str::title(Str::lower(Str::replace('.', ' ', Str::replace('index', 'home', get_route()))));

    return $app_name ? config('app.name').' '.$page_name : $page_name;
}

function get_formatted_name(string $name): string
{
    return Str::endsWith($name, 's') ? Str::finish($name, "'") : $name."'s";
}
