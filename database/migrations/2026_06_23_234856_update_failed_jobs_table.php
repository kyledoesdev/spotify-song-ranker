<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            ALTER TABLE `failed_jobs`
                ADD COLUMN `display_name` VARCHAR(255)
                    GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(`payload`, '$.displayName')))
                    STORED AFTER `uuid`,
                ADD COLUMN `exception_name` VARCHAR(255)
                    GENERATED ALWAYS AS (SUBSTRING_INDEX(`exception`, ':', 1))
                    STORED AFTER `display_name`,
                ADD INDEX `failed_jobs_display_name_index` (`display_name`),
                ADD INDEX `failed_jobs_exception_name_index` (`exception_name`)
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        Schema::table('failed_jobs', function (Blueprint $table) {
            $table->dropIndex('failed_jobs_exception_name_index');
            $table->dropIndex('failed_jobs_display_name_index');
            $table->dropColumn(['exception_name', 'display_name']);
        });
    }
};
