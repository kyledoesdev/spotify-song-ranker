<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ExploreController extends Controller
{
    public function index(): View 
    {
        return view('explore', [
            'title' => 'Explore Rankings'
        ]);
    }

    public function pages(): JsonResponse
    {
        return response()->json([
            'rankings' => Ranking::query()
                ->where('is_ranked', true)
                ->where('is_public', true)
                ->with('user', 'artist')
                ->with('songs', fn($q) => $q->where('rank', 1))
                ->withCount('songs')
                ->latest()
                ->paginate(9),
        ], 200);
    }
}
