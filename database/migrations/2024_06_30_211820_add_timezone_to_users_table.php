<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('timezone')->after('avatar')->nullable();
            $table->string('ip_address')->after('timezone')->nullable();
            $table->string('user_agent')->after('ip_address')->nullable();
            $table->string('user_platform')->after('user_agent')->nullable();
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('timezone');
            $table->dropColumn('ip_address');
            $table->dropColumn('user_agent');
            $table->dropColumn('user_platform');
        });
    }
};
