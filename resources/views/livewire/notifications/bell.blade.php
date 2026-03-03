<div
    x-data="{
        open: false,
        toggle() {
            if (!this.open) {
                $wire.open()
            }
            this.open = !this.open
        },
        close() {
            this.open = false
        }
    }"
    x-on:keydown.escape.prevent.stop="close()"
    class="relative"
>
    <button
        x-on:click="toggle()"
        type="button"
        class="notification-bell-button relative"
        aria-label="Notifications"
    >
        <i class="fa fa-bell fa-lg text-zinc-600 hover:text-zinc-800 transition-colors"></i>
        @if ($unreadCount > 0)
            <span class="notification-bell-badge">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-on:click.outside="close()"
        style="display: none;"
        class="notification-bell-popover"
    >
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-zinc-800">Notifications</h3>
        </div>

        <div class="notification-bell-list">
            @forelse ($notifications as $notification)
                <x-notification-item :notification="$notification" />
            @empty
                <div class="px-4 py-8 text-center">
                    <i class="fa fa-bell-slash text-zinc-300 text-2xl mb-2"></i>
                    <p class="text-sm text-zinc-400">No notifications yet</p>
                </div>
            @endforelse
        </div>

        <div class="border-t border-gray-100 px-4 py-3">
            <a
                href="{{ route('notifications') }}"
                class="flex items-center justify-center text-sm font-medium text-purple-600 hover:text-purple-800 transition-colors"
            >
                View All
            </a>
        </div>
    </div>
</div>
