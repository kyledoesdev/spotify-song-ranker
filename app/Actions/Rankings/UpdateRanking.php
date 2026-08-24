<?php

namespace App\Actions\Rankings;

use App\Livewire\Forms\RankingForm;
use App\Models\Ranking;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class UpdateRanking
{
    public function handle(User $user, Ranking $ranking, RankingForm $form): void
    {
        DB::transaction(function() use ($user, $ranking, $form) {
            $ranking->update([
                'name' => $form->name,
                'is_public' => $form->is_public === '1' || $form->is_public === true,
                'comments_enabled' => $form->comments_enabled === '1' || $form->comments_enabled === true,
                'comments_replies_enabled' => $form->comments_replies_enabled === '1' || $form->comments_replies_enabled === true,
            ]);
        });
    }
}
