<?php

use App\Http\Controllers\RankingController;
use App\Http\Controllers\SpotifyAPIController;
use App\Http\Controllers\SpotifyAuthController;
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
Route::get('/', function() { return view('login'); })->name('login');

Auth::Routes();

Route::get('/login/spotify', [SpotifyAuthController::class, 'login'])->name('spotify.login');
Route::get('/login/spotify/callback', [SpotifyAuthController::class, 'processLogin'])->name('spotify.process_login');

Route::middleware(['auth'])->group(function() {
    Route::get('/home', function() { return view('home'); })->name('home');

    Route::get('/spotify/search', [SpotifyAPIController::class, 'search'])->name('spotify.search_api');
    Route::get('/spotify/artist_songs', [SpotifyAPIController::class, 'artistSongs'])->name('spotify.artist_songs');

    Route::get('/rank/{id}', [RankingController::class, 'show'])->name('rank.show');
    Route::post('/rank/create', [RankingController::class, 'create'])->name('rank.create');
    Route::post('/rank/store', [RankingController::class, 'store'])->name('rank.store');
});
