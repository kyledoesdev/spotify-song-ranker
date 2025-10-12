<?php

use App\Jobs\DeleteUserJob;
use App\Livewire\Profile\Settings;
use App\Models\Ranking;
use App\Models\User;
use App\Notifications\DownloadDataNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

test('user can enable ranking reminder emails', function () {
    $user = User::factory()->create();
    $user->preferences->update(['recieve_reminder_emails' => false]);

    Livewire::actingAs($user)
        ->test(Settings::class)
        ->call('updateSetting', 'recieve_reminder_emails', true);

    expect($user->fresh()->preferences->recieve_reminder_emails)->toBeTrue();
});

test('user can disable ranking reminder emails', function () {
    $user = User::factory()->create();
    $user->preferences->update(['recieve_reminder_emails' => true]);

    Livewire::actingAs($user)
        ->test(Settings::class)
        ->call('updateSetting', 'recieve_reminder_emails', false);

    expect($user->fresh()->preferences->recieve_reminder_emails)->toBeFalse();
});

test('user can enable newsletter emails', function () {
    $user = User::factory()->create();
    $user->preferences->update(['recieve_newsletter_emails' => false]);

    Livewire::actingAs($user)
        ->test(Settings::class)
        ->call('updateSetting', 'recieve_newsletter_emails', true);

    expect($user->fresh()->preferences->recieve_newsletter_emails)->toBeTrue();
});

test('user can disable newsletter emails', function () {
    $user = User::factory()->create();
    $user->preferences->update(['recieve_newsletter_emails' => true]);

    Livewire::actingAs($user)
        ->test(Settings::class)
        ->call('updateSetting', 'recieve_newsletter_emails', false);

    expect($user->fresh()->preferences->recieve_newsletter_emails)->toBeFalse();
});

test('user receives download notification when deleting account with rankings', function () {
    Notification::fake();

    $user = User::factory()->create();
    Ranking::factory()->count(3)->create(['user_id' => $user->id]);

    DeleteUserJob::dispatch($user);

    Notification::assertSentTo($user, DownloadDataNotification::class);
});

test('user does not receive download notification when deleting account without rankings', function () {
    Notification::fake();

    $user = User::factory()->create();

    DeleteUserJob::dispatch($user);

    Notification::assertNotSentTo($user, DownloadDataNotification::class);
});

test('user can delete their account', function () {
    Notification::fake();

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Settings::class)
        ->call('destroy', $user->id)
        ->assertRedirect('/');

    expect(User::find($user->id))->toBeNull();
    $this->assertGuest();
});

test('user account deletion also deletes their rankings', function () {
    Notification::fake();

    $user = User::factory()->create();
    $rankings = Ranking::factory()->count(3)->create(['user_id' => $user->id]);
    $rankingIds = $rankings->pluck('id');

    DeleteUserJob::dispatchSync($user);

    expect(User::find($user->id))->toBeNull();
    expect(Ranking::whereIn('id', $rankingIds)->count())->toBe(0);
});

test('user cannot delete another users account', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Settings::class)
        ->call('destroy', $otherUser->id)
        ->assertForbidden();

    expect(User::find($otherUser->id))->not->toBeNull();
});
