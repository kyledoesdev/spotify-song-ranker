<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRankingRequest;
use App\Http\Requests\DeleteRankingRequest;
use App\Http\Requests\UpdateRankingRequest;
use App\Models\Ranking;
use App\Models\Song;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RankingController extends Controller {

    public function index() : View {
        return view('rank.index', [
            'rankings' => Ranking::query()
                ->where('user_id', auth()->id())
                ->with(['songs', 'artist'])
                ->withCount('songs')
                ->latest()
                ->get()
        ]);
    }

    public function show($id) : View {
        $ranking = Ranking::findOrFail($id);

        //if ranking is not complete && the ranking doesn't belong to auth user abort
        if (!$ranking->is_ranked && $ranking->user_id != auth()->id()) {
            abort(403, "This ranking is not complete. You do not have permission to view it.");
        }

        return view('rank.show', [
            'songs' => $ranking->songs,
            'ranking' => $ranking,
        ]);
    }
    
    public function create(CreateRankingRequest $request) : JsonResponse {
        $ranking = Ranking::start($request);
        
        return response()->json([
            'redirect' => route('rank.show', [
                'id' => $ranking->id
            ])
        ], 200);
    }

    public function finish(UpdateRankingRequest $request) : JsonResponse {
        Ranking::complete(collect($request->songs), $request->rankingId);

        return response()->json([
            'redirect' => route('home')
        ], 200);
    }

    public function delete(DeleteRankingRequest $request) : JsonResponse {
        Ranking::findOrFail($request->rankingId)->delete();
        Song::where('ranking_id', $request->rankingId)->delete();

        return response()->json([
            'message' => 'Successfully deleted the Ranking.',
            'rankings' => Ranking::query()
                ->where('user_id', auth()->id())
                ->with(['songs', 'artist'])
                ->withCount('songs')
                ->latest()
                ->get(),
        ], 200);
    }

}
