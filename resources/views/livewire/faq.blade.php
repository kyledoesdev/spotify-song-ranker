<div class="flex mt-4">
    <div class="bg-white rounded-lg p-6 w-full">
        <h1 class="text-2xl font-bold mb-4 text-center">Frequently Asked Questions</h1>

        @forelse ($this->faqs as $faq)
            <div
                x-data="{ open: false }"
                class="border-b border-gray-200 last:border-b-0"
                wire:key="faq-{{ $faq->id }}"
            >
                <button
                    @click="open = !open"
                    class="flex w-full cursor-pointer items-center justify-between py-4 text-left font-medium text-gray-900 hover:text-purple-500 transition-colors"
                >
                    <span>{{ $faq->question }}</span>
                    <svg
                        class="h-5 w-5 shrink-0 transition-transform duration-200"
                        :class="{ 'rotate-180': open }"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div
                    x-show="open"
                    x-collapse
                    class="pb-4"
                >
                    <article class="prose prose-slate prose-a:text-blue-600 max-w-full text-base">
                        {!! $faq->text !!}
                    </article>
                </div>
            </div>
        @empty
            <p class="text-gray-500">No FAQs available at this time.</p>
        @endforelse
    </div>
</div>
