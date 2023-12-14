<?php

namespace App\Http\Requests;

use App\Models\Ranking;
use Illuminate\Foundation\Http\FormRequest;

class FinishRankingRequest extends FormRequest {
    public function authorize(): bool {
        return Ranking::findOrFail(request()->rankingId)->user_id === auth()->id();
    }

    public function rules(): array {
        return [
            'rankingId' => 'required',
            'songs' => 'required'
        ];
    }
}
