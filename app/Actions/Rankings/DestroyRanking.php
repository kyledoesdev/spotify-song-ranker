<?php

namespace App\Actions\Rankings;

use App\Models\Ranking;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class DestroyRanking
{
    public function handle(User $user, Ranking $ranking)
    {
        if ($ranking->is_public) {
            cache()->forget('explore:total-rankings');
        }

        DB::transaction(function () use ($ranking) {
            $ranking->songs()->delete();
            $ranking->delete();
        });
    }
}
