<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ExploreController extends Controller
{
    public function pages(): JsonResponse
    {
        return response()->json([
            'rankings' => Ranking::query()
                ->forExplorePage(request()->search)
                ->paginate(6),
        ], 200);
    }
}
