@php
    use Illuminate\Support\Collection;use Spatie\Comments\Models\Concerns\Interfaces\CanComment;use Spatie\LivewireComments\Support\Config;

    /** @var Collection<CanComment> $results */
@endphp
<div>
    <div x-data="{ activeItem: 0, results: $wire.entangle('results') }">
        @empty($results)
            <div class="no-results">No results</div>
        @endempty
        <ul
            @auto-complete-down.window="activeItem = Math.min(activeItem + 1, results.length - 1);"
            @auto-complete-up.window="activeItem = Math.max(activeItem - 1, 0)"
            @auto-complete-enter.window="$wire.select(results[activeItem].id, results[activeItem].name)"
            @mention-autocomplete-search.window="activeItem = 0;"
        >
            @foreach($results as $i => $result)
                <li
                    class="result"
                    x-bind:class="{{ $i }} === activeItem ? 'active' : ''"
                    wire:click="select('{{ $result['id'] }}', '{{ $result['name'] }}')"
                >
                    @if(Config::showAvatarsInMentionsAutocomplete())
                        <img
                            src="{{ $result['avatar'] }}"
                            alt="{{ $result['name'] }} avatar"
                            class="avatar"
                        >
                    @endif
                    {{ $result['name'] }}
                </li>
            @endforeach
        </ul>
    </div>
</div>
