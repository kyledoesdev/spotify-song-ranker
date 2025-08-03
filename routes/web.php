<?php

use App\Http\Controllers\SpotifyAuthController;
use App\Http\Middleware\IsDeveloper;
use App\Livewire\About;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Explorer;
use App\Livewire\Profile\Profile;
use App\Livewire\Profile\Settings;
use App\Livewire\Ranking\EditRanking;
use App\Livewire\Ranking\Ranking;
use App\Livewire\Welcome\Welcome;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

Route::get('/', Welcome::class)->name('welcome');
Route::get('/about', About::class)->name('about');

Route::get('/explore', Explorer::class)->name('explore');

Route::get('/rank/{id}', Ranking::class)->name('rank.show');
Route::get('/profile/{id}', Profile::class)->name('profile');

Route::get('/login/spotify', [SpotifyAuthController::class, 'login'])->name('spotify.login');
Route::get('/login/spotify/callback', [SpotifyAuthController::class, 'processLogin'])->name('spotify.process_login');
Route::get('/logout', [SpotifyAuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/rank/{id}/edit', EditRanking::class)->name('rank.edit');

    Route::get('/settings', Settings::class)->name('settings');

    Route::supportBubble();

    Route::middleware(IsDeveloper::class)->group(function () {
        Route::get('health', HealthCheckResultsController::class);
    });
});
