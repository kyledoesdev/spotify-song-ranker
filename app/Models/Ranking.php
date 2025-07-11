<?php

namespace App\Models;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
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

    protected $appends = [
        'show_route',
        'formatted_completed_at'
    ];

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

    public function sortingState(): HasOne
    {
        return $this->hasOne(RankingSortingState::class, 'ranking_id', 'id');
    }

    public function getCompletedAtAttribute()
    {
        if ($this->attributes['completed_at'] == null) {
            return 'In Progress';
        }

        return Carbon::parse($this->attributes['completed_at'])->diffForHumans();
    }

    public function getFormattedCompletedAtAttribute()
    {
        if ($this->attributes['completed_at'] == null) {
            return 'In Progress';
        }

        return Carbon::parse($this->attributes['completed_at'])->inUserTimezone()->format('M d, Y g:i A T');
    }

    public function getShowRouteAttribute()
    {
        return route('rank.show', ['id' => $this->getKey()]);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->total_comparisons == 0) {
            return 0;
        }

        return intval(($this->completed_comparisons / $this->total_comparisons) * 100);
    }

    /**
     * Start a new ranking.
     */
    public static function start($request): self
    {
        $ranking = DB::transaction(function () use ($request) {
            /* update or create the artist  */
            $artist = Artist::updateOrCreate([
                'artist_id' => $request->artist_id,
            ], [
                'artist_name' => $request->artist_name,
                'artist_img' => $request->artist_img,
            ]);

            /* create a new ranking  */
            $ranking = self::create([
                'user_id' => auth()->id(),
                'artist_id' => $artist->getKey(),
                'name' => Str::limit($request->name ?? $artist->artist_name . ' List', 30),
                'is_public' => $request->is_public ?? false,
                'total_comparisons' => 0,
                'completed_comparisons' => 0,
            ]);

            /* create the relation to the ranking's sorted state */
            $ranking->sortingState()->create();

            $songs = [];
            foreach ($request->songs as $song) {
                array_push($songs, [
                    'ranking_id' => $ranking->getKey(),
                    'spotify_song_id' => $song['id'],
                    'uuid' => Str::uuid(),
                    'title' => $song['name'] ?? 'track deleted from spotify servers',
                    'cover' => $song['cover'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            /* batch insert the song records */
            Song::insert($songs);

            return $ranking;
        });

        Log::channel('discord')->info(auth()->user()->name . ' started ranking: ' . $ranking->name);

        return $ranking;
    }

    /**
     * Complete a ranking (legacy method, now handled by Livewire component)
     */
    public static function complete($songs, $id): void
    {
        $data = [];
        for ($i = 0; $i < count($songs); $i++) {
            array_push($data, [
                'ranking_id' => $id,
                'spotify_song_id' => $songs[$i]['spotify_song_id'],
                'uuid' => $songs[$i]['uuid'],
                'title' => $songs[$i]['title'],
                'cover' => $songs[$i]['cover'],
                'rank' => $i + 1,
                'updated_at' => now(),
            ]);
        }

        $ranking = self::find($id);

        DB::transaction(function () use ($ranking, $data) {
            $ranking->update([
                'is_ranked' => true, 
                'completed_at' => now(),
                'sorting_state' => null
            ]);
            
            Song::upsert($data, ['ranking_id', 'spotify_song_id'], ['title', 'cover', 'rank', 'updated_at']);
        });

        Log::channel('discord')->info("{$ranking->user->name} completed a ranking: {$ranking->name}.");
    }

    /* scopes */
    public function scopeForExplorePage(Builder $query, ?string $search = '', ?string $artist = null)
    {
        $query->newQuery()
            ->where('is_ranked', true)
            ->where('is_public', true)
            ->whereHas('artist', function ($query2) use ($search, $artist) {
                $query2
                    ->when($search != '', fn ($q) => $q->where('artist_name', 'LIKE', "%{$search}%"))
                    ->when($artist != null, fn ($q) => $q->where('id', $artist));
            })
            ->with('user', 'artist')
            ->with('songs', fn ($q) => $q->where('rank', 1))
            ->withCount('songs')
            ->latest();
    }

    public function scopeForProfilePage(Builder $query, User $user)
    {
        $query->newQuery()
            ->where('user_id', $user ? $user->getKey() : auth()->id())
            ->when($user && $user->getKey() !== auth()->id(), function ($query2) {
                $query2->where('is_ranked', true)
                    ->where('is_public', true);
            })
            ->with('user', 'artist')
            ->with('songs', fn ($q) => $q->where('rank', 1))
            ->withCount('songs')
            ->latest();
    }

    public function scopeForReminders(Builder $query)
    {
        $query->newQuery()
            ->select('id', 'user_id', 'artist_id', 'name', 'is_ranked', 'created_at', 'completed_comparisons', 'total_comparisons')
            ->where('is_ranked', false)
            ->with('artist')
            ->withCount('songs');
    }

    public function scopeInProgress(Builder $query)
    {
        $query->where('is_ranked', false)
            ->whereNotNull('sorting_state');
    }

    public function scopeAbandoned(Builder $query, $days = 7)
    {
        $query->where('is_ranked', false)
            ->whereNotNull('sorting_state')
            ->where('updated_at', '<', now()->subDays($days));
    }

    /* -- Filament Admin Functions -- */

    public static function getAdminForm(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            DatePicker::make('completed_at')
                ->label('Completed At'),
            Toggle::make('is_ranked')
                ->label('Is Ranked')
                ->required(),
            Toggle::make('is_public')
                ->label('Is Public')
                ->required(),
        ];
    }

    public static function getAdminTable(?int $user_id = null): array
    {
        return [
            TextColumn::make('user.name')
                ->label('Creator')
                ->searchable()
                ->sortable()
                ->hidden(fn () => ! is_null($user_id)),
            TextColumn::make('name')
                ->searchable()
                ->sortable(),
            TextColumn::make('artist.artist_name')
                ->label('Artist')
                ->searchable()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false),
            IconColumn::make('is_ranked')
                ->label('Is Ranked')
                ->boolean()
                ->searchable()
                ->sortable()
                ->trueIcon('heroicon-o-check-badge')
                ->falseIcon('heroicon-o-x-mark')
                ->toggleable(isToggledHiddenByDefault: false),
            IconColumn::make('is_public')
                ->label('Is Public')
                ->boolean()
                ->searchable()
                ->sortable()
                ->trueIcon('heroicon-o-check-badge')
                ->falseIcon('heroicon-o-x-mark')
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('completed_at')
                ->searchable()
                ->sortable()
                ->dateTime()
                ->toggleable(isToggledHiddenByDefault: false)
                ->formatStateUsing(function ($state, $record) {
                    $timestamp = $record->getAttributes()['completed_at'];

                    if (is_null($timestamp)) {
                        return 'In Progress';
                    }

                    return Carbon::parse($timestamp)
                        ->inUserTimezone()
                        ->format('m/d/Y g:i A T');
                }),
            TextColumn::make('created_at')
                ->searchable()
                ->sortable()
                ->dateTime()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('songs_count')
                ->sortable()
                ->label('Songs')
                ->counts('songs'),
        ];
    }
}
