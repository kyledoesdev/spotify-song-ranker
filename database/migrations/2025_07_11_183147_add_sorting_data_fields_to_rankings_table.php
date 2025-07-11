<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rankings', function (Blueprint $table) {
            $table->json('sorting_state')->nullable()->after('completed_at');
            $table->integer('total_comparisons')->default(0)->nullable()->after('sorting_state');
            $table->integer('completed_comparisons')->default(0)->nullable()->after('total_comparisons');
        });
    }
};
