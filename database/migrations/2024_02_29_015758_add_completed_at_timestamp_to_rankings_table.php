<?php

use App\Models\Ranking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rankings', function (Blueprint $table) {
            $table->timestamp('completed_at')->after('is_ranked')->nullable();
        });

        foreach (Ranking::all() as $ranking) {
            $ranking->update(['completed_at' => $ranking->updated_at]);
        }
    }

    public function down(): void
    {
        Schema::table('rankings', function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
};
