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
        $search = request()->search;

        return response()->json([
            'rankings' => Ranking::query()
                ->where('is_ranked', true)
                ->where('is_public', true)
                ->when($search != null, function($query) use ($search) {
                    $query->newQuery()
                        ->whereHas('artist', fn($q) => $q->where('artist_name', 'LIKE', "%{$search}%"))
                        ->orWhere('name', 'LIKE', "%{$search}%");
                })
                ->with('user', 'artist')
                ->with('songs', fn($q) => $q->where('rank', 1))
                ->withCount('songs')
                ->latest()
                ->paginate(6),
        ], 200);
    }
}
