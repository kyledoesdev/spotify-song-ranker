<?php

use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Livewire\Livewire;

describe('view user page', function () {
    test('offers a way through to the edit page', function () {
        $user = User::factory()->createOne();

        Livewire::actingAs(kyle())
            ->test(ViewUser::class, ['record' => $user->getKey()])
            ->assertOk()
            ->assertSee(UserResource::getUrl('edit', ['record' => $user]));
    });
});
