<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatalogFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'      => 'sometimes|string|max:255',
            'rarity'      => 'sometimes|string|max:50',
            'location'    => 'sometimes|string|max:100',
            'speed'       => 'sometimes|string|max:50',
            'shadow'      => 'sometimes|string|max:50',
            'has_fake'    => 'sometimes|boolean',
            'part_of'     => 'sometimes|string|max:100',
            'price_order' => 'sometimes|string|in:asc,desc',
            'hemisphere'  => 'sometimes|string|in:north,south',
            'per_page'    => 'sometimes|integer|min:1|max:100',
        ];
    }
}
