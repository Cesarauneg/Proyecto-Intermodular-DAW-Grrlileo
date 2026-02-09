<?php

namespace App\Services;

use App\Models\GameScore;
use App\Models\Villager;

class MemoryGameService
{
    private const LEVEL_PAIRS = [
        1 => 9,
        2 => 12,
        3 => 16,
        4 => 20,
        5 => 25,
    ];

    public function buildGamePayload(int $level, int $userId): array
    {
        if (!array_key_exists($level, self::LEVEL_PAIRS)) {
            abort(404);
        }

        $pairs = self::LEVEL_PAIRS[$level];

        $villagers = Villager::query()
            ->inRandomOrder()
            ->limit($pairs)
            ->get(['id', 'name_es', 'file_name']);

        $cards = $villagers->flatMap(function ($villager) {
            $imagePath = "/images/villagers/{$villager->file_name}.png";

            return [
                ['pair_id' => $villager->id, 'name' => $villager->name_es, 'image' => $imagePath],
                ['pair_id' => $villager->id, 'name' => $villager->name_es, 'image' => $imagePath],
            ];
        })->shuffle()->values();

        $cards = $cards->map(fn($card, $index) => array_merge($card, ['id' => $index + 1]));

        $score = GameScore::query()
            ->where('user_id', $userId)
            ->where('level', $level)
            ->first();

        return [
            'level' => $level,
            'pairs' => $pairs,
            'cards' => $cards,
            'bestTime' => $score?->time_seconds,
            'bestMoves' => $score?->moves,
            'memorizeSeconds' => 3,
        ];
    }

    public function saveScore(int $userId, int $level, float $time, int $moves): array
    {
        $score = GameScore::query()
            ->where('user_id', $userId)
            ->where('level', $level)
            ->first();

        $updated = false;

        if (!$score) {
            $score = GameScore::query()->create([
                'user_id' => $userId,
                'level' => $level,
                'time_seconds' => $time,
                'moves' => $moves,
            ]);
            $updated = true;
        } else {
            $isTie = abs($time - (float) $score->time_seconds) < 0.0005;
            $shouldUpdate = $time < (float) $score->time_seconds
                || ($isTie && $moves < (int) $score->moves);

            if ($shouldUpdate) {
                $score->update([
                    'time_seconds' => $time,
                    'moves' => $moves,
                ]);
                $updated = true;
            }
        }

        return [
            'updated' => $updated,
            'best_time' => $score->time_seconds,
            'best_moves' => $score->moves,
        ];
    }
}
