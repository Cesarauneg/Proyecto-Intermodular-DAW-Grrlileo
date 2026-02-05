<?php

namespace App\Http\Controllers;

use App\Http\Resources\HourlyMusicResource;
use App\Models\Hourly_Music;
use App\Services\MusicService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HourlyMusicController extends Controller
{
    public function __construct(
        private readonly MusicService $musicService,
    ) {}

    public function index(): JsonResponse
    {
        $music = Cache::remember('hourly_music_all', 86400, fn() => Hourly_Music::all());

        return response()->json(HourlyMusicResource::collection($music));
    }

    public function getByHour(int $hour): JsonResponse
    {
        $hour = max(0, min(23, $hour));

        $songs = Hourly_Music::where('hour', $hour)
            ->orderBy('weather')
            ->get();

        return response()->json(HourlyMusicResource::collection($songs));
    }

    public function getCurrentHour(): JsonResponse
    {
        return response()->json([
            'hour' => (int) now()->format('G'),
            'timestamp' => now()->timestamp,
        ]);
    }
}
