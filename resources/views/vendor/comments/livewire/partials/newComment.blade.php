@if($writable && \Illuminate\Support\Facades\Gate::check('createComment', $model))
    <div class="comments-form">
        @if($showAvatars)
            <x-comments::avatar/>
        @endif
        <form
            class="comments-form-inner"
            wire:submit.prevent="comment"
            wire:keydown.cmd.enter="comment"
            wire:keydown.ctrl.enter="comment"
        >
            <x-dynamic-component
                :component="\Spatie\LivewireComments\Support\Config::editor()"
                model="text"
                :commentable="$model"
                :placeholder="__('comments::comments.write_comment')"
                wire:key="editor-new"
            />
            @error('text')
            <p class="comments-error">
                {{ $message }}
            </p>
            @enderror
            <x-comments::button submit>
                {{ __('comments::comments.create_comment') }}
            </x-comments::button>
        </form>
    </div>
@endif
