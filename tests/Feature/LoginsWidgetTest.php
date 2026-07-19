<?php

use App\Filament\Widgets\LoginsWidget;
use App\Models\User;
use Kyledoesdev\Essentials\Stats\LoginStat;
use Livewire\Livewire;

describe('logins widget', function () {
    test('renders all stats', function () {
        $user = User::factory()->createOne(['timezone' => 'UTC']);

        Livewire::actingAs($user)
            ->test(LoginsWidget::class)
            ->assertSee('Total Users')
            ->assertSee('Deleted Users')
            ->assertSee('Daily Active Users')
            ->assertSee('Weekly Active Users')
            ->assertSee('Monthly Active Users');
    });

    test('counts total and deleted users', function () {
        $actor = User::factory()->createOne(['timezone' => 'UTC']);
        User::factory()->count(2)->create();
        User::factory()->count(3)->create()->each->delete();

        Livewire::actingAs($actor)
            ->test(LoginsWidget::class)
            ->assertSee('3')
            ->assertSee('All-time account deletions');
    });

    test('reflects daily active users from login stats', function () {
        $user = User::factory()->createOne(['timezone' => 'UTC']);

        LoginStat::increase(5);

        Livewire::actingAs($user)
            ->test(LoginsWidget::class)
            ->assertSee('Daily Active Users')
            ->assertSee('5');
    });
});
