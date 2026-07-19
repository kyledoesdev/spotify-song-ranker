<div>
    <div class="min-h-screen py-4">
        <div class="w-full max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
                <x-leaderboards.section
                    title="Top Ranked Artists"
                    icon="fa-music"
                    :entries="$topArtists"
                    count-label="rankings"
                />

                <x-leaderboards.section
                    title="Top Creators"
                    icon="fa-user"
                    :entries="$topCreators"
                    count-label="rankings"
                />

                <x-leaderboards.section
                    title="Rankings with Most Songs"
                    icon="fa-list-ol"
                    :entries="$biggestRankings"
                    count-label="songs"
                />
            </div>
        </div>
    </div>
</div>
