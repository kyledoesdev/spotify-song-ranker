<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rankings\CreateRankingRequest;
use App\Http\Requests\Rankings\DestroyRankingRequest;
use App\Http\Requests\Rankings\FinishRankingRequest;
use App\Http\Requests\Rankings\UpdateRankingRequest;
use App\Jobs\DownloadDataJob;
use App\Models\Ranking;
use App\Models\Song;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RankingController extends Controller
{
    public function show($id): View
    {
        $ranking = Ranking::query()
            ->with('user', 'songs')
            ->findOrFail($id);

        //if ranking is not complete && the ranking doesn't belong to auth user abort
        if (! $ranking->is_ranked && $ranking->user_id != auth()->id()) {
            abort(403, 'This ranking is not complete. You can not view it.');
        }

        return view('rank.show', [
            'songs' => $ranking->songs,
            'ranking' => $ranking,
            'creator' => $ranking->user->name,
        ]);
    }

    public function create(CreateRankingRequest $request): JsonResponse
    {
        return response()->json([
            'redirect' => route('rank.show', [
                'id' => Ranking::start($request)->id,
            ]),
        ], 200);
    }

    public function edit($id): View
    {
        $ranking = Ranking::query()
            ->with('songs')
            ->findOrFail($id);

        if ($ranking->user_id !== auth()->id()) {
            abort(403, "You are not allowed to edit this ranking.");
        }
        
        return view('rank.edit', [
            'ranking' => $ranking,
        ]);
    }

    public function update(UpdateRankingRequest $request, $id): JsonResponse
    {
        $ranking = Ranking::findOrFail($id);
        $ranking->update($request->validated());

        session()->flash('success', 'Ranking was succesfully updated!');

        return response()->json([
            'redirect' => route('profile.show', ['id' => auth()->user()->spotify_id]),
        ], 200);
    }

    public function destroy(DestroyRankingRequest $request): JsonResponse
    {
        Ranking::findOrFail($request->rankingId)->delete();
        Song::where('ranking_id', $request->rankingId)->delete();

        return response()->json([
            'message' => 'Your ranking has been tossed into the void, never to return. Or will it?',
            'rankings' => Ranking::query()
                ->where('user_id', auth()->id())
                ->with('user', 'artist')
                ->with('songs', fn($q) => $q->where('rank', 1))
                ->withCount('songs')
                ->latest()
                ->paginate(5),
        ], 200);
    }

    public function pages(): JsonResponse
    {
        $user = User::where('spotify_id', request()->spotify_id)->first();

        $response = [
            'success' => true,
            'rankings' => Ranking::query()->forProfilePage($user)->paginate(5),
            'name' => $user ? get_formatted_name($user->name) : null,
        ];

        if (is_null($user)) {
            $response['success'] = false;
            $response['message'] = "No ranking results for user: {$spotify_id}.";
        }

        return response()->json($response, 200);
    }
}
