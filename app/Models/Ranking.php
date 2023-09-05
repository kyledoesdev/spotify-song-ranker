<?php

namespace App\Models;

use App\Models\Artist;
use App\Models\Model;
use App\Models\Song;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ranking extends Model {

    protected $fillable = [
        'user_id',
        'artist_id',
        'name'
    ];

    public static function boot() {
        parent::boot();
    }

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function songs() : HasMany {
        return $this->hasMany(Song::class);
    }

    /**
     * Start a new ranking.
     */
    public static function start($request) : self {
        $artist = Artist::updateOrCreate([
            'artist_id' => $request->artist_id
        ], [
            'artist_name' => $request->artist_name,
            'artist_img' => $request->artist_img
        ]);

        $ranking = self::create([
            'user_id' => auth()->id(),
            'artist_id' => $artist->id,
            'name' => $request->name
        ]);

        $songs = [];
        foreach ($request->songs as $song) {
            array_push($songs, [
                'ranking_id' => $ranking->id,
                'spotify_song_id' => $song['id'],
                'title' => $song['name'],
                'cover' => $song['cover'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        Song::insert($songs);

        return $ranking;
    }

}
