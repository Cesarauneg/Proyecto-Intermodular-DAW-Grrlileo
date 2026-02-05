<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VillagerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_es' => $this->name_es,
            'name_en' => $this->name_en,
            'file_name' => $this->file_name,
            'personality' => $this->personality,
            'species' => $this->species,
            'gender' => $this->gender,
            'hobby' => $this->hobby,
            'birthday' => $this->birthday,
        ];
    }
}
