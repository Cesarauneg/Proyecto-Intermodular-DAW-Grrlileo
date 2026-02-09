<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameScoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'level' => $this->level,
            'time_seconds' => $this->time_seconds,
            'moves' => $this->moves,
        ];
    }
}
