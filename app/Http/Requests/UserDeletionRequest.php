<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserDeletionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (int) request()->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required'
        ];
    }
}
