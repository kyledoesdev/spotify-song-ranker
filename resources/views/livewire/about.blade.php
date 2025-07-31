<div>
    <div class="bg-white rounded-lg p-4 mt-4">
        <article class="
            prose prose-img:rounded-xl prose-slate
            prose-headings:underline
            prose-a:text-blue-600
            max-w-full text-base
        ">
            {!! Str::of($aboutPage)->markdown() !!}
        </article>
    </div>

    <div class="mt-4 bg-white border-zinc-800 rounded-lg p-4">
        <h5>webring:</h5>
        <div class="flex">
            <a href="https://insect.christmas" target="_blank">
                <img class="mx-1" src="/insect.gif" />
            </a>
            <a class="portfolio-webring" href="https://kyledoes.dev" target="_blank">
                <span class="text-dark" style="font-size: 12px;">my portfolio</span>
            </a>
        </div>
    </div>
</div>
