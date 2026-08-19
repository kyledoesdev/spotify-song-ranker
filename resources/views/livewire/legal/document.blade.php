<div class="mt-4">
    @if (blank($content))
        <div class="bg-white rounded-lg p-6 text-center">
            <h1 class="text-2xl font-bold mb-2">{{ $type->label() }}</h1>
            <p class="text-gray-500">This document has not been published yet. Please check back soon.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-[16rem_minmax(0,1fr)] gap-4 items-start">
            {{-- Table of contents: collapsible on mobile, sticky alongside the document on desktop --}}
            <aside x-data="{ open: false }" class="lg:sticky lg:top-4">
                <nav class="bg-white rounded-lg p-4">
                    <button
                        type="button"
                        @click="open = !open"
                        class="flex w-full cursor-pointer items-center justify-between lg:cursor-default"
                    >
                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">Contents</span>
                        <svg
                            class="h-4 w-4 shrink-0 text-gray-400 transition-transform duration-200 lg:hidden"
                            :class="{ 'rotate-180': open }"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <ol x-show="open" class="mt-3 space-y-1 lg:block!">
                        @foreach ($sections as $section)
                            <li>
                                <a
                                    href="#{{ $section['id'] }}"
                                    class="block py-1 text-sm text-gray-600 hover:text-purple-500 transition-colors"
                                >
                                    {{ $section['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </nav>
            </aside>

            <div class="bg-white rounded-lg p-6">
                <h1 class="text-2xl font-bold text-center">{{ $type->label() }}</h1>

                @if ($document?->effective_at)
                    <p class="mt-1 mb-6 text-center text-sm text-gray-500">
                        Last updated {{ $document->effective_at->format('F j, Y') }}
                    </p>
                @endif

                <article class="prose prose-slate prose-a:text-blue-600 prose-h2:scroll-mt-4 max-w-full text-base">
                    {!! $content !!}
                </article>
            </div>
        </div>
    @endif
</div>
