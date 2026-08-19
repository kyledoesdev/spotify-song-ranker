@props(['content'])

<section class="pt-4 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg p-6">
            <article class="
                prose prose-slate max-w-full text-base
                prose-headings:mt-0
                prose-a:text-blue-600
                prose-img:rounded-xl
            ">
                {!! $content !!}
            </article>
        </div>
    </div>
</section>
