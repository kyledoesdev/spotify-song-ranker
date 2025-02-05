<?php

namespace App\Models;

use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class Ranking extends Model
{
    protected $fillable = [
        'user_id',
        'artist_id',
        'name',
        'is_ranked',
        'is_public',
        'completed_at',
    ];

    protected $appends = ['show_route'];

    protected function casts(): array
    {
        return [
            'is_ranked' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function artist(): HasOne
    {
        return $this->hasOne(Artist::class, 'id', 'artist_id');
    }

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }

    public function getCompletedAtAttribute()
    {
        if ($this->attributes['completed_at'] == null) { 
            return 'In Progress'; 
        }

        return Carbon::parse($this->attributes['completed_at'])->diffForHumans();
    }

    public function getShowRouteAttribute()
    {
        return route('rank.show', ['id' => $this->getKey()]);
    }

    /**
     * Start a new ranking.
     */
    public static function start($request): self
    {
        $artist = Artist::updateOrCreate([
            'artist_id' => $request->artist_id,
        ], [
            'artist_name' => $request->artist_name,
            'artist_img' => $request->artist_img,
        ]);

        $ranking = self::create([
            'user_id' => auth()->id(),
            'artist_id' => $artist->getKey(),
            'name' => Str::limit($request->name ?? $artist->artist_name . ' List' , 30),
            'is_public' => $request->is_public ?? false
        ]);

        $songs = [];
        foreach ($request->songs as $song) {
            array_push($songs, [
                'ranking_id' => $ranking->getKey(),
                'spotify_song_id' => $song['id'],
                'title' => $song['name'],
                'cover' => $song['cover'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Song::insert($songs);

        Log::channel('discord')->info(auth()->user()->name . ' started ranking: ' . $ranking->name);

        return $ranking;
    }

    public static function complete($songs, $id): void
    {
        $data = [];
        for ($i = 0; $i < count($songs); $i++) {
            array_push($data, [
                'ranking_id' => $id,
                'spotify_song_id' => $songs[$i]['spotify_song_id'],
                'title' => $songs[$i]['title'],
                'cover' => $songs[$i]['cover'],
                'rank' => $i + 1,
                'updated_at' => now(),
            ]);
        }

        $ranking = self::find($id);

        $ranking->update(['is_ranked' => true, 'completed_at' => now()]);
        Song::upsert($data, ['ranking_id', 'spotify_song_id'], ['title', 'cover', 'rank', 'updated_at']);
        Log::channel('discord')->info("{$ranking->user->name} completed a ranking: {$ranking->name}.");
    }

    /* scopes */
    public function scopeForExplorePage(Builder $query, ?string $search = null)
    {
        $query->newQuery()
            ->where('is_ranked', true)
            ->where('is_public', true)
            ->when($search != null, function($query) use ($search) {
                $query->whereHas('artist', function($q) use ($search) {
                    $q->where('artist_name', 'LIKE', "%{$search}%")->orWhere('id', $search);
                });
            })
            ->with('user', 'artist')
            ->with('songs', fn($q) => $q->where('rank', 1))
            ->withCount('songs')
            ->latest();
    }

    public function scopeForProfilePage(Builder $query, $user)
    {
        $query->newQuery()
            ->where('user_id', $user ? $user->getKey() : auth()->id())
            ->when($user && $user->getKey() !== auth()->id(), function($query2) {
                $query2->where('is_ranked', true)
                    ->where('is_public', true);
            })
            ->with('user', 'artist')
            ->with('songs', fn($q) => $q->where('rank', 1))
            ->withCount('songs')
            ->latest();
    }

    public function scopeForReminders(Builder $query)
    {
        $query->newQuery()
            ->select('id', 'user_id', 'artist_id', 'name', 'is_ranked', 'created_at')
            ->where('is_ranked', false)
            ->with('artist')
            ->withCount('songs');
    }
}
