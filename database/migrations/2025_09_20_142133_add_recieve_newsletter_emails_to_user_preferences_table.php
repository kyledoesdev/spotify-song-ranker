<?php

use App\Models\UserPreference;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->boolean('recieve_newsletter_emails')->default(true)->after('recieve_reminder_emails');
        });

        UserPreference::where('recieve_reminder_emails', false)->update(['recieve_newsletter_emails' => false]);
    }
};
