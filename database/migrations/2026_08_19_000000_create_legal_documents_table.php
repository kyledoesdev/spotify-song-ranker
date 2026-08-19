<?php

use App\Enums\LegalDocumentType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_documents', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->longText('content')->nullable();
            $table->timestamp('effective_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'effective_at']);
        });

        DB::transaction(fn () => $this->carryOverExistingTerms());

        Schema::dropIfExists('terms');
    }

    public function down(): void
    {
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->longText('content')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::transaction(function () {
            DB::table('legal_documents')
                ->where('type', LegalDocumentType::TERMS->value)
                ->orderBy('id')
                ->get()
                ->each(fn ($document) => DB::table('terms')->insert([
                    'content' => $document->content,
                    'created_at' => $document->created_at,
                    'updated_at' => $document->updated_at,
                    'deleted_at' => $document->deleted_at,
                ]));
        });

        Schema::dropIfExists('legal_documents');
    }

    /**
     * Preserve the revision history from the old single purpose terms table.
     */
    private function carryOverExistingTerms(): void
    {
        if (! Schema::hasTable('terms')) {
            return;
        }

        DB::table('terms')
            ->orderBy('id')
            ->get()
            ->each(fn ($term) => DB::table('legal_documents')->insert([
                'type' => LegalDocumentType::TERMS->value,
                'content' => $term->content,
                'effective_at' => $term->created_at,
                'created_at' => $term->created_at,
                'updated_at' => $term->updated_at,
                'deleted_at' => $term->deleted_at,
            ]));
    }
};
