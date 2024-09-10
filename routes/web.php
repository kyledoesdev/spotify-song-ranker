<?php

use App\Http\Controllers\ExploreController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SpotifyAPIController;
use App\Http\Controllers\SpotifyAuthController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');
Route::view('/about', 'about')->name('about');

Route::get('/login/spotify', [SpotifyAuthController::class, 'login'])->name('spotify.login');
Route::get('/login/spotify/callback', [SpotifyAuthController::class, 'processLogin'])->name('spotify.process_login');
Route::get('/logout', [SpotifyAuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::view('/home', 'home')->name('home');

    /* Spotify API routes */
    Route::get('/spotify/search', [SpotifyAPIController::class, 'search'])->name('spotify.search_api');
    Route::get('/spotify/artist_songs', [SpotifyAPIController::class, 'artistSongs'])->name('spotify.artist_songs');

    /* Ranking CRUD routes */
    Route::post('/rank/create', [RankingController::class, 'create'])->name('rank.create');
    Route::get('/rank/{id}/edit', [RankingController::class, 'edit'])->name('rank.edit');
    Route::post('/rank/{id}/update', [RankingController::class, 'update'])->name('rank.update');
    Route::post('/rank/delete', [RankingController::class, 'delete'])->name('rank.delete');
    Route::post('/rank/finish', [RankingController::class, 'finish'])->name('rank.finish');
    Route::get('/ranks/export', [RankingController::class, 'export'])->name('rankings.export');

    /* Settings */
    Route::view('/settings', 'settings.index')->name('settings.index');
    Route::post('/settings/update', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/destroy', [SettingsController::class, 'destroy'])->name('settings.destroy');
});

Route::view('/profile', 'profile.index')->name('profile.index');
Route::get('/ranks/pages', [RankingController::class, 'pages'])->name('rank.pages');
Route::view('/explore', 'explore')->name('explore');
Route::get('/explore/pages', [ExploreController::class, 'pages'])->name('explore.pages');
Route::get('/rank/{id}', [RankingController::class, 'show'])->name('rank.show');