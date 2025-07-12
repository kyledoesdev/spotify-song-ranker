<?php

use App\Models\Ranking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ranking_sorting_states', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Ranking::class);
            $table->json('sorting_state')->nullable();
            $table->integer('aprox_comparisons')->default(0);
            $table->integer('completed_comparisons')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        foreach (Ranking::all() as $ranking) {
            $ranking->sortingState()->create();
        }
    }
};
