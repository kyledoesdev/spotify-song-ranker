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

function title(bool $app_name = true): string
{
    $page_name = Str::title(Str::lower(Str::replace('.', ' ', Str::replace('index', 'home', get_route()))));

    return $app_name ? config('app.name').' '.$page_name : $page_name;
}

function get_formatted_name(string $name): string
{
    return Str::endsWith($name, 's') ? Str::finish($name, "'") : $name."'s";
}
