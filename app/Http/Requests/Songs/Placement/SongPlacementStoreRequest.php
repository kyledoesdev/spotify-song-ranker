<?php

namespace App\Http\Requests\Songs\Placement;

use App\Models\Ranking;
use Illuminate\Foundation\Http\FormRequest;

class SongPlacementStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Ranking::findOrFail(request()->rankingId)->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'rankingId' => 'required',
            'songs' => 'required',
        ];
    }
}
