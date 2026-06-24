<?php

use App\Filament\Widgets\LoginsWidget;
use App\Models\User;
use Kyledoesdev\Essentials\Stats\LoginStat;
use Livewire\Livewire;

test('user stats widget renders all stats', function () {
    $user = User::factory()->create(['timezone' => 'UTC']);

    Livewire::actingAs($user)
        ->test(LoginsWidget::class)
        ->assertSee('Total Users')
        ->assertSee('Deleted Users')
        ->assertSee('Daily Active Users')
        ->assertSee('Weekly Active Users')
        ->assertSee('Monthly Active Users');
});

test('user stats widget counts total and deleted users', function () {
    $actor = User::factory()->create(['timezone' => 'UTC']);
    User::factory()->count(2)->create();
    User::factory()->count(3)->create()->each->delete();

    Livewire::actingAs($actor)
        ->test(LoginsWidget::class)
        ->assertSee('3') // total active users: actor + 2
        ->assertSee('All-time account deletions');
});

test('user stats widget reflects daily active users from login stats', function () {
    $user = User::factory()->create(['timezone' => 'UTC']);

    LoginStat::increase(5);

    Livewire::actingAs($user)
        ->test(LoginsWidget::class)
        ->assertSee('Daily Active Users')
        ->assertSee('5');
});
