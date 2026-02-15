<div>
    <div class="bg-white rounded-lg p-4 mt-4">
        <article class="
            prose prose-img:rounded-xl prose-slate
            prose-headings:underline
            prose-a:text-blue-600
            max-w-full text-base
        ">
            {!! Str::of($content)->markdown() !!}
        </article>
    </div>
</div>
