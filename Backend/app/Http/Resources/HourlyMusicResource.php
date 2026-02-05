<?php

namespace App\Http\Resources;

use App\Services\MusicService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HourlyMusicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $musicService = app(MusicService::class);

        return [
            'id' => $this->id,
            'titulo' => $musicService->formatTitle($this->file_name, $this->weather),
            'weather' => $this->weather,
            'src' => asset($this->music_uri),
        ];
    }
}
