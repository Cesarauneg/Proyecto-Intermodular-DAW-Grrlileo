<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ArtUserController extends Controller
{
   // Añadir un arte a un usuario (al donarlo al museo)
    public function donate($artId)
    {
        $user = Auth::user();

        // Si no lo tiene aún → lo añadimos como donado
        if (! $user->art()->where('art_id', $artId)->exists()) {
            $user->art()->attach($artId, [
                'donated' => true
            ]);
        } else {
            // Si ya lo tiene → solo actualizar pivot
            $user->art()->updateExistingPivot($artId, [
                'donated' => true
            ]);
        }

        return response()->json([
            'ok' => true,
            'donated' => true
        ]);
    }
    // Listar las obras de arte de un usuario
    public function index()
    {
        $user = Auth::user();
        $art = $user->art()->get();   

        return response()->json($art);
    }   
}
