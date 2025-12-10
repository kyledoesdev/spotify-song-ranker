@props([
    'comment' => null,
    'placeholder' => '',
    'model',
    'autofocus' => false,
])
<textarea
    wire:model="{{ $model }}"
    @if($autofocus)
        x-data
        x-on:open-editor-{{ $comment->id }}.window="$nextTick(() => $el.focus())"
    @endif
    class="comments-textarea"
    placeholder="{{ $placeholder }}"
></textarea>
