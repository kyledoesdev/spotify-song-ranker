<?php

namespace App\Livewire\Legal;

use App\Enums\LegalDocumentType;
use App\Models\LegalDocument;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Component;

abstract class LegalDocumentPage extends Component
{
    abstract protected function type(): LegalDocumentType;

    public function render(): View
    {
        $document = LegalDocument::query()->currentFor($this->type());

        [$content, $sections] = $this->anchorHeadings($document?->content ?? '');

        return view('livewire.legal.document', [
            'type' => $this->type(),
            'document' => $document,
            'content' => $content,
            'sections' => $sections,
        ]);
    }

    /**
     * Give every top level heading a stable id and collect them for the table of contents,
     * so an admin never has to hand maintain anchors in the rich text editor.
     *
     * @return array{0: string, 1: array<int, array{id: string, title: string}>}
     */
    private function anchorHeadings(string $html): array
    {
        $sections = [];

        $anchored = preg_replace_callback(
            '/<h2\b[^>]*>(.*?)<\/h2>/is',
            function (array $matches) use (&$sections): string {
                $title = trim(html_entity_decode(strip_tags($matches[1]), ENT_QUOTES | ENT_HTML5));
                $id = Str::slug($title) ?: 'section-'.(count($sections) + 1);

                $sections[] = ['id' => $id, 'title' => $title];

                return '<h2 id="'.e($id).'">'.$matches[1].'</h2>';
            },
            $html
        );

        return [$anchored ?? $html, $sections];
    }
}
