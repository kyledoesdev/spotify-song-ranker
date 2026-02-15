<?php

use App\Models\Faq;

test('faq page loads successfully', function () {
    $this->get(route('faq'))->assertOk();
});

test('faq page displays active faqs', function () {
    $faq = Faq::factory()->create([
        'question' => 'How do I rank songs?',
        'text' => '<p>Select an artist and start comparing!</p>',
        'is_active' => true,
    ]);

    $this->get(route('faq'))
        ->assertOk()
        ->assertSee('How do I rank songs?')
        ->assertSee('Select an artist and start comparing!');
});

test('faq page does not display inactive faqs', function () {
    $faq = Faq::factory()->inactive()->create([
        'question' => 'Hidden question',
    ]);

    $this->get(route('faq'))
        ->assertOk()
        ->assertDontSee('Hidden question');
});

test('faqs are displayed in order', function () {
    Faq::factory()->create(['question' => 'Second question', 'order' => 2]);
    Faq::factory()->create(['question' => 'First question', 'order' => 1]);

    $response = $this->get(route('faq'))->assertOk();

    $content = $response->getContent();
    $firstPos = strpos($content, 'First question');
    $secondPos = strpos($content, 'Second question');

    expect($firstPos)->toBeLessThan($secondPos);
});

test('faq page shows empty state when no faqs exist', function () {
    Faq::query()->forceDelete();

    $this->get(route('faq'))
        ->assertOk()
        ->assertSee('No FAQs available at this time.');
});

test('slug is auto-generated from question on create', function () {
    $faq = Faq::factory()->create([
        'question' => 'How do I rank songs?',
    ]);

    expect($faq->slug)->toBe('how-do-i-rank-songs?');
});
