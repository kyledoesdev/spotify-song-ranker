<?php

use App\Filament\Resources\LegalDocuments\Pages\ListLegalDocuments;
use App\Models\LegalDocument;
use Livewire\Livewire;

beforeEach(function () {
    LegalDocument::query()->forceDelete();
});

describe('legal document table', function () {
    test('lists every document', function () {
        $terms = LegalDocument::factory()->terms()->create();
        $privacy = LegalDocument::factory()->privacy()->create();

        Livewire::actingAs(kyle())
            ->test(ListLegalDocuments::class)
            ->assertCanSeeTableRecords([$terms, $privacy]);
    });

    test('filters by document type', function () {
        $terms = LegalDocument::factory()->terms()->create();
        $privacy = LegalDocument::factory()->privacy()->create();

        Livewire::actingAs(kyle())
            ->test(ListLegalDocuments::class)
            ->filterTable('type', 'privacy')
            ->assertCanSeeTableRecords([$privacy])
            ->assertCanNotSeeTableRecords([$terms]);
    });

    test('hides trashed documents by default', function () {
        $live = LegalDocument::factory()->terms()->create();
        $trashed = LegalDocument::factory()->terms()->create();
        $trashed->delete();

        Livewire::actingAs(kyle())
            ->test(ListLegalDocuments::class)
            ->assertCanSeeTableRecords([$live])
            ->assertCanNotSeeTableRecords([$trashed]);
    });

    /** Without this filter the restore and force delete bulk actions can never reach a record. */
    test('surfaces trashed documents so they can be restored', function () {
        $live = LegalDocument::factory()->terms()->create();
        $trashed = LegalDocument::factory()->terms()->create();
        $trashed->delete();

        Livewire::actingAs(kyle())
            ->test(ListLegalDocuments::class)
            ->filterTable('trashed', false)
            ->assertCanSeeTableRecords([$trashed])
            ->assertCanNotSeeTableRecords([$live]);
    });
});
