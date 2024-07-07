<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

function get_route(): string
{
    return Route::currentRouteName();
}

function prev_route(): string
{
    return Route::getRoutes()->match(app('request')->create(url()->previous()))->getName();
}

function get_timezone() :string
{
    $ip = file_get_contents("http://ipecho.net/plain");
    $url = 'http://ip-api.com/json/'.$ip;

    return json_decode(file_get_contents($url), true)['timezone'];
}

function tz(): string
{
    return auth()->check() && auth()->user()->timezone ? auth()->user()->timezone : get_timezone();
}

function title(bool $app_name = true): string
{
    $page_name = Str::title(Str::lower(Str::replace('.', ' ', Str::replace('index', 'home', get_route()))));
    return $app_name ? env("APP_NAME") . ' ' . $page_name : $page_name;
}