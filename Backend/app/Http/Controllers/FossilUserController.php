<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fossil;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class FossilUserController extends Controller
{
        // Añadir un fosil a un usuario (al donarlo al museo)
    public function donate($fossilId)
    {
        $user = Auth::user();

        // Si no lo tiene aún → lo añadimos como donado
        if (! $user->fossil()->where('fossil_id', $fossilId)->exists()) {
            $user->fossil()->attach($fossilId, [
                'donated' => true
            ]);
        } else {
            // Si ya lo tiene → solo actualizar pivot
            $user->fossil()->updateExistingPivot($fossilId, [
                'donated' => true
            ]);
        }

        return response()->json([
            'ok' => true,
            'donated' => true
        ]);
    }
    // Listar los fosiles de un usuario
    public function index()
    {
        $user = Auth::user();
        $fossils = $user->fossils()->get();   

        return response()->json($fossils);
    } 
}
