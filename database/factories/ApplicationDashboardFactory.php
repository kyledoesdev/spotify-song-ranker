<?php

namespace Database\Factories;

use App\Models\ApplicationDashboard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApplicationDashboard>
 */
class ApplicationDashboardFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => config('app.name'),
            'about_page' => '<h2>Welcome to SongRank</h2><p>'.fake()->paragraph().'</p>',
            'support_page' => null,
            'version' => '2.0',
            'slideshow_speed' => 3,
            'seo_terms' => 'song, ranking, spotify',
        ];
    }
}
