<?php

namespace Database\Factories;

use App\Enums\LegalDocumentType;
use App\Models\LegalDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LegalDocument>
 */
class LegalDocumentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => LegalDocumentType::TERMS,
            'content' => '<h2>'.fake()->sentence().'</h2><p>'.fake()->paragraph().'</p>',
            'effective_at' => now()->subDay(),
        ];
    }

    public function terms(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => LegalDocumentType::TERMS,
        ]);
    }

    public function privacy(): static
    {
        return $this->state(fn (array $attributes): array => [
            'type' => LegalDocumentType::PRIVACY,
        ]);
    }

    public function effectiveAt(string $date): static
    {
        return $this->state(fn (array $attributes): array => [
            'effective_at' => $date,
        ]);
    }
}
