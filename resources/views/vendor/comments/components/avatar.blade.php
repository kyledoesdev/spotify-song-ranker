@php use Spatie\Comments\Models\Concerns\Interfaces\CanComment;use Spatie\Comments\Support\CommentatorProperties; @endphp
@props([
    'comment' => null,
])
@php
    /** @var ?CommentatorProperties $commentatorProperties */
    $commentatorProperties = $comment?->commentatorProperties();

    if (! $commentatorProperties && auth()->user() instanceof CanComment) {
        $commentatorProperties = auth()->user()->commentatorProperties();
    }

    $avatar = $commentatorProperties?->avatar;

    if (! $avatar) {
        $defaultImage = Spatie\LivewireComments\Support\Config::gravatarDefaultImage();
        $avatar = "https://www.gravatar.com/avatar/unknown?d={$defaultImage}";
    }
@endphp

<img
    class="comments-avatar"
    src="{{ $avatar }}"
    alt="{{ trim("{$commentatorProperties?->name} avatar") }}"
>
