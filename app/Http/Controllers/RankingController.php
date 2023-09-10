<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use App\Models\Song;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RankingController extends Controller {

    public function show($id) : View {
        $ranking = Ranking::query()
            ->with('songs', 
                fn($query) => $query->newQuery()
                    ->select('id', 'ranking_id', 'spotify_song_id', 'title', 'cover')
            )
            ->findOrFail($id);

        abort_if($ranking->user_id != auth()->id(), 403);

        return view('rank', [
            'songs' => $ranking->songs,
            'isRanked' => $ranking->is_ranked,
            'rankingId' => $ranking->id
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

    public function update(Request $request) : JsonResponse {
        $songs = collect($request->songs);

        $data = [];
        for ($i = 0; $i < count($songs) ; $i++) { 
            array_push($data, [
                'ranking_id' => $request->rankingId,
                'spotify_song_id' => $songs[$i]['spotify_song_id'],
                'title' => $songs[$i]['title'],
                'cover' => $songs[$i]['cover'],
                'rank' => $i + 1
            ]);
        }
        
        Song::where('ranking_id', $request->rankingId)->forceDelete();
        Song::insert($data);

        return response()->json([
            'redirect' => route('home')
        ], 200);
    }

}
