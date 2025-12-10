<?php

use App\Models\User;
use Spatie\Comments\Models\Comment;
use Spatie\Comments\Models\Reaction;
use Spatie\Comments\Support\CommentSanitizer;
use Spatie\Comments\Actions\RejectCommentAction;
use Spatie\Comments\Actions\ApproveCommentAction;
use Spatie\Comments\Actions\ProcessCommentAction;
use Spatie\Comments\Models\CommentNotificationSubscription;
use Spatie\Comments\CommentTransformers\MentionsTransformer;
use Spatie\Comments\Notifications\PendingCommentNotification;
use Spatie\Comments\Notifications\ApprovedCommentNotification;
use App\Actions\Comments\CustomResolveMentionsAutocompleteAction;
use Spatie\Comments\CommentTransformers\MarkdownToHtmlTransformer;
use Spatie\Comments\Actions\SendNotificationsForPendingCommentAction;
use Spatie\Comments\Actions\SendNotificationsForApprovedCommentAction;

return [
    /*
     * These are the reactions that can be made on a comment. We highly recommend
     * only enabling positive ones for getting good vibes in your community.
     */
    'allowed_reactions' => ['👍', '👎', '🤝', '👀', '🎉', '❤️', '💅', '🔥', '💩', '🎵'],

    /*
     * You can allow guests to post comments. They will not be able to use
     * reactions.
     */
    'allow_anonymous_comments' => false,

    /*
     * A comment transformer is a class that will transform the comment text
     * for example from Markdown to HTML
     */
    'comment_transformers' => [
        MarkdownToHtmlTransformer::class,
        MentionsTransformer::class,
    ],

    /*
     * After all transformers have transformed the comment text, it will
     * be passed to this class to sanitize it
     */
    'comment_sanitizer' => CommentSanitizer::class,

    /*
     * These attributes will be allowed in the comment text. All other
     * attributes will be removed by the comment sanitizer.
     */
    'allowed_attributes' => [
        // enabling this could allow for CSS clickjacking attacks:
        // https://github.com/spatie/laravel-comments/pull/182#issuecomment-2090665892

        // 'style' => '*',
    ],

    /*
     * Comments need to be approved before they are shown. You can opt
     * to have all comments to be approved automatically.
     */
    'automatically_approve_all_comments' => true,

    'models' => [
        /*
         * The class that will comment on other things. Typically, this
         * would be a user model.
         */
        'commentator' => User::class,

        /*
         * The field to use to display the name of the commentator model.
         */
        'name' => 'name',

        /*
         * The field to use to display the avatar of the commentator model.
         */
        'avatar' => 'avatar',

        /*
         * The model you want to use as a Comment model. It needs to be or
         * extend the `Spatie\Comments\Models\Comment::class` model.
         */
        'comment' => Comment::class,

        /*
         * The model you want to use as a React model. It needs to be or
         * extend the `Spatie\Comments\Models\Reaction::class` model.
         */
        'reaction' => Reaction::class,

        /*
         * The model you want to use as an subscription model. It needs to be or
         * extend the `Spatie\Comments\Models\CommentNotificationSubscription::class` model.
         */
        'comment_notification_subscription' => CommentNotificationSubscription::class,
    ],

    'notifications' => [
        /*
         * When somebody creates a comment, a notification will be sent to other persons
         * that commented on the same thing.
         */
        'enabled' => true,

        'notifications' => [
            'pending_comment' => PendingCommentNotification::class,
            'approved_comment' => ApprovedCommentNotification::class,
        ],

        /*
         * Here you can configure the notifications that will be sent via mail.
         */
        'mail' => [
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'name' => env('MAIL_FROM_NAME', 'Example'),
            ],
        ],
    ],

    /*
     * Unless you need fine-grained customization, you don't need to change
     * these action classes. If you do change any of them, make sure that your class
     * extends the original action class.
     */
    'actions' => [
        'process_comment' => ProcessCommentAction::class,
        'send_notifications_for_pending_comment' => SendNotificationsForPendingCommentAction::class,
        'approve_comment' => ApproveCommentAction::class,
        'reject_comment' => RejectCommentAction::class,
        'resolve_mentions_autocomplete' => CustomResolveMentionsAutocompleteAction::class,
        'send_notifications_for_approved_comment' => SendNotificationsForApprovedCommentAction::class,
    ],

    'gravatar' => [
        /*
         * Here you can choose which default image to show when a user does not have a Gravatar profile.
         * See the Gravatar docs for further information https://en.gravatar.com/site/implement/images/
         */
        'default_image' => 'mp',
    ],

    'mentions' => [
        'enabled' => false,
    ],

    'ui' => [
        'editor' => 'comments::editors.textarea',
    ],
];
