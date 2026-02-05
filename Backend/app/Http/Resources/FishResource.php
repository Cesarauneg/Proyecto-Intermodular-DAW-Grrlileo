<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FishResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_es' => $this->name_es,
            'name_en' => $this->name_en,
            'icon' => $this->icon,
            'price' => $this->price,
            'location' => $this->location,
            'rarity' => $this->rarity,
            'month_array_northern' => $this->month_array_northern,
            'month_array_southern' => $this->month_array_southern,
            'time_array' => $this->time_array,
            'is_all_day' => $this->is_all_day,
            'is_all_year' => $this->is_all_year,
        ];
    }
}
