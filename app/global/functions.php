<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

function prev_route(): string
{
    return Route::getRoutes()->match(app('request')->create(url()->previous()))->getName();
}

function title(): string
{
    $title = config('app.name').' - ';

    match (Route::currentRouteName()) {
        'ranking' => $title .= session()->get('ranking_name'),
        'profile' => $title .= session()->get('profile_name').' Profile',
        default => $title .= Str::title(Str::lower(Str::replace('.', ' ', Route::currentRouteName())))
    };

    return $title;
}

function toSafeFilename(string $string): string
{
    return Str::of($string)->ascii()->replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-')->trim()->toString();
}
