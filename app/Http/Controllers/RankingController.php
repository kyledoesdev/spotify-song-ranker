<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rankings\CreateRankingRequest;
use App\Http\Requests\Rankings\UpdateRankingRequest;
use App\Models\Ranking;
use App\Models\Song;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RankingController extends Controller
{
    public function show($id): View
    {
        $ranking = Ranking::query()
            ->with(['user', 'songs', 'artist', 'sortingState'])
            ->findOrFail($id);

        if (! $ranking->is_public && $ranking->user_id != auth()->id()) {
            abort(404);
        }

        if (! $ranking->is_ranked && $ranking->user_id != auth()->id()) {
            abort(403, 'This ranking is not complete. You can not view it.');
        }

        return view('rank.show', [
            'ranking' => $ranking,
            'sortingState' => $ranking->sortingState,
        ]);
    }

    public function create(CreateRankingRequest $request): JsonResponse
    {
        $ranking = Ranking::start($request);

        return response()->json([
            'redirect' => route('rank.show', [
                'id' => $ranking->getKey(),
            ]),
        ], 200);
    }

    public function edit($id): View
    {
        $ranking = Ranking::query()
            ->with('songs')
            ->findOrFail($id);

        if ($ranking->user_id !== auth()->id()) {
            abort(403, 'You are not allowed to edit this ranking.');
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
            'redirect' => route('profile', ['id' => auth()->user()->spotify_id]),
        ], 200);
    }
}
