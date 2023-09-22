<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function() { return auth()->check() ? view('home') : view('login'); })->name('login');

Auth::Routes();

Route::get('/login/spotify', [App\Http\Controllers\SpotifyAuthController::class, 'login'])->name('spotify.login');
Route::get('/login/spotify/callback', [App\Http\Controllers\SpotifyAuthController::class, 'processLogin'])->name('spotify.process_login');

Route::middleware(['auth'])->group(function() {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/spotify/search', [App\Http\Controllers\SpotifyAPIController::class, 'search'])->name('spotify.search_api');
    Route::get('/spotify/artist_songs', [App\Http\Controllers\SpotifyAPIController::class, 'artistSongs'])->name('spotify.artist_songs');

    Route::get('/ranks', [App\Http\Controllers\RankingController::class, 'index'])->name('rank.index');
    Route::get('/rank/{id}', [App\Http\Controllers\RankingController::class, 'show'])->name('rank.show');
    Route::post('/rank/create', [App\Http\Controllers\RankingController::class, 'create'])->name('rank.create');
    Route::post('/rank/update', [App\Http\Controllers\RankingController::class, 'update'])->name('rank.update');
});
