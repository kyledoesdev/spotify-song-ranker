<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

function prev_route(): string
{
    return Route::getRoutes()->match(app('request')->create(url()->previous()))->getName();
}

function short_number(int $number): string
{
    return match (true) {
        $number >= 1000000 => round($number / 1000000, 1).'m',
        $number >= 1000 => round($number / 1000, 1).'k',
        default => (string) $number,
    };
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
