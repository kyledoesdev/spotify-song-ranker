<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RankingController extends Controller {

    public function show($id) : View {
        return view('rank', [
            'songs' => Ranking::findOrFail($id)->songs
        ]);
    }
    
    public function create(Request $request) : JsonResponse {
        $ranking = Ranking::start($request);
        
        return response()->json([
            'redirect' => route('rank.show', [
                'id' => $ranking->id
            ])
        ], 200);
    }

}
