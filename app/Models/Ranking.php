<?php

namespace App\Models;

use App\Models\Artist;
use App\Models\Model;
use App\Models\Song;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\Builder;

class Ranking extends Model {

    protected $fillable = [
        'user_id',
        'artist_id',
        'name',
        'is_ranked'
    ];

    public $casts = [
        'is_ranked' => 'boolean'
    ];

    public static function boot() {
        parent::boot();
    }

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function artist() : HasOne {
        return $this->hasOne(Artist::class, 'id', 'artist_id');
    }

    public function songs() : HasMany {
        return $this->hasMany(Song::class);
    }

    public function getUpdatedAtAttribute() {
        return Carbon::parse($this->attributes['updated_at'])->diffForHumans();
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

    public static function complete($songs, $id) : void {
        $data = [];
        for ($i = 0; $i < count($songs) ; $i++) { 
            array_push($data, [
                'ranking_id' => $id,
                'spotify_song_id' => $songs[$i]['spotify_song_id'],
                'title' => $songs[$i]['title'],
                'cover' => $songs[$i]['cover'],
                'rank' => $i + 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        self::find($id)->update(['is_ranked' => true]);
        Song::where('ranking_id', $id)->forceDelete();
        Song::insert($data);
    }
}
