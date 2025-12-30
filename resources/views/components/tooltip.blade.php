@props([
    'text',
    'icon' => 'fas fa-question-circle',
])

<div 
    class="inline-flex relative align-middle"
    x-data="{ show: false }"
    wire:ignore.self
>
    <button
        type="button"
        class="{{ $icon }} cursor-pointer text-slate-600 hover:text-purple-400 transition-colors duration-200 bg-transparent border-0 p-0"
        x-on:click="show = !show"
        x-on:click.outside="show = false"
    ></button>
    
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        style="display: none;"
        class="absolute z-20 w-64 p-3 text-sm rounded-xl shadow-lg border
               bg-white text-slate-700 border-slate-200
               top-full left-1/2 -translate-x-1/2 mt-2
               md:top-1/2 md:left-full md:-translate-y-1/2 md:translate-x-0 md:ml-2 md:mt-0"
    >
        <div class="flex items-start gap-2">
            <i class="fas fa-info-circle text-purple-400 mt-0.5 shrink-0"></i>
            <span>{{ $text }}</span>
        </div>
        
        <div class="absolute border-8 border-transparent
                    bottom-full left-1/2 -translate-x-1/2 border-b-white
                    md:left-0 md:top-1/2 md:bottom-auto md:-translate-y-1/2 md:translate-x-0 md:-ml-4 md:border-b-transparent md:border-r-white">
        </div>
    </div>
</div>