<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserPreferenceFactory extends Factory
{
    private bool $recieveReminderEmails = true;

    public function definition(): array
    {
        return [
            'user_id' => User::facotry(),
            'recieve_reminder_emails' => $this->recieveReminderEmails
        ];
    }

    private function toggleReminderEmails(bool $on): void
    {
        $this->recieveReminderEmails = $on;
    }
}
