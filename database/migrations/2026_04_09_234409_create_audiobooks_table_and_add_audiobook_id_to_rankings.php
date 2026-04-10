<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shows', function (Blueprint $table) {
            $table->id();
            $table->string('show_id')->unique();
            $table->string('publisher')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('cover')->nullable();
            $table->integer('episode_count')->default(0);
            $table->json('data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('rankings', function (Blueprint $table) {
            $table->foreignId('show_id')->after('playlist_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('rankings', function (Blueprint $table) {
            $table->dropColumn('show_id');
        });

        Schema::dropIfExists('shows');
    }
};
