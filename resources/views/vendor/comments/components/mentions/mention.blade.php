@if($mentionee !== null && $url = $mentionee->commentatorProperties()?->url)
    <a class="mention a" href="{{ $url }}">
        {{ '@' . $mentionee->commentatorProperties()->name }}
    </a>
@else
    @if ($mentionee !== null)
        <span class="mention">{{ '@' . $mentionee?->commentatorProperties()?->name ?? __('comments::comments.guest')  }}</span>
    @else
        <span class="mention">{{ '@' . __('comments::comments.deleted_user')  }}</span>
    @endif
@endif
