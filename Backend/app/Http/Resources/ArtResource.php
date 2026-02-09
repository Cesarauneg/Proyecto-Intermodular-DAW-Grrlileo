<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArtResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_es' => $this->name_es,
            'name_en' => $this->name_en,
            'buy_price' => $this->buy_price,
            'sell_price' => $this->sell_price,
            'has_fake' => $this->has_fake,
            'image' => $this->image,
        ];
    }
}
