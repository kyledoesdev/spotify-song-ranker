<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Ranking;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ExploreController extends Controller
{
    public function pages(): JsonResponse
    {
        return response()->json([
            'top_artists' => Artist::query()->topArtists()->limit(10)->get(),
            'rankings' => Ranking::query()
                ->forExplorePage(request()->search, request()->artist)
                ->paginate(5),
        ], 200);
    }
}
