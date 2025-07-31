<div>
    <div class="bg-white rounded-lg p-2 mt-4">
        {!! Str::of($aboutPage)->markdown(); !!}
    </div>

    <div class="mt-4 bg-white border-zinc-800 rounded-lg p-4">
        <h5>webring:</h5>
        <div class="flex">
            <a href="https://insect.christmas" target="_blank">
                <img class="mx-1" src="/insect.gif" />
            </a>
            <a class="portfolio-webring" href="https://kyledoes.dev" target="_blank">
                <span class="text-white" style="font-size: 12px;">my portfolio</span>
            </a>
        </div>
    </div>

    <style>
        .portfolio-webring {
            background: linear-gradient(45deg, #00E5FF 25%, #1200FF);
            background-size: 400% 400%;
            animation: webring 5s linear infinite;
            min-width: 88px; 
            max-width: 88px; 
            min-height: 31px; 
            max-height: 31px; 
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid black;
        }
        
        @keyframes webring {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }
    </style>
</div>
