<?php

namespace App\Http\Controllers;

use App\Http\Requests\Song\Placement\StoreSongPlacementRequest;
use App\Models\Ranking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SongPlacementController extends Controller
{
    public function store(StoreSongPlacementRequest $request): JsonResponse
    {
        Ranking::complete(collect($request->songs), $request->rankingId);

        session()->flash('success', 'Song rankings were successfuly placed!');

        return response()->json([
            'redirect' => route('home'),
        ], 200);
    }
}
