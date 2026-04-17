<?php

use App\Livewire\Ranking\Card as ProfileRankingCard;
use App\Livewire\Ranking\EditRanking;
use App\Models\Artist;
use App\Models\Ranking;
use App\Models\Song;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    Artist::factory()->create();
    User::factory()->create();
});

test('owner can view the edit page', function () {
    $user = User::factory()
        ->has(Ranking::factory()->count(1))
        ->create();

    $this->actingAs($user)
        ->get(route('rank.edit', ['id' => $user->rankings->first()->getKey()]))
        ->assertOk();
});

test('non-owner cannot view the edit page', function () {
    $ranking = Ranking::factory()->create();

    $this->actingAs(User::first())
        ->get(route('rank.edit', ['id' => $ranking->getKey()]))
        ->assertForbidden();
});

test('owner can update name and visibility', function () {
    $user = User::factory()->create();

    $ranking = Ranking::factory()
        ->has(Song::factory()->count(5))
        ->create([
            'user_id' => $user->getKey(),
            'is_public' => false,
        ]);

    Livewire::actingAs($user)
        ->test(EditRanking::class, ['id' => $ranking->getKey()])
        ->set('form.name', 'new name')
        ->set('form.is_public', true)
        ->call('update')
        ->assertHasNoErrors();

    $ranking->refresh();

    expect($ranking->name)->toBe('new name');
    expect($ranking->is_public)->toBeTrue();
});

test('non-owner cannot update name and visibility', function () {
    $user = User::factory()->create();

    $ranking = Ranking::factory()
        ->has(Song::factory()->count(5))
        ->create(['is_public' => false]);

    Livewire::actingAs($user)
        ->test(EditRanking::class, ['id' => $ranking->getKey()])
        ->assertForbidden();
});

test('owner can delete their ranking', function () {
    $user = User::factory()
        ->has(Ranking::factory())
        ->create();

    $ranking = $user->rankings->first();

    expect($ranking->deleted_at)->toBeNull();
    expect(Song::where('ranking_id', $ranking->getKey())->count())->toBe(10);

    Livewire::actingAs($user)
        ->test(ProfileRankingCard::class, ['ranking' => $ranking])
        ->call('destroy')
        ->assertDispatched('rankings-updated');

    $ranking->refresh();

    expect($ranking->deleted_at)->not->toBeNull();
    expect(Song::where('ranking_id', $ranking->getKey())->count())->toBe(0);
});

test('non-owner cannot delete another user\'s ranking', function () {
    $owner = User::factory()
        ->has(Ranking::factory())
        ->create();

    $nonOwner = User::factory()->create();
    $ranking = $owner->rankings->first();

    expect($ranking->deleted_at)->toBeNull();
    expect(Song::where('ranking_id', $ranking->getKey())->count())->toBe(10);

    Livewire::actingAs($nonOwner)
        ->test(ProfileRankingCard::class, ['ranking' => $ranking])
        ->call('destroy')
        ->assertForbidden();

    $ranking->refresh();

    expect($ranking->deleted_at)->toBeNull();
    expect(Song::where('ranking_id', $ranking->getKey())->count())->toBe(10);
});

test('non-owner can view a public completed ranking', function () {
    $visitor = User::factory()->create();

    $ranking = Ranking::factory()
        ->has(Song::factory()->count(5))
        ->create([
            'is_public' => true,
            'is_ranked' => true,
        ]);

    $this->actingAs($visitor)
        ->get(route('ranking', ['id' => $ranking->getKey()]))
        ->assertOk();
});

test('non-owner cannot view a private ranking', function () {
    $visitor = User::factory()->create();

    $ranking = Ranking::factory()
        ->has(Song::factory()->count(5))
        ->create([
            'is_public' => false,
            'is_ranked' => true,
        ]);

    $this->actingAs($visitor)
        ->get(route('ranking', ['id' => $ranking->getKey()]))
        ->assertStatus(404);
});

test('owner can view their own private ranking', function () {
    $user = User::factory()->create();

    $ranking = Ranking::factory()
        ->has(Song::factory()->count(5))
        ->create([
            'user_id' => $user->getKey(),
            'is_public' => false,
            'is_ranked' => true,
        ]);

    $this->actingAs($user)
        ->get(route('ranking', ['id' => $ranking->getKey()]))
        ->assertOk();
});
