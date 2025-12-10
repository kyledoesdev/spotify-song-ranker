<div>
    @props([
        'comment' => null,
        'placeholder' => '',
        'model',
        'autofocus' => false,
        'commentable' => null,
        'editing' => false
    ])
    <div x-data="{ text: $wire.$entangle('{{$model}}'), isEditing: @js($editing) }">
        <div
            x-data="markdownEditor_{{$comment?->id ?? 'new'}}"
            @if($comment)
                x-on:open-editor-{{ $comment->id }}.window="open"
                x-on:close-editor-{{ $comment->id }}.window="close"
            @endif
        >
            <div wire:ignore>
                <textarea x-ref="textarea" placeholder="{{ $placeholder }}"></textarea>
            </div>

            <div class="comments-form-editor-tip">
                You can use <a href="https://spatie.be/markdown" target="_blank" rel="nofollow noopener noreferrer">Markdown</a>
            </div>

            @if(config('comments.mentions.enabled'))
            <div x-ref="autocomplete" class="mention-autocomplete" x-show="showAutoComplete">
                <livewire:comments-mention-search wire:key="mentions_{{rand(0, 1000)}}" :commentable="$commentable" />
            </div>
            @endif
        </div>

        @script
        <script>
            Alpine.data('markdownEditor_{{$comment?->id ?? 'new'}}', () => ({
                autofocus: @js($autofocus),
                editor: null,
                showAutoComplete: false,
                autoCompleteStart: null,
                commentIsSet: @js($comment !== null),
                mentionsEnabled: @js(config('comments.mentions.enabled')),

                init() {
                    if (this.isEditing) {
                        this.open();
                        return;
                    }

                    if (!this.commentIsSet) {
                        this.open();
                    }
                },

                open() {
                    if (this.editor) {
                        return;
                    }

                    const textarea = this.$refs.textarea;

                    if (! textarea) {
                        return;
                    }

                    this.editor = MarkDownEditorUtils.initEasyMde(textarea, {{ Spatie\LivewireComments\Support\Config::autoloadFontAwesome() ? 'true' : 'false' }});

                    const editor = Alpine.raw(this.editor);
                    const modal = this.$refs.autocomplete;

                    editor.value(this.text);

                    MarkDownEditorUtils.replaceMentions(editor.codemirror);

                    if (this.autofocus) {
                        $nextTick(() => {
                            editor.codemirror.focus();
                            editor.codemirror.setCursor(editor.codemirror.lineCount(), 0);
                        });
                    }

                    Livewire.on('mention-selected', (data, more) => {
                        if (!this.autoCompleteStart || !this.showAutoComplete) {
                            return;
                        }

                        this.showAutoComplete = false;

                        const cursorPosition = MarkDownEditorUtils.getCursorPosition(editor.codemirror);

                        editor.codemirror.replaceRange('<span data-mention="' + data[0].id + '">' + data[0].display + '</span> ', {line: this.autoCompleteStart.line, ch: this.autoCompleteStart.ch -1 }, cursorPosition);

                        editor.codemirror.focus();
                    });

                    Livewire.on('comment', () => {
                        editor.value('');
                    });

                    editor.codemirror.on('blur', (instance, event) => {
                        // Using a timeout to give the click event a chance to fire in case of clicking a mention.
                        setTimeout(() => {
                            this.showAutoComplete = false;
                        }, 200);
                    });

                    editor.codemirror.on('keydown', (instance, event) => {
                        if (!this.mentionsEnabled) return;

                        if (['Escape', 'ArrowRight', 'ArrowLeft', 'Space', ' '].includes(event.key)) {
                            this.showAutoComplete = false;
                            return;
                        }

                        if (event.key === 'ArrowDown') {
                            $dispatch('auto-complete-down', event);
                            event.preventDefault();
                            return;
                        }

                        if (event.key === 'ArrowUp') {
                            $dispatch('auto-complete-up', event);
                            event.preventDefault();
                            return;
                        }

                        if (event.key === 'Enter' && this.showAutoComplete) {
                            $dispatch('auto-complete-enter', event);
                            event.preventDefault();
                            return;
                        }
                    });

                    editor.codemirror.on('change', (codeMirror) => {
                        this.text = editor.value();

                        if (!this.mentionsEnabled) return;

                        if (!this.showAutoComplete && MarkDownEditorUtils.shouldAutocomplete(codeMirror)) {
                            this.showAutoComplete = true;
                            this.autoCompleteStart = MarkDownEditorUtils.getCursorPosition(codeMirror);
                        }

                        if (this.showAutoComplete && this.mentionsEnabled) {
                            MarkDownEditorUtils.updateAutocompleteModalCoordinates(codeMirror, modal);

                            const query = codeMirror.getRange(
                                this.autoCompleteStart,
                                MarkDownEditorUtils.getCursorPosition(codeMirror),
                            ).trim();

                            $wire.dispatch('mention-autocomplete-search', {query});
                        }

                        MarkDownEditorUtils.replaceMentions(codeMirror);
                    });

                    document.addEventListener('scroll', function() {
                        if (!this.mentionsEnabled) return;

                        MarkDownEditorUtils.updateAutocompleteModalCoordinates(editor.codemirror, modal);
                    });
                },

                close() {
                    if (! this.editor) return;

                    this.editor.value('');
                    this.editor.toTextArea();
                    this.editor.cleanup();
                    this.editor = null;
                },
            }));
        </script>
        @endscript
    </div>
</div>
