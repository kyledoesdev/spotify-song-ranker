<?php

namespace App\Http\Requests\Rankings;

use Illuminate\Foundation\Http\FormRequest;

class CreateRankingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'artist_id' => 'required|string',
            'artist_name' => 'required|string',
            'artist_img' => 'required|string',
            'name' => 'nullable|string|max:30|min:1',
            'songs' => 'required',
            'is_public' => 'required|boolean',
        ];
    }
}
