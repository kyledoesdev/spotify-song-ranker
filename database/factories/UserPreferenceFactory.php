<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserPreferenceFactory extends Factory
{
    private bool $recieveReminderEmails = true;
    private bool $recieveNewsletterEmails = true;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recieve_reminder_emails' => $this->recieveReminderEmails,
            'recieve_newsletter_emails' => $this->recieveNewsletterEmails,
        ];
    }

    private function toggleReminderEmails(bool $on): void
    {
        $this->recieveReminderEmails = $on;
    }

    private function toggleNewsletterEmails(bool $on): void
    {
        $this->recieveNewsletterEmails = $on;
    }
}
