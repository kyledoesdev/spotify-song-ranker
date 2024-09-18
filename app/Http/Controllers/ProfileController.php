<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(string $id): View
    {
        $user = User::where('spotify_id', $id)->first();

        return view('profile.show', [
            'user' => $user,
            'rankings' => Ranking::query()
                ->forProfilePage($user)
                ->paginate(5)
        ]);
    }
}
