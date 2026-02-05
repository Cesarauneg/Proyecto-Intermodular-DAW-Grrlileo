<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GameScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'level'        => 'required|integer|between:1,5',
            'time_seconds' => 'required|numeric|min:0.001',
            'moves'        => 'required|integer|min:1',
        ];
    }
}
