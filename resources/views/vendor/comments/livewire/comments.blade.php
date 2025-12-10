@php
    use Spatie\Comments\Enums\NotificationSubscriptionType;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Gate;
@endphp

<section class="comments {{ $newestFirst ? 'comments-newest-first' : '' }}">
    <header class="comments-header">
        @if($writable && $showNotificationOptions && Auth::check())
            <div x-data="{ subscriptionsOpen: false}" class="comments-subscription">
                <button @click.prevent="subscriptionsOpen = true" class="comments-subscription-trigger">
                    {{ NotificationSubscriptionType::from($selectedNotificationSubscriptionType)->longDescription() }}
                </button>
                <x-comments::modal
                    bottom
                    compact
                    x-show="subscriptionsOpen"
                    @click.outside="subscriptionsOpen = false"
                >
                    @foreach(NotificationSubscriptionType::cases() as $case)
                        <button class="comments-subscription-item" @click="subscriptionsOpen = false" wire:click="updateSelectedNotificationSubscriptionType('{{ $case->value }}')">
                            {{ $case->description() }}
                        </button>
                    @endforeach
                </x-comments::modal>
            </div>
        @endif
    </header>

    @if ($newestFirst)
        @include('comments::livewire.partials.newComment')
    @endif

    @forelse($this->comments as $comment)
        @continue(! Gate::check('see', $comment))
        <livewire:comments-comment
            :key="$comment->id"
            :comment="$comment"
            :show-avatar="$showAvatars"
            :newest-first="$newestFirst"
            :writable="$writable"
            :show-replies="$showReplies"
            :show-reactions="$showReactions"
        />
    @empty
        <p class="comments-no-comment-yet">{{ $noCommentsText ?? __('comments::comments.no_comments_yet') }}</p>
    @endforelse

    @if ($this->comments->hasPages())
        {{ $this->comments->links() }}
    @endif

    @if (! $newestFirst)
        @include('comments::livewire.partials.newComment')
    @endif
</section>
