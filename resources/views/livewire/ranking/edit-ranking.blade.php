<div>
    <div class="bg-white rounded-lg shadow-md mt-4 space-y-6" style="min-height: 300px;">
        <div class="flex justify-between mt-2">
            <div>
                <h5 class="md:text-xl mt-3 mx-3">Edit your ranking</h5>
            </div>
            <div class="mr-4">
                <button 
                    type="button" 
                    class="btn-primary" 
                    wire:click="update"
                >
                    <i class="fa fa-solid fa-check"></i>
                </button>
                <button 
                    type="button" 
                    class="btn-secondary"
                    onclick="history.back()"
                >
                    <i class="fa fa-solid fa-times"></i>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:max-w-1/2 mx-3">
            <div class="mb-4">
                <label class="block mb-2">Ranking Name?</label>
                <input
                    type="text"
                    class="w-full bg-zinc-100 rounded-lg p-2"
                    wire:model.live.debounce.500ms="form.name"
                    maxlength="30"
                />
            </div>

            <div class="mb-4">
                <label class="block mb-2 transition-all duration-300">Show In Explore Feed?</label>
                <select
                    class="w-full bg-zinc-100 rounded-lg p-2"
                    wire:model.live.debounce.500ms="form.is_public"
                    required
                >
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block mb-2 transition-all duration-300">Comments Enabled?</label>
                <select
                    class="w-full bg-zinc-100 rounded-lg p-2"
                    wire:model.live.debounce.500ms="form.comments_enabled"
                    required
                >
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block mb-2 transition-all duration-300">Comment Replies Enabled?</label>
                <select
                    class="w-full bg-zinc-100 rounded-lg p-2"
                    wire:model.live.debounce.500ms="form.comments_replies_enabled"
                    required
                >
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
        </div>
    </div>
</div>
