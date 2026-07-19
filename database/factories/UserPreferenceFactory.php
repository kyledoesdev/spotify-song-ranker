<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserPreference>
 */
class UserPreferenceFactory extends Factory
{
    private bool $recieveNewsletterEmails = true;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recieve_newsletter_emails' => $this->recieveNewsletterEmails,
        ];
    }

    private function toggleNewsletterEmails(bool $on): void
    {
        $this->recieveNewsletterEmails = $on;
    }
}
