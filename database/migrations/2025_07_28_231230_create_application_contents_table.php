<?php

use App\Models\ApplicationDashboard;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_dashboard', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('about_page')->nullable();
            $table->string('version');
            $table->timestamps();
            $table->softDeletes();
        });

        ApplicationDashboard::create([
            'name' => config('app.name'),
            'about_page' => null,
            'version' => '2.0',
        ]);
    }
};
