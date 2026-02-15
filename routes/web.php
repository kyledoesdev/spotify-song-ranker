<?php

use App\Http\Controllers\SpotifyAuthController;
use App\Livewire\About;
use App\Livewire\Dashboard\Dashboard;
use App\Livewire\Explorer;
use App\Livewire\Faq;
use App\Livewire\Profile\Profile;
use App\Livewire\Profile\Settings;
use App\Livewire\Ranking\EditRanking;
use App\Livewire\Ranking\Ranking;
use App\Livewire\Terms;
use App\Livewire\Welcome\Welcome;
use Illuminate\Support\Facades\Route;
use Kyledoesdev\Essentials\Middleware\IsDeveloper;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

Route::livewire('/', Welcome::class)->name('welcome');
Route::livewire('/about', About::class)->name('about');
// Route::get('/terms', Terms::class)->name('terms');

Route::livewire('/faq', Faq::class)->name('faq');
Route::livewire('/explore', Explorer::class)->name('explore');

Route::livewire('/rank/{id}', Ranking::class)->name('ranking');
Route::livewire('/profile/{id}', Profile::class)->name('profile');

Route::get('/login/spotify', [SpotifyAuthController::class, 'login'])->name('spotify.login');
Route::get('/login/spotify/callback', [SpotifyAuthController::class, 'processLogin'])->name('spotify.process_login');
Route::get('/logout', [SpotifyAuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::livewire('/dashboard', Dashboard::class)->name('dashboard');

    Route::livewire('/rank/{id}/edit', EditRanking::class)->name('rank.edit');

    Route::livewire('/settings', Settings::class)->name('settings');

    Route::supportBubble();

    Route::middleware(IsDeveloper::class)->group(function () {
        Route::get('health', HealthCheckResultsController::class);
    });
});
