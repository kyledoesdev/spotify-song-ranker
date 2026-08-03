<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Manager\OAuth2\User as SpotifyUser;

use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\get;

beforeEach(function () {
    Http::fake();
});

describe('email backfilling', function () {
    test('a new user without a spotify email falls back to a generated address', function () {
        fakeSpotifyLogin(id: 'spotify-abc', email: null);

        get(route('spotify.process_login'))->assertRedirect(route('dashboard'));

        expect(User::firstWhere('spotify_id', 'spotify-abc')->email)
            ->toBe('spotify-abc@songrank.dev');
    });

    test('an existing email is never overwritten', function () {
        $user = User::factory()->createOne([
            'spotify_id' => 'spotify-abc',
            'email' => 'real@example.com',
        ]);

        fakeSpotifyLogin(id: 'spotify-abc', email: 'changed@example.com');

        get(route('spotify.process_login'))->assertRedirect(route('dashboard'));

        expect($user->fresh()->email)->toBe('real@example.com');
    });

    test('login succeeds when a duplicate account already owns the generated address', function () {
        $withoutEmail = User::factory()->createOne([
            'spotify_id' => 'spotify-abc',
            'email' => null,
        ]);

        $duplicate = User::factory()->createOne([
            'spotify_id' => 'spotify-abc',
            'email' => 'spotify-abc@songrank.dev',
        ]);

        fakeSpotifyLogin(id: 'spotify-abc', email: null);

        get(route('spotify.process_login'))->assertRedirect(route('dashboard'));

        assertAuthenticatedAs($withoutEmail);

        expect($withoutEmail->fresh()->email)->toBeNull()
            ->and($duplicate->fresh()->email)->toBe('spotify-abc@songrank.dev');
    });
});

describe('returning users', function () {
    test('a soft deleted account is restored rather than duplicated', function () {
        $user = User::factory()->createOne(['spotify_id' => 'spotify-abc']);
        $user->delete();

        fakeSpotifyLogin(id: 'spotify-abc', email: $user->email);

        get(route('spotify.process_login'))->assertRedirect(route('dashboard'));

        expect($user->fresh()->deleted_at)->toBeNull()
            ->and(User::withTrashed()->where('spotify_id', 'spotify-abc')->count())->toBe(1);

        assertAuthenticatedAs($user);
    });

    test('preferences are only created for brand new users', function () {
        fakeSpotifyLogin(id: 'spotify-abc', email: 'new@example.com');

        get(route('spotify.process_login'));
        get(route('spotify.process_login'));

        expect(User::firstWhere('spotify_id', 'spotify-abc')->preferences()->count())->toBe(1);
    });
});

function fakeSpotifyLogin(string $id, ?string $email): void
{
    $spotifyUser = new SpotifyUser;
    $spotifyUser->id = $id;
    $spotifyUser->name = 'Test User';
    $spotifyUser->email = $email;
    $spotifyUser->avatar = 'https://example.com/avatar.png';
    $spotifyUser->token = 'token';
    $spotifyUser->refreshToken = 'refresh-token';

    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')->andReturn($spotifyUser);

    Socialite::shouldReceive('driver')->with('spotify')->andReturn($provider);
}
