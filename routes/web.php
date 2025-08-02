<?php

use App\Http\Controllers\ExploreController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\RankingDownloadController;
use App\Http\Controllers\SongPlacementController;
use App\Http\Controllers\SpotifyAPIController;
use App\Http\Controllers\SpotifyAuthController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Middleware\IsDeveloper;
use App\Livewire\About;
use App\Livewire\Profile;
use App\Livewire\Settings;
use App\Livewire\Welcome;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

Route::get('/', Welcome::class)->name('welcome');
Route::get('/about', About::class)->name('about');

Route::view('/explore', [ExploreController::class, 'explore.index'])->name('explore.index');
Route::get('/explore/pages', [ExploreController::class, 'pages'])->name('explore.pages');

Route::get('/rank/{id}', RankingController::class)->name('rank.show');
Route::get('/profile/{id}', Profile::class)->name('profile');

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
    Route::post('/rank/destroy', [RankingController::class, 'destroy'])->name('rank.destroy');

    /* Ranking Export */
    Route::get('/ranking-download', [RankingDownloadController::class, 'index'])->name('ranking-download.index');

    /* Settings */
    Route::get('/settings', Settings::class)->name('settings');

    Route::supportBubble();

    Route::middleware(IsDeveloper::class)->group(function () {
        Route::get('health', HealthCheckResultsController::class);
    });
});
