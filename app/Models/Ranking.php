<?php

namespace App\Models;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function isPublic(): bool
    {
        $user = auth()->check() ? auth()->user() : null;

        if (is_null($user) && $this->is_public) {
            return true;
        }

        return $this->is_public || ($user && $user->is_dev); //allow admin view of private rankings
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
            ->select('id', 'user_id', 'artist_id', 'name', 'is_ranked', 'created_at')
            ->where('is_ranked', false)
            ->with('artist')
            ->withCount('songs');
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
