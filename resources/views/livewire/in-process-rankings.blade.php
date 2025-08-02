<div>
    @if (count($rankings))
        <div class="bg-white shadow-md rounded-lg p-2">
            <h5 class="md:text-md mt-2 mb-4 px-2">
                Pick up where you left off
            </h5>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($rankings as $ranking)
                    <div class="border rounded-xl">
                        <x-ranking-card :ranking="$ranking" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
