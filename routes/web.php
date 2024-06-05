<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\SettingsController;

Route::view('/', 'welcome')->name('welcome');
Route::get('/login/spotify', [App\Http\Controllers\SpotifyAuthController::class, 'login'])->name('spotify.login');
Route::get('/login/spotify/callback', [App\Http\Controllers\SpotifyAuthController::class, 'processLogin'])->name('spotify.process_login');
Route::get('/logout', function () {
    Auth::logout();

    return redirect(route('welcome'))->with('success', "You've logged out. See ya next time!");
})->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    /* Spotify API routes */
    Route::get('/spotify/search', [App\Http\Controllers\SpotifyAPIController::class, 'search'])->name('spotify.search_api');
    Route::get('/spotify/artist_songs', [App\Http\Controllers\SpotifyAPIController::class, 'artistSongs'])->name('spotify.artist_songs');

    /* Ranking CRUD routes */
    Route::get('/ranks', [RankingController::class, 'index'])->name('rank.index');
    Route::get('/rank/{id}', [RankingController::class, 'show'])->name('rank.show');
    Route::post('/rank/create', [RankingController::class, 'create'])->name('rank.create');
    Route::get('/rank/{id}/edit', [RankingController::class, 'edit'])->name('rank.edit');
    Route::post('/rank/{id}/update', [RankingController::class, 'update'])->name('rank.update');
    Route::post('/rank/delete', [RankingController::class, 'delete'])->name('rank.delete');
    Route::post('/rank/finish', [RankingController::class, 'finish'])->name('rank.finish');
    Route::get('/ranks/pages', [RankingController::class, 'pages'])->name('rank.pages');
    Route::get('/ranks/export', [RankingController::class, 'export'])->name('rankings.export');

    /* Settings */
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/delete', [SettingsController::class, 'destroy'])->name('settings.delete');
});
