@props([
    'submit' => false,
    'small' => false,
    'danger' => false,
    'link' => false,
])
<button
    type="{{ $submit ? 'submit' : 'button' }}"
    @class([
        'comments-button',
        'is-small' => $small,
        'is-danger' => $danger,
        'is-link' => $link,
    ])
    {{ $attributes->except('type', 'size', 'submit', 'link') }}
>
    {{ $slot }}
</button>
