<?php

namespace App\Actions\Rankings;

use App\Models\Ranking;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class DestroyRanking
{
    public function handle(User $user, Ranking $ranking)
    {
        DB::transaction(function () use ($ranking) {
            $ranking->songs()->delete();
            $ranking->delete();
        });

        Log::channel('discord_ranking_updates')->info($user->name.' deleted ranking: '.$ranking->name);
    }
}
