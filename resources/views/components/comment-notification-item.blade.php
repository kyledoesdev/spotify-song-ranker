@props([
    'notification',
])

<div class="flex items-start gap-3">
    @if ($notification->avatar)
        <img
            src="{{ $notification->avatar }}"
            alt="{{ $notification->user_name }}"
            class="h-8 w-8 rounded-full shrink-0"
        />
    @endif

    <div class="flex-1 min-w-0">
        <p class="text-sm text-zinc-800">{{ $notification->description() }}</p>
        @if ($notification->message)
            <p class="text-xs text-zinc-500 mt-1 italic truncate">
                "{{ str($notification->message)->limit(100) }}"
            </p>
        @endif
        <p class="text-xs text-zinc-400 mt-1">
            {{ $notification->created_at->diffForHumans() }}
        </p>
    </div>
</div>
