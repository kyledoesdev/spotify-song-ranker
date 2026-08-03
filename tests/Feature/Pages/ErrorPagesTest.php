<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\withHeaders;

beforeEach(function () {
    Route::get('/testing/abort/{status}', fn (int $status) => abort($status));
});

describe('error pages', function () {
    test('renders the custom error page with the site chrome', function () {
        get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('404')
            ->assertSee('Leaderboards')
            ->assertSee('Built by');
    });

    test('drops the site chrome for livewire requests', function () {
        withHeaders(['X-Livewire' => 'true'])
            ->get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('Try that again')
            ->assertDontSee('Leaderboards')
            ->assertDontSee('Built by');
    });

    test('offers support options on server errors', function () {
        get('/testing/abort/500')
            ->assertStatus(500)
            ->assertSee('Ask on Discord')
            ->assertDontSee('Open the support bubble');
    });

    test('adds the support bubble for signed in users', function () {
        actingAs(User::factory()->create());

        get('/testing/abort/500')
            ->assertStatus(500)
            ->assertSee('Open the support bubble');
    });

    test('falls back to the xx views for statuses without their own page', function () {
        get('/testing/abort/502')
            ->assertStatus(502)
            ->assertSee('502');

        get('/testing/abort/418')
            ->assertStatus(418)
            ->assertSee('418');
    });
});
