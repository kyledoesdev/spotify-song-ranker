<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\withHeaders;

beforeEach(function () {
    Route::get('/testing/abort/{status}', fn (int $status) => abort($status));
});

describe('404 page', function () {
    test('renders the themed not found page for a missing route', function () {
        get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('404');
    });

    test('includes the navigation and footer on a standard request', function () {
        get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('Leaderboards')
            ->assertSee('Built by');
    });

    test('offers explore and home actions to guests', function () {
        get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('Take me home')
            ->assertSee('Explore rankings');
    });

    test('points signed in users at their dashboard', function () {
        actingAs(User::factory()->create());

        get('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('My rankings');
    });
});

describe('livewire error responses', function () {
    test('omits the navigation and footer', function () {
        livewireRequest('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertDontSee('Leaderboards')
            ->assertDontSee('Built by');
    });

    test('offers a retry action instead of the explore action', function () {
        livewireRequest('/this-page-does-not-exist')
            ->assertNotFound()
            ->assertSee('Try that again')
            ->assertDontSee('Explore rankings');
    });
});

describe('500 page', function () {
    test('tells the user the team has been notified', function () {
        get('/testing/abort/500')
            ->assertStatus(500);
    });

    test('offers support options', function () {
        get('/testing/abort/500')
            ->assertStatus(500)
            ->assertSee('Ask on Discord');
    });

    test('opens the support bubble for signed in users', function () {
        actingAs(User::factory()->create());

        get('/testing/abort/500')
            ->assertStatus(500)
            ->assertSee('Open the support bubble');
    });

    test('falls back to the 5xx page for other server errors', function () {
        get('/testing/abort/502')
            ->assertStatus(502)
            ->assertSee('502');
    });
});

function livewireRequest(string $uri): TestResponse
{
    return withHeaders(['X-Livewire' => 'true'])->get($uri);
}
