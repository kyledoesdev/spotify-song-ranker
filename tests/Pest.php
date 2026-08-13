<?php

use App\Contracts\Rankable;
use App\Models\Ranking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Filament');

pest()->extend(TestCase::class)
    ->in('Platform');

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Pass a $source (Artist, Playlist or Show) to control what the ranking is of;
 * it defaults to a fresh artist via the factory.
 */
function publicCompletedRanking(?Rankable $source = null, array $attributes = []): Ranking
{
    $factory = Ranking::factory();

    if ($source) {
        $factory = $factory->for($source, 'source');
    }

    return $factory->create(array_merge([
        'is_public' => true,
        'is_ranked' => true,
        'completed_at' => now(),
    ], $attributes));
}

function kyle(): User
{
    return User::factory()->createOne([
        'name' => 'Kyle',
        'is_dev' => true,
    ]);
}
