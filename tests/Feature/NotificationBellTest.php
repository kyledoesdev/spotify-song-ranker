<?php

use App\Livewire\Notifications\Bell;
use App\Livewire\Notifications\ShowAll;
use App\Models\Comment;
use App\Models\Ranking;
use App\Models\Song;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->owner = User::factory()->create();

    $this->ranking = Ranking::factory()
        ->has(Song::factory()->count(3))
        ->create([
            'user_id' => $this->owner->getKey(),
            'is_public' => true,
            'is_ranked' => true,
            'comments_enabled' => true,
        ]);
});

describe('Notification Bell Component', function () {
    test('renders for authenticated users', function () {
        Livewire::actingAs($this->owner)
            ->test(Bell::class)
            ->assertOk();
    });

    test('shows zero unread count when no notifications exist', function () {
        Livewire::actingAs($this->owner)
            ->test(Bell::class)
            ->assertSet('unreadCount', 0);
    });

    test('shows correct unread count', function () {
        $commenter = User::factory()->create();

        createComment($commenter, $this->ranking, 'First comment');
        createComment($commenter, $this->ranking, 'Second comment');

        Livewire::actingAs($this->owner)
            ->test(Bell::class)
            ->assertSet('unreadCount', 2);
    });

    test('opening bell marks all notifications as read', function () {
        $commenter = User::factory()->create();

        createComment($commenter, $this->ranking, 'Comment 1');
        createComment($commenter, $this->ranking, 'Comment 2');
        createComment($commenter, $this->ranking, 'Comment 3');

        Livewire::actingAs($this->owner)
            ->test(Bell::class)
            ->assertSet('unreadCount', 3)
            ->call('open')
            ->assertSet('unreadCount', 0);

        expect($this->owner->unreadNotifications()->count())->toBe(0);
        expect($this->owner->notifications()->count())->toBe(3);
    });
});

describe('Notifications ShowAll Page', function () {
    test('authenticated user can view notifications page', function () {
        $this->actingAs($this->owner)
            ->get(route('notifications'))
            ->assertOk();
    });
});

describe('Comment Notification Dispatch', function () {
    test('notification is created when someone comments on a ranking', function () {
        $commenter = User::factory()->create();

        createComment($commenter, $this->ranking, 'This is a test comment');

        expect($this->owner->notifications()->count())->toBe(1);

        $notification = $this->owner->notifications()->first();

        expect($notification->user_name)->toBe($commenter->name);
        expect($notification->entity['name'])->toBe($this->ranking->name);
        expect($notification->entity['id'])->toBe($this->ranking->getKey());
    });

    test('notification is not created when owner comments on their own ranking', function () {
        createComment($this->owner, $this->ranking, 'My own comment');

        expect($this->owner->notifications()->count())->toBe(0);
    });

    test('unread notification is removed when comment is deleted', function () {
        $commenter = User::factory()->create();

        $comment = createComment($commenter, $this->ranking);

        expect($this->owner->notifications()->count())->toBe(1);

        $comment->delete();

        expect($this->owner->notifications()->count())->toBe(0);
    });

    test('read notification is kept when comment is deleted', function () {
        $commenter = User::factory()->create();

        $comment = createComment($commenter, $this->ranking);

        $this->owner->notifications()->first()->markAsRead();

        $comment->delete();

        expect($this->owner->notifications()->count())->toBe(1);
    });
});

function createComment(User $commenter, Ranking $ranking, string $text = 'Great ranking!'): Comment
{
    return Comment::create([
        'commentable_type' => Ranking::class,
        'commentable_id' => $ranking->getKey(),
        'commentator_type' => User::class,
        'commentator_id' => $commenter->getKey(),
        'original_text' => $text,
        'text' => $text,
        'approved_at' => now(),
    ]);
}
