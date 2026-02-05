<?php

namespace App\Http\Controllers;

use App\Models\GameScore;
use App\Models\Villager;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemoryGameController extends Controller
{
    private const LEVEL_PAIRS = [
        1 => 9,
        2 => 12,
        3 => 16,
        4 => 20,
        5 => 25,
    ];

    private function buildGamePayload(int $level): array
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
                [
                    'pair_id' => $villager->id,
                    'name' => $villager->name_es,
                    'image' => $imagePath,
                ],
                [
                    'pair_id' => $villager->id,
                    'name' => $villager->name_es,
                    'image' => $imagePath,
                ],
            ];
        })->shuffle()->values();

        $cards = $cards->map(function ($card, $index) {
            return array_merge($card, ['id' => $index + 1]);
        });

        $bestTime = GameScore::query()
            ->where('user_id', request()->user()->id)
            ->where('level', $level)
            ->value('time_seconds');

        $bestMoves = GameScore::query()
            ->where('user_id', request()->user()->id)
            ->where('level', $level)
            ->value('moves');

        return [
            'level' => $level,
            'pairs' => $pairs,
            'cards' => $cards,
            'bestTime' => $bestTime,
            'bestMoves' => $bestMoves,
            'memorizeSeconds' => 3,
        ];
    }

    public function show(int $level)
    {
        return Inertia::render('Games/MemoryGame', $this->buildGamePayload($level));
    }

    public function data(int $level)
    {
        return response()->json($this->buildGamePayload($level));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'level' => ['required', 'integer', 'between:1,5'],
            'time_seconds' => ['required', 'numeric', 'min:0.001'],
            'moves' => ['required', 'integer', 'min:1'],
        ]);

        $userId = $request->user()->id;
        $level = (int) $data['level'];
        $timeSeconds = (float) $data['time_seconds'];
        $moves = (int) $data['moves'];

        $score = GameScore::query()
            ->where('user_id', $userId)
            ->where('level', $level)
            ->first();

        $updated = false;

        if (!$score) {
            GameScore::query()->create([
                'user_id' => $userId,
                'level' => $level,
                'time_seconds' => $timeSeconds,
                'moves' => $moves,
            ]);
            $updated = true;
        } else {
            $currentBest = (float) $score->time_seconds;
            $currentMoves = (int) $score->moves;
            $isTie = abs($timeSeconds - $currentBest) < 0.0005;
            $shouldUpdate = $timeSeconds < $currentBest
                || ($isTie && $moves < $currentMoves);

            if ($shouldUpdate) {
                $score->update([
                    'time_seconds' => $timeSeconds,
                    'moves' => $moves,
                ]);
                $updated = true;
            }
        }

        $bestTime = GameScore::query()
            ->where('user_id', $userId)
            ->where('level', $level)
            ->value('time_seconds');

        $bestMoves = GameScore::query()
            ->where('user_id', $userId)
            ->where('level', $level)
            ->value('moves');

        return response()->json([
            'updated' => $updated,
            'best_time' => $bestTime,
            'best_moves' => $bestMoves,
        ]);
    }
}
