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

        /* no profile found for user */
        if (is_null($user)) {
            return view('profile.show', [
                'user' => null,
                'name' => get_formatted_name($id),
                'rankings' => null,
            ]);
        }

        return view('profile.show', [
            'user' => $user,
            'name' => get_formatted_name($user->name),
            'rankings' => Ranking::query()
                ->forProfilePage($user)
                ->get()
        ]);
    }
}
