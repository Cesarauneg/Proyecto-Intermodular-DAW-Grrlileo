<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FossilResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_es' => $this->name_es,
            'name_en' => $this->name_en,
            'price' => $this->price,
            'part_of' => $this->part_of,
            'image' => $this->image,
        ];
    }
}
