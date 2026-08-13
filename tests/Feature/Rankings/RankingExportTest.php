<?php

use App\Exports\RankingsExport;
use App\Exports\SongExport;
use App\Models\Ranking;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

describe('song export', function () {
    test('exposes the expected headings', function () {
        $ranking = publicCompletedRanking();

        expect(songExport($ranking)->headings())->toBe(['Artist', 'Rank', 'Title']);
    });

    test('uses the ranking name as the sheet title', function () {
        $ranking = publicCompletedRanking(attributes: ['name' => 'My Favourite Songs']);

        expect(songExport($ranking)->title())->toBe('My Favourite Songs');
    });

    test('maps a song to its artist, rank and title', function () {
        $ranking = publicCompletedRanking();
        $song = $ranking->songs()->orderBy('rank')->first();

        expect(songExport($ranking)->map($song))->toBe([
            $song->artist->artist_name,
            $song->rank,
            $song->title,
        ]);
    });

    test('falls back to N/A when a song has no artist', function () {
        $ranking = publicCompletedRanking();
        $song = $ranking->songs()->orderBy('rank')->first();
        $song->setRelation('artist', null);

        expect(songExport($ranking)->map($song)[0])->toBe('N/A');
    });
});

describe('song export rendering', function () {
    test('renders a csv with a heading row and one row per song', function () {
        $ranking = publicCompletedRanking();

        $lines = csvLines(Excel::raw(songExport($ranking), ExcelFormat::CSV));

        expect($lines[0])->toBe('"Artist","Rank","Title"')
            ->and($lines)->toHaveCount($ranking->songs->count() + 1);
    });

    test('renders the songs in rank order', function () {
        $ranking = publicCompletedRanking();

        $lines = csvLines(Excel::raw(songExport($ranking), ExcelFormat::CSV));

        expect($lines[1])->toContain('"1"')
            ->and($lines[2])->toContain('"2"');
    });
});

describe('rankings export', function () {
    test('creates one sheet per ranking', function () {
        $rankings = Ranking::query()->whereKey([
            publicCompletedRanking()->getKey(),
            publicCompletedRanking()->getKey(),
        ])->get();

        expect((new RankingsExport($rankings))->sheets())->toHaveCount(2);
    });

    test('renders a readable xlsx with a sheet per ranking', function () {
        $first = publicCompletedRanking(attributes: ['name' => 'First Ranking']);
        $second = publicCompletedRanking(attributes: ['name' => 'Second Ranking']);

        $rankings = Ranking::query()
            ->with('songs.artist')
            ->whereKey([$first->getKey(), $second->getKey()])
            ->get();

        $path = tempnam(sys_get_temp_dir(), 'export').'.xlsx';
        file_put_contents($path, Excel::raw(new RankingsExport($rankings), ExcelFormat::XLSX));

        $spreadsheet = IOFactory::load($path);

        expect($spreadsheet->getSheetCount())->toBe(2)
            ->and($spreadsheet->getSheet(0)->getTitle())->toBe('First Ranking')
            ->and($spreadsheet->getSheet(1)->getTitle())->toBe('Second Ranking')
            ->and($spreadsheet->getSheet(0)->getCell('A1')->getValue())->toBe('Artist')
            ->and($spreadsheet->getSheet(0)->getHighestRow())->toBe($first->songs->count() + 1);

        unlink($path);
    });
});

function songExport(Ranking $ranking): SongExport
{
    return new SongExport($ranking->songs()->with('artist')->orderBy('rank')->get(), $ranking->name);
}

/**
 * The CSV writer emits PHP_EOL, so line endings differ between Windows and Linux.
 *
 * @return list<string>
 */
function csvLines(string $csv): array
{
    return array_values(array_filter(preg_split('/\R/', trim($csv))));
}
