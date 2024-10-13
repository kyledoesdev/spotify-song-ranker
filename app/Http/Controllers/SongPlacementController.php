<?php

namespace App\Http\Controllers;

use App\Http\Requests\Songs\Placement\SongPlacementStoreRequest;
use App\Models\Ranking;
use Illuminate\Http\JsonResponse;

class SongPlacementController extends Controller
{
    public function store(SongPlacementStoreRequest $request): JsonResponse
    {
        Ranking::complete(collect($request->songs), $request->rankingId);

        session()->flash('success', 'Song rankings were successfuly placed!');

        return response()->json([
            'redirect' => route('home'),
        ], 200);
    }
}
