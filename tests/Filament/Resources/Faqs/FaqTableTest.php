<?php

use App\Filament\Resources\Faqs\Pages\ListFaqs;
use App\Models\Faq;
use Livewire\Livewire;

describe('trashed filter', function () {
    test('hides trashed faqs by default', function () {
        $active = Faq::factory()->createOne();
        $trashed = Faq::factory()->createOne();
        $trashed->delete();

        Livewire::actingAs(kyle())
            ->test(ListFaqs::class)
            ->assertCanSeeTableRecords([$active])
            ->assertCanNotSeeTableRecords([$trashed]);
    });

    /** Without this filter the restore and force delete bulk actions can never reach a record. */
    test('surfaces trashed faqs so they can be restored', function () {
        $active = Faq::factory()->createOne();
        $trashed = Faq::factory()->createOne();
        $trashed->delete();

        Livewire::actingAs(kyle())
            ->test(ListFaqs::class)
            ->filterTable('trashed', false)
            ->assertCanSeeTableRecords([$trashed])
            ->assertCanNotSeeTableRecords([$active]);
    });
});
