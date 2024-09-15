<?php

namespace App\Http\Requests\Rankings;

use App\Models\Ranking;
use Illuminate\Foundation\Http\FormRequest;

class DeleteRankingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && Ranking::findOrFail(request()->rankingId)->user_id == auth()->id();
    }

    public function rules(): array
    {
        return [
            'rankingId' => 'required',
        ];
    }
}
