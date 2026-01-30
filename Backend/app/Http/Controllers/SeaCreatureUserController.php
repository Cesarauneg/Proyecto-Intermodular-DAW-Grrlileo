<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class SeaCreatureUserController extends Controller
{
    // Donar criatura marina al museo
    public function donate($seaCreatureId)
    {
        $user = Auth::user();

        if (! $user->seaCreatures()->where('sea_creature_id', $seaCreatureId)->exists()) {
            $user->seaCreatures()->attach($seaCreatureId, [
                'donated_to_museum' => true,
            ]);
        } else {
            $user->seaCreatures()->updateExistingPivot($seaCreatureId, [
                'donated_to_museum' => true,
            ]);
        }

        return response()->json([
            'ok'      => true,
            'donated' => true,
        ]);
    }

    // Listar criaturas marinas del usuario
    public function index()
    {
        return response()->json(
            Auth::user()->seaCreatures()->get()
        );
    }
}
