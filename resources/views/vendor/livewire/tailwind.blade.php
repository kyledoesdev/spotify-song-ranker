@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="sticky bottom-0 bg-zinc-100 hover:shadow-lg transition-shadow py-4 shadow-md rounded-lg">
            <!-- Mobile Pagination -->
            <div class="sm:hidden">
                <div class="w-full overflow-x-auto">
                    <ul class="flex justify-center items-center space-x-2 py-3 min-w-max px-4">
                        <!-- First Button -->
                        <li>
                            @if ($paginator->onFirstPage())
                                <span class="px-2 py-2 rounded-md text-zinc-800 cursor-not-allowed opacity-50">
                                    <i class="fa fa-solid fa-angles-left"></i>
                                </span>
                            @else
                                <button 
                                    type="button" 
                                    wire:click="gotoPage(1, '{{ $paginator->getPageName() }}')" 
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}" 
                                    class="px-2 py-2 rounded-md text-zinc-800 cursor-pointer hover:bg-gray-100"
                                    title="First Page"
                                >
                                    <i class="fa fa-solid fa-angles-left"></i>
                                </button>
                            @endif
                        </li>

                        <!-- Previous Button -->
                        <li>
                            @if ($paginator->onFirstPage())
                                <span class="px-3 py-2 rounded-md text-zinc-800 cursor-not-allowed opacity-50">
                                    <i class="fa fa-solid fa-arrow-left-long"></i>
                                </span>
                            @else
                                <button 
                                    type="button" 
                                    wire:click="previousPage('{{ $paginator->getPageName() }}')" 
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}" 
                                    class="px-3 py-2 rounded-md text-zinc-800 cursor-pointer hover:bg-gray-100"
                                >
                                    <i class="fa fa-solid fa-arrow-left-long"></i>
                                </button>
                            @endif
                        </li>

                        <!-- Page Numbers -->
                        @foreach(range(max(1, $paginator->currentPage() - 1), min($paginator->lastPage(), $paginator->currentPage() + 1)) as $page)
                            <li wire:key="mobile-page-{{ $page }}">
                                @if ($page == $paginator->currentPage())
                                    <span class="px-3 py-2 text-zinc-800 k-line bg-blue-100 border border-blue-300 rounded-md shadow-sm">
                                        {{ $page }}
                                    </span>
                                @else
                                    <button 
                                        type="button" 
                                        wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" 
                                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                        class="px-3 py-2 text-zinc-800 cursor-pointer hover:bg-gray-100 rounded-md"
                                    >
                                        {{ $page }}
                                    </button>
                                @endif
                            </li>
                        @endforeach

                        <!-- Next Button -->
                        <li>
                            @if ($paginator->hasMorePages())
                                <button 
                                    type="button" 
                                    wire:click="nextPage('{{ $paginator->getPageName() }}')" 
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                    class="px-3 py-2 rounded-md text-zinc-800 cursor-pointer hover:bg-gray-100"
                                >
                                    <i class="fa fa-solid fa-arrow-right-long"></i>
                                </button>
                            @else
                                <span class="px-3 py-2 rounded-md text-zinc-800 cursor-not-allowed opacity-50">
                                    <i class="fa fa-solid fa-arrow-right-long"></i>
                                </span>
                            @endif
                        </li>

                        <!-- Last Button -->
                        <li>
                            @if ($paginator->currentPage() == $paginator->lastPage())
                                <span class="px-2 py-2 rounded-md text-zinc-800 cursor-not-allowed opacity-50">
                                    <i class="fa fa-solid fa-angles-right"></i>
                                </span>
                            @else
                                <button 
                                    type="button" 
                                    wire:click="gotoPage({{ $paginator->lastPage() }}, '{{ $paginator->getPageName() }}')" 
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                    class="px-2 py-2 rounded-md text-zinc-800 cursor-pointer hover:bg-gray-100"
                                    title="Last Page"
                                >
                                    <i class="fa fa-solid fa-angles-right"></i>
                                </button>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Desktop Pagination -->
            <div class="hidden sm:flex sm:flex-col">
                <div class="mb-4">
                    <p class="text-sm text-zinc-800 leading-5 text-center">
                        <span>Showing</span>
                        <span class="font-medium">{{ $paginator->firstItem() }}</span>
                        <span>to</span>
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                        <span>of</span>
                        <span class="font-medium">{{ $paginator->total() }}</span>
                        <span>results</span>
                    </p>
                </div>

                <div class="w-full overflow-x-auto">
                    <ul class="flex justify-center items-center space-x-2 py-3 min-w-max px-4">
                        <!-- First Button -->
                        <li>
                            @if ($paginator->onFirstPage())
                                <span class="px-2 py-2 rounded-md text-zinc-800 cursor-not-allowed opacity-50">
                                    <i class="fa fa-solid fa-angles-left"></i>
                                </span>
                            @else
                                <button 
                                    type="button" 
                                    wire:click="gotoPage(1, '{{ $paginator->getPageName() }}')" 
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                    class="px-2 py-2 rounded-md text-zinc-800 cursor-pointer hover:bg-gray-100"
                                    title="First Page"
                                >
                                    <i class="fa fa-solid fa-angles-left"></i>
                                </button>
                            @endif
                        </li>

                        <!-- Previous Button -->
                        <li>
                            @if ($paginator->onFirstPage())
                                <span class="px-3 py-2 rounded-md text-zinc-800 cursor-not-allowed opacity-50">
                                    <i class="fa fa-solid fa-arrow-left-long"></i>
                                </span>
                            @else
                                <button 
                                    type="button" 
                                    wire:click="previousPage('{{ $paginator->getPageName() }}')" 
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}" 
                                    class="px-3 py-2 rounded-md text-zinc-800 cursor-pointer hover:bg-gray-100"
                                >
                                    <i class="fa fa-solid fa-arrow-left-long"></i>
                                </button>
                            @endif
                        </li>

                        <!-- First Page -->
                        @if ($paginator->currentPage() > 3)
                            <li>
                                <button 
                                    type="button" 
                                    wire:click="gotoPage(1, '{{ $paginator->getPageName() }}')" 
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                    class="px-3 py-2 text-zinc-800 cursor-pointer hover:bg-gray-100 rounded-md"
                                >
                                    1
                                </button>
                            </li>
                        @endif

                        <!-- Ellipsis -->
                        @if ($paginator->currentPage() > 3)
                            <li>
                                <span class="px-2">...</span>
                            </li>
                        @endif

                        <!-- Page Numbers -->
                        @foreach(range(max(1, $paginator->currentPage() - 1), min($paginator->lastPage(), $paginator->currentPage() + 1)) as $page)
                            <li wire:key="desktop-page-{{ $page }}">
                                @if ($page == $paginator->currentPage())
                                    <span class="px-3 py-2 text-zinc-800 bg-purple-100 border border-purple-700 rounded-md shadow-sm cursor-pointer">
                                        {{ $page }}
                                    </span>
                                @else
                                    <button 
                                        type="button" 
                                        wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" 
                                        x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                        class="px-3 py-2 text-zinc-800 cursor-pointer hover:bg-gray-100 rounded-md"
                                    >
                                        {{ $page }}
                                    </button>
                                @endif
                            </li>
                        @endforeach

                        <!-- Ellipsis -->
                        @if ($paginator->currentPage() < $paginator->lastPage() - 2)
                            <li>
                                <span class="px-2">...</span>
                            </li>
                        @endif

                        <!-- Last Page -->
                        @if ($paginator->currentPage() < $paginator->lastPage() - 2)
                            <li>
                                <button 
                                    type="button" 
                                    wire:click="gotoPage({{ $paginator->lastPage() }}, '{{ $paginator->getPageName() }}')" 
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                    class="px-3 py-2 text-zinc-800 cursor-pointer hover:bg-gray-100 rounded-md"
                                >
                                    {{ $paginator->lastPage() }}
                                </button>
                            </li>
                        @endif

                        <!-- Next Button -->
                        <li>
                            @if ($paginator->hasMorePages())
                                <button 
                                    type="button" 
                                    wire:click="nextPage('{{ $paginator->getPageName() }}')" 
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                    class="px-3 py-2 rounded-md text-zinc-800 cursor-pointer hover:bg-gray-100"
                                >
                                    <i class="fa fa-solid fa-arrow-right-long"></i>
                                </button>
                            @else
                                <span class="px-3 py-2 rounded-md text-zinc-800 cursor-not-allowed opacity-50">
                                    <i class="fa fa-solid fa-arrow-right-long"></i>
                                </span>
                            @endif
                        </li>

                        <!-- Last Button -->
                        <li>
                            @if ($paginator->currentPage() == $paginator->lastPage())
                                <span class="px-2 py-2 rounded-md text-zinc-800 cursor-not-allowed opacity-50">
                                    <i class="fa fa-solid fa-angles-right"></i>
                                </span>
                            @else
                                <button 
                                    type="button" 
                                    wire:click="gotoPage({{ $paginator->lastPage() }}, '{{ $paginator->getPageName() }}')" 
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                    class="px-2 py-2 rounded-md text-zinc-800 cursor-pointer hover:bg-gray-100"
                                    title="Last Page"
                                >
                                    <i class="fa fa-solid fa-angles-right"></i>
                                </button>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    @endif
</div>