<div
    x-data="{ removing: false }"
    x-show="!removing"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-95 translate-x-[-20px]"
    class="m-2 mb-2 transition-all duration-300 hover:bg-gray-50 rounded-lg"
>
    <div class="flex">
        <div>
            <img 
                src="{{ $song['cover'] }}" 
                alt="{{ $song['name'] }}"
                class="w-12 h-12 sm:w-[72px] sm:h-[72px] rounded-xl mr-4 transition-transform hover:scale-105" 
            />
        </div>
        <div class="flex flex-1">
            <div class="min-w-0 pt-1 flex-1">
                <h5 class="mx-2 mt-1 mb-0.5 text-xs sm:text-base break-words" title="{{ $song['name'] }}">
                    {{ $song['name'] }}
                </h5>
                <div class="flex items-center mx-2">
                    <x-spotify-logo :song="$song['id']" />

                    @if ($canDelete)
                        <button 
                            type="button" 
                            class="text-gray-500 hover:text-red-600 transition-all duration-200 p-1 text-sm ml-2 cursor-pointer transform hover:scale-110" 
                            x-on:click="removing = true; setTimeout(() => { $wire.removeSong('{{ $song['id'] }}') }, 300)"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50"
                            title="Remove song"
                        >
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>