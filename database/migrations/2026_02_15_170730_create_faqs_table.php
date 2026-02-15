<?php

use App\Models\Faq;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->integer('order')->default(0);
            $table->string('question');
            $table->string('slug');
            $table->longText('text');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        $faqs = [
            [
                'order' => 1,
                'question' => 'What is SongRank?',
                'text' => '<p>SongRank is a fun, interactive tool that helps you discover your true favorite songs! Connect your Spotify account, pick an artist or playlist, and we\'ll walk you through a series of head-to-head matchups until your personal ranking is complete. Think of it like a bracket tournament for your music taste.</p>',
            ],
            [
                'order' => 2,
                'question' => 'How does SongRank work?',
                'text' => '<p>It\'s simple! Once you pick an artist or playlist, SongRank will show you two songs at a time and ask you to choose which one you like more. Behind the scenes, we use a merge-sort algorithm to figure out your full ranking with as few comparisons as possible. You can even pause and come back later &mdash; your progress is saved automatically.</p>',
            ],
            [
                'order' => 3,
                'question' => 'What can I rank on SongRank?',
                'text' => '<p>You can rank any artist\'s discography or any playlist from your Spotify library. Just search for the artist or select a playlist, choose which tracks to include, and start ranking!</p>',
            ],
            [
                'order' => 4,
                'question' => 'Is SongRank free?',
                'text' => '<p>Yes! SongRank is completely free to use. All you need is a Spotify account to log in and start ranking your favorite music.</p>',
            ],
            [
                'order' => 5,
                'question' => 'What Spotify data does SongRank collect?',
                'text' => '<p>We only collect the basics needed to make SongRank work &mdash; your Spotify display name, profile image, and the ability to look up artist and playlist track listings. We never access your private listening history, and we never modify anything on your Spotify account. Your data stays safe with us.</p>',
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
