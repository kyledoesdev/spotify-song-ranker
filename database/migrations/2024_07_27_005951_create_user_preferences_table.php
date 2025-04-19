<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->boolean('recieve_reminder_emails')->default(true)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        User::all()->each(fn ($user) => $user->preferences()->create(['recieve_reminder_emails' => true]));
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
