<?php

use App\Actions\Comments\FilterCommentProfanityAction;
use App\Models\User;
use Spatie\Comments\Models\Comment;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

describe('ranking comments', function () {
    test('displays comments component when comments are enabled', function () {
        $user = User::factory()->createOne();

        $ranking = publicCompletedRanking(attributes: [
            'user_id' => $user->getKey(),
            'comments_enabled' => true,
        ]);

        actingAs($user)
            ->get(route('ranking', ['id' => $ranking->getKey()]))
            ->assertOk()
            ->assertSeeLivewire('comments');
    });

    test('hides comments component when comments are disabled', function () {
        $user = User::factory()->createOne();

        $ranking = publicCompletedRanking(attributes: [
            'user_id' => $user->getKey(),
            'comments_enabled' => false,
        ]);

        actingAs($user)
            ->get(route('ranking', ['id' => $ranking->getKey()]))
            ->assertOk()
            ->assertDontSeeLivewire('comments');
    });
});

describe('comment profanity filtering', function () {
    test('masks profanity in the comment text', function () {
        $comment = profanityFilteredComment('this is a fucking test');

        expect($comment->text)->toBe('this is a ******* test');
    });

    test('leaves clean text unchanged', function () {
        $comment = profanityFilteredComment('this is a perfectly fine test');

        expect($comment->text)->toBe('this is a perfectly fine test');
    });

    test('preserves the original text alongside the masked text', function () {
        $comment = profanityFilteredComment('this is a fucking test');

        expect($comment->original_text)->toBe('this is a fucking test');
    });
});

describe('ranking comment replies', function () {
    test('allows replies when comment replies are enabled', function () {
        $ranking = publicCompletedRanking(attributes: [
            'comments_enabled' => true,
            'comments_replies_enabled' => true,
        ]);

        get(route('ranking', ['id' => $ranking->getKey()]))
            ->assertOk()
            ->assertSee('&quot;showReplies&quot;:true', false);
    });

    test('disables replies when comment replies are disabled', function () {
        $ranking = publicCompletedRanking(attributes: [
            'comments_enabled' => true,
            'comments_replies_enabled' => false,
        ]);

        get(route('ranking', ['id' => $ranking->getKey()]))
            ->assertOk()
            ->assertSee('&quot;showReplies&quot;:false', false);
    });
});

function profanityFilteredComment(string $text): Comment
{
    $comment = new Comment;
    $comment->original_text = $text;

    (new FilterCommentProfanityAction)->handle($comment);

    return $comment;
}
