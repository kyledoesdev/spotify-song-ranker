@props(['disabled' => false])

<label class="inline-flex items-center gap-2 {{ $disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer' }}">
    <span class="relative inline-flex shrink-0 items-center">
        <input
            type="checkbox"
            class="peer sr-only"
            @disabled($disabled)
            {{ $attributes }}
        />
        <span class="w-9 h-5 rounded-full bg-zinc-300 transition-colors duration-300 peer-checked:bg-purple-400 peer-focus-visible:ring-2 peer-focus-visible:ring-blue-400"></span>
        <span class="absolute left-0.5 w-4 h-4 rounded-full bg-white shadow-sm transition-transform duration-300 peer-checked:translate-x-4"></span>
    </span>

    @if ($slot->isNotEmpty())
        <span class="text-sm">{{ $slot }}</span>
    @endif
</label>
