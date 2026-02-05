<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class MuseumStatsService
{
    public const MAX_COUNTS = [
        'peces'     => 80,
        'bichos'    => 80,
        'arte'      => 43,
        'fosiles'   => 73,
        'criaturas' => 40,
    ];

    public function getStatsForUser(User $user): array
    {
        return Cache::remember("museum_stats_{$user->id}", 300, function () use ($user) {
            return [
                'peces'     => $user->fish()->wherePivot('donated_to_museum', true)->count(),
                'bichos'    => $user->bugs()->wherePivot('donated_to_museum', true)->count(),
                'arte'      => $user->art()->wherePivot('donated_to_museum', true)->count(),
                'fosiles'   => $user->fossils()->wherePivot('donated_to_museum', true)->count(),
                'criaturas' => $user->seaCreatures()->wherePivot('donated_to_museum', true)->count(),
            ];
        });
    }

    public function invalidateCache(int $userId): void
    {
        Cache::forget("museum_stats_{$userId}");
    }

    public function getMaxCounts(): array
    {
        return self::MAX_COUNTS;
    }
}
