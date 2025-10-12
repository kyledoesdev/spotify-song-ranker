<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SongExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function __construct(
        private Collection $songs,
        private string $rankingName
    ) {}

    public function collection()
    {
        return $this->songs;
    }

    public function map($song): array
    {
        return [
            $song->rank,
            $song->title,
        ];
    }

    public function headings(): array
    {
        return [
            'Rank',
            'Title',
        ];
    }

    public function title(): string
    {
        return $this->rankingName;
    }
}
