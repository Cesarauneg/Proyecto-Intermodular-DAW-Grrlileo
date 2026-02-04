<?php

namespace App\Http\Controllers;

use App\Models\GameScore;
use App\Models\Villager;
use App\Models\Hourly_Music; // NEW IMPORT
use Illuminate\Http\Request;
use Inertia\Inertia;

class MemoryGameController extends Controller
{
    /**
     * Display the Memory Game page.
     */
    public function index()
    {
        // Fetch a pool of villagers. The frontend will select a random subset based on level.
        // For simplicity, let's fetch all villagers for now and let the frontend handle the selection.
        $villagers = Villager::all(['id', 'name_es', 'icon', 'image', 'species', 'image_url']);
        $hourlyMusic = Hourly_Music::all(); // Fetch hourly music

        return Inertia::render('Games/MemoryGame', [
            'villagers' => $villagers,
            'hourlyMusic' => $hourlyMusic, // Pass hourly music
        ]);
    }

    /**
     * Save a new game score.
     */
    public function saveScore(Request $request)
    {
        $request->validate([
            'level' => ['required', 'integer', 'min:1', 'max:5'],
            'completion_time_seconds' => ['required', 'integer', 'min:0'],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        GameScore::create([
            'user_id' => $user->id,
            'level' => $request->level,
            'completion_time_seconds' => $request->completion_time_seconds,
        ]);

        return response()->json(['message' => 'Score saved successfully!'], 201);
    }
}