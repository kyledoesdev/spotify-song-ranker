@props([
    'notification',
    'showViewBtn' => true,
])

<div class="notification-bell-item bg-purple-50/50">
    @if ($notification->isCommentNotification())
        <x-comment-notification-item :notification="$notification" />
    @else
        <div class="flex-1 min-w-0">
            <p class="text-sm text-zinc-800">{{ $notification->description() }}</p>
            <p class="text-xs text-zinc-400 mt-1">
                {{ $notification->created_at->diffForHumans() }}
            </p>
        </div>
    @endif

    <div class="flex items-center gap-2 mt-2">
        @if ($showViewBtn && $notification->actionUrl())
            <a
                href="{{ $notification->actionUrl() }}"
                class="btn-primary text-xs"
            >
                View
            </a>
        @endif
    </div>
</div>
