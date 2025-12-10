@props([
    'compact' => false,
    'left' => false,
    'bottom' => false,
    'title' => '',
])
<div
    x-cloak
    @class([
        'comments-modal',
        'is-compact' => $compact,
        'is-left' => $left,
        'is-bottom' => $bottom
    ])
    {{ $attributes->except(['compact', 'left', 'bottom']) }}
>
    @if($title)
        <p class="comments-modal-title">
            {{ $title }}
        </p>
    @endif
    <div class="comments-modal-contents">
        {{ $slot }}
    </div>
</div>
