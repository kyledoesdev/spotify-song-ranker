<?php

namespace App\Http\Requests\Rankings;

use App\Models\Ranking;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRankingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && Ranking::findOrFail(request()->id)->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:30|min:1',
            'is_public' => 'required|boolean'
        ];
    }
}
