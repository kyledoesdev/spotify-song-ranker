<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'lists' => Ranking::query()
                ->where('user_id', auth()->id())
                ->with('artist', 'songs')
                ->withCount('songs')
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }
}
