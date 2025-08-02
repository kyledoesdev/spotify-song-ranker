<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RankingController extends Controller
{
    public function __invoke(Request $request, int $id): View
    {
        $ranking = Ranking::query()
            ->with(['user', 'songs', 'artist', 'sortingState'])
            ->findOrFail($id);

        if (! $ranking->isPublic()) {
            abort(404);
        }

        if (! $ranking->is_ranked && $ranking->user_id != auth()->id()) {
            abort(403, 'This ranking is not complete. You can not view it.');
        }

        return view('rank.show', [
            'ranking' => $ranking,
            'sortingState' => $ranking->sortingState,
        ]);
    }
}
