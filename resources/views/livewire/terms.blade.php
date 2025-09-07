<div>
    <div class="d-flex bg-white justify-center rounded-xl mt-8">
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
