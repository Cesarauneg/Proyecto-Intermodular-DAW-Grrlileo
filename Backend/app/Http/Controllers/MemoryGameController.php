<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameScoreRequest;
use App\Services\MemoryGameService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemoryGameController extends Controller
{
    public function __construct(
        private readonly MemoryGameService $gameService,
    ) {}

    public function show(Request $request, int $level)
    {
        return Inertia::render('Games/MemoryGame', $this->gameService->buildGamePayload($level, $request->user()->id));
    }

    public function data(Request $request, int $level)
    {
        return response()->json($this->gameService->buildGamePayload($level, $request->user()->id));
    }

    public function store(GameScoreRequest $request)
    {
        $data = $request->validated();

        $result = $this->gameService->saveScore(
            $request->user()->id,
            (int) $data['level'],
            (float) $data['time_seconds'],
            (int) $data['moves'],
        );

        return response()->json($result);
    }
}
