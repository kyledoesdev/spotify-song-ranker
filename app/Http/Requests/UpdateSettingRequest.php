<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->id() == request()->user_id;
    }

    public function rules(): array
    {
        return [
            'setting_name' => 'required|string',
            'setting_value' => 'required'
        ];
    }
}
