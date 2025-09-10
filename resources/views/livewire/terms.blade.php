<div>
    <div class="flex justify-center">
        <div class="flex bg-white justify-center rounded-xl mt-8 overflow-y-auto max-h-175">
            <article class="
                prose prose-img:rounded-xl
                prose-headings:underline
                prose-a:text-blue-600
                p-4
            ">
                {!! Str::of($content)->markdown() !!}
            </article>
        </div>
    </div>
</div>
