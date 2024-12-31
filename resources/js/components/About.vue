<template>
    <div class="bg-white border border-zinc-800 rounded-lg p-4">
        <p class="p-2">
            Welcome to songrank.dev. This application will allow you to log in with your
            spotify account and rank your favorite artists' entire discography. This is not a bracket style ranking system.
            I've built a user-driven merge-sort algorithm to rank every possible combination of tracks as efficently as possible.
        </p>
        <p class="p-2">
            This application isn't limited to an artists' entire discography either, you can rank indivdual albums, a set
            of albums, just singles, etc. You can filter down your favorite artists' songs to just the ones you want to rank.
        </p>
        <p class="p-2">
            After you've finished a ranking, feel free to share your finalized list with friends, download the list & more.
        </p>
        <hr />
        <p class="p-2 italic text-xs mt-2">
            While completly functional on mobile devices, for the best experience, use this website on your computer.
        </p>
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
</template>

<script>
    export default {
        'name': 'About Page',
    }
</script>
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