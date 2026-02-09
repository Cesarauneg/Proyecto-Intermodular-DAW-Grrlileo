<?php
namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * IMPORTANTE: Si esto devuelve false, devuelve un error 403 Forbidden silencioso.
     */
    public function authorize(): bool
    {
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'bio'          => ['nullable', 'string', 'max:500'],
            'hemisphere'   => ['nullable', 'in:north,south'],
            'island_name'  => ['nullable', 'string', 'max:255'],
            'island_fruit' => ['nullable', 'string', 'in:apples,pears,oranges,cherries,peaches'],
            'avatar'       => ['nullable', 'string'],
        ];
    }
}