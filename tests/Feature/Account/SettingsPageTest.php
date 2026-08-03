<?php

use App\Jobs\DeleteUserJob;
use App\Livewire\Profile\Settings;
use App\Models\Ranking;
use App\Models\User;
use App\Notifications\DownloadDataNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\assertGuest;

describe('newsletter preferences', function () {
    test('user can enable newsletter emails', function () {
        $user = User::factory()->createOne();
        $user->preferences->update(['recieve_newsletter_emails' => false]);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->call('updateSetting', 'recieve_newsletter_emails', true);

        expect($user->fresh()->preferences->recieve_newsletter_emails)->toBeTrue();
    });

    test('user can disable newsletter emails', function () {
        $user = User::factory()->createOne();
        $user->preferences->update(['recieve_newsletter_emails' => true]);

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->call('updateSetting', 'recieve_newsletter_emails', false);

        expect($user->fresh()->preferences->recieve_newsletter_emails)->toBeFalse();
    });
});

describe('account deletion', function () {
    test('user receives download notification when deleting account with rankings', function () {
        Notification::fake();

        $user = User::factory()->createOne();
        Ranking::factory()->count(3)->create(['user_id' => $user->getKey()]);

        DeleteUserJob::dispatch($user);

        Notification::assertSentTo($user, DownloadDataNotification::class);
    });

    test('user does not receive download notification when deleting account without rankings', function () {
        Notification::fake();

        $user = User::factory()->createOne();

        DeleteUserJob::dispatch($user);

        Notification::assertNotSentTo($user, DownloadDataNotification::class);
    });

    test('user can delete their account', function () {
        Notification::fake();

        $user = User::factory()->createOne();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->call('destroy', $user->getKey())
            ->assertRedirect('/');

        expect(User::find($user->getKey()))->toBeNull();
        assertGuest();
    });

    test('account deletion also deletes their rankings', function () {
        Notification::fake();

        $user = User::factory()->createOne();
        $rankingIds = Ranking::factory()->count(3)->create(['user_id' => $user->getKey()])->pluck('id');

        DeleteUserJob::dispatchSync($user);

        expect(User::find($user->getKey()))->toBeNull();
        expect(Ranking::whereIn('id', $rankingIds)->count())->toBe(0);
    });

    test('user cannot delete another users account', function () {
        $user = User::factory()->createOne();
        $otherUser = User::factory()->createOne();

        Livewire::actingAs($user)
            ->test(Settings::class)
            ->call('destroy', $otherUser->getKey())
            ->assertForbidden();

        expect(User::find($otherUser->getKey()))->not->toBeNull();
    });
});
