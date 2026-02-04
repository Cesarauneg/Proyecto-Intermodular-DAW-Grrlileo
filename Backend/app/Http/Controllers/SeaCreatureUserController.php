<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class SeaCreatureUserController extends Controller
{
    // Donar criatura marina al museo
public function donate($seaCreatureId)
{
    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'No autenticado'], 401);
    }

    $user->seaCreatures()->toggle([$seaCreatureId => ['donated_to_museum' => true]]);

    return response()->json(['success' => true], 200);
}
    // Listar criaturas marinas del usuario
    public function index()
    {
        return response()->json(
            Auth::user()->seaCreatures()->get()
        );
    }
}
