<?php

namespace App\Http\Controllers;

use App\Jobs\DownloadDataJob;
use App\Models\Ranking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RankingDownloadController extends Controller
{
    public function index(): JsonResponse
    {
        $rankings = Ranking::query()
            ->where('user_id', auth()->id())
            ->with('songs', 'artist')
            ->get();

        DownloadDataJob::dispatch($rankings, auth()->user());

        return response()->json([
            'success' => true,
            'message' => "Your download has started and will be emailed to you when completed!"
        ], 200);
    }
}
