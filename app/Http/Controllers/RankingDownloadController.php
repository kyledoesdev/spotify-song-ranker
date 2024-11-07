<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use App\Notifications\DownloadDataNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;

class RankingDownloadController extends Controller
{
    public function index(): JsonResponse
    {
        $rankings = Ranking::query()
            ->where('user_id', auth()->id())
            ->with('songs', 'artist')
            ->get();

        //defer(fn() => );
        Notification::send(auth()->user(), new DownloadDataNotification($rankings));

        return response()->json([
            'success' => true,
            'message' => "Your download has started and will be emailed to you when completed!"
        ], 200);
    }
}
